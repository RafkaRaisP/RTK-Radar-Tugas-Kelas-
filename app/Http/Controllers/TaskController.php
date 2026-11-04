<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ProofPhoto;
use App\Models\Subject;
use App\Models\Task;
use App\Models\User;
use App\Support\PhotoCompressor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    // Halaman publik (radar) — semua orang bisa lihat, tanpa login
    public function publicIndex(Request $request)
    {
        $query = Task::query()->orderBy('deadline', 'asc');

        // Filter berdasarkan kelas (kalau ada)
        if ($request->filled('class_name')) {
            $query->whereRaw('LOWER(class_name) = ?', [strtolower($request->class_name)]);
        }

        $tasks = $query->paginate(50);
        $classOptions = User::CLASS_OPTIONS;

        return view('public.radar', compact('tasks', 'classOptions'));
    }

    // Dashboard user yang sudah login — lihat tugas kelasnya sendiri
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $query = Task::query();
            if ($request->filled('class_name')) {
                $query->where('class_name', $request->class_name);
            }
        } else {
            $query = Task::where('class_name', $user->class_name);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }
        if ($request->filled('subject')) {
            $query->where('subject', $request->subject);
        }

        $tasks = $query->orderBy('deadline', 'asc')->paginate(50);

        $taskStatuses = [];
        foreach ($user->completedTasks()->select('task_id', 'task_user.status')->get() as $ct) {
            $taskStatuses[$ct->task_id] = $ct->pivot->status;
        }

        $subjects = Subject::orderBy('name')->pluck('name');
        $classOptions = User::classOptionsFor($user);

        return view('dashboard.agenda', compact('tasks', 'taskStatuses', 'subjects', 'classOptions'));
    }

    // Tambah tugas baru (hanya pengurus kelas & admin)
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'siswa') {
            abort(403);
        }

        $rules = [
            'title' => 'required|string|max:255',
            'deadline' => 'required|date',
            'description' => 'nullable|string',
        ];

        if ($user->role === 'admin') {
            $rules['subject'] = ['required', Rule::exists('subjects', 'name')];
            $rules['class_name'] = ['required', Rule::in(User::CLASS_OPTIONS)];
        } elseif ($user->isPjMapel()) {
            // PJ Mapel: subject otomatis dari mapelnya, tidak perlu input
        } else {
            $rules['subject'] = ['required', Rule::exists('subjects', 'name')];
        }

        $validated = $request->validate($rules);

        $class = $user->isPjMapel() ? $user->class_name : ($user->role === 'admin' ? $validated['class_name'] : $user->class_name);

        Task::create([
            'user_id' => Auth::id(),
            'class_name' => $class,
            'academic_year_id' => User::yearIdForClass($class),
            'subject' => $user->isPjMapel() ? $user->pj_subject : $validated['subject'],
            'title' => $validated['title'],
            'deadline' => $validated['deadline'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('dashboard')->with('success', 'Tugas berhasil ditambahkan.');
    }

    // Edit tugas yang sudah ada
    public function update(Request $request, Task $task)
    {
        $user = Auth::user();
        if ($user->role === 'siswa' || ($user->role !== 'admin' && strtolower($task->class_name) !== strtolower($user->class_name))) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject' => ['required', Rule::exists('subjects', 'name')],
            'deadline' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $task->update($validated);

        return redirect()->route('dashboard')->with('success', 'Tugas berhasil diperbarui.');
    }

    // Hapus tugas
    public function destroy(Task $task)
    {
        $user = Auth::user();
        if ($user->role === 'siswa' || ($user->role !== 'admin' && strtolower($task->class_name) !== strtolower($user->class_name))) {
            abort(403);
        }

        $task->delete();

        return redirect()->route('dashboard')->with('success', 'Tugas berhasil dihapus.');
    }

    // Siswa membatalkan pengajuan tugas (hapus pivot + foto bukti)
    public function toggleComplete(Task $task)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && strtolower($task->class_name) !== strtolower($user->class_name)) {
            abort(403);
        }

        $existing = $user->completedTasks()->where('task_id', $task->id)->first();

        if (! $existing) {
            return redirect()->route('dashboard')->with('info', 'Tidak ada pengajuan yang bisa dibatalkan.');
        }

        // Hapus foto bukti beserta filenya supaya tidak jadi data orfan
        $oldPhotos = ProofPhoto::where('task_id', $task->id)->where('user_id', $user->id)->get();
        foreach ($oldPhotos as $photo) {
            Storage::disk('public')->delete($photo->photo_path);
            $photo->delete();
        }

        $user->completedTasks()->detach($task);

        return redirect()->route('dashboard')->with('success', 'Pengajuan tugas dibatalkan.');
    }

    // Siswa menandai tugas selesai (submit ke verifikasi PJ)
    public function markAsDone(Request $request, Task $task)
    {
        $user = Auth::user();
        if (strtolower($task->class_name) !== strtolower($user->class_name)) {
            abort(403);
        }

        // Tidak ada batas ukuran — foto dikompres otomatis maks 2MB setelah upload
        $validated = $request->validate([
            'photos' => 'required|array|min:1|max:5',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp',
        ], [
            'photos.*.image' => 'File harus berupa foto (JPG, PNG, atau WEBP). Format HEIC belum didukung — foto ke JPG dulu.',
            'photos.*.mimes' => 'File harus berupa foto (JPG, PNG, atau WEBP). Format HEIC belum didukung — foto ke JPG dulu.',
        ]);

        $existing = $user->completedTasks()->where('task_id', $task->id)->first();
        $hasPhotos = ProofPhoto::where('task_id', $task->id)->where('user_id', $user->id)->exists();

        if ($existing && $existing->pivot->status === 'pending' && $hasPhotos) {
            return redirect()->route('dashboard')->with('info', 'Tugas sudah dalam antrian verifikasi.');
        }

        if ($existing) {
            // Hapus foto lama sebelum upload baru
            $oldPhotos = ProofPhoto::where('task_id', $task->id)->where('user_id', $user->id)->get();
            foreach ($oldPhotos as $photo) {
                Storage::disk('public')->delete($photo->photo_path);
                $photo->delete();
            }
            $user->completedTasks()->updateExistingPivot($task->id, ['status' => 'pending', 'confirmed_by' => null]);
        } else {
            $user->completedTasks()->attach($task, ['created_at' => now(), 'status' => 'pending']);
        }

        // Simpan foto bukti (dikompres maks 2MB)
        foreach ($request->file('photos') as $photo) {
            $path = PhotoCompressor::compressToPublic($photo, 'proof-photos/'.$task->id.'/'.$user->id);

            if ($path === null) {
                return redirect()->route('dashboard')->with('error', 'Gagal memproses foto "'.$photo->getClientOriginalName().'". Coba foto lain.');
            }

            ProofPhoto::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'photo_path' => $path,
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Tugas ditandai selesai. Menunggu verifikasi PJ Mapel.');
    }

    // Dashboard PJ Mapel: lihat tugas pending per mapel
    public function pjDashboard(Request $request)
    {
        $user = Auth::user();

        if (! $user->isPjMapel()) {
            abort(403);
        }

        $tasks = Task::where('class_name', $user->class_name)
            ->where('subject', $user->pj_subject)
            ->whereHas('completedBy', fn ($q) => $q->where('task_user.status', 'pending'))
            ->with([
                'completedBy' => fn ($q) => $q->where('task_user.status', 'pending'),
                'proofPhotos',
            ])
            ->orderBy('deadline', 'asc')
            ->paginate(50);

        $confirmedTasks = Task::where('class_name', $user->class_name)
            ->where('subject', $user->pj_subject)
            ->whereHas('completedBy', fn ($q) => $q->where('task_user.status', 'confirmed'))
            ->with([
                'completedBy' => fn ($q) => $q->where('task_user.status', 'confirmed'),
                'proofPhotos',
            ])
            ->orderBy('deadline', 'desc')
            ->paginate(20, ['*'], 'confirmed_page');

        return view('pj.dashboard', compact('tasks', 'confirmedTasks'));
    }

    // PJ Mapel: konfirmasi tugas siswa tertentu
    public function confirmTask(Request $request, Task $task)
    {
        $user = Auth::user();

        if (! $user->isPjMapel()) {
            abort(403);
        }

        // Hanya boleh konfirmasi tugas di kelas & mapel miliknya
        if (strtolower($task->class_name) !== strtolower($user->class_name)
            || ! $user->isPjOf($task->subject)) {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => 'required|integer',
        ]);

        $updated = DB::table('task_user')
            ->where('task_id', $task->id)
            ->where('user_id', $validated['user_id'])
            ->where('status', 'pending')
            ->update([
                'status' => 'confirmed',
                'confirmed_by' => $user->id,
            ]);

        if (! $updated) {
            return redirect()->route('pj.dashboard')->with('error', 'Tidak ada tugas pending untuk dikonfirmasi.');
        }

        return redirect()->route('pj.dashboard')->with('success', 'Tugas berhasil dikonfirmasi.');
    }

    // Export tugas ke PDF (bertema web)
    public function exportPdf()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $tasks = Task::orderBy('deadline', 'asc')->get();
            $scopeLabel = 'Semua Kelas';
        } else {
            $tasks = Task::where('class_name', $user->class_name)->orderBy('deadline', 'asc')->get();
            $scopeLabel = $user->class_name;
        }

        $pdf = Pdf::loadView('exports.tasks-pdf', [
            'tasks' => $tasks,
            'user' => $user,
            'scopeLabel' => $scopeLabel,
            'year' => AcademicYear::where('is_active', true)->first()
                ?? AcademicYear::orderByDesc('name')->first(),
            'printedAt' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('tugas-'.now()->format('Y-m-d').'.pdf');
    }
}
