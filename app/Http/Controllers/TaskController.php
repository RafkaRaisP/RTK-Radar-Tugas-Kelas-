<?php

namespace App\Http\Controllers;

use App\Models\ProofPhoto;
use App\Models\Subject;
use App\Models\Task;
use App\Models\User;
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
        $classOptions = User::CLASS_OPTIONS;

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

        Task::create([
            'user_id' => Auth::id(),
            'class_name' => $user->isPjMapel() ? $user->class_name : ($user->role === 'admin' ? $validated['class_name'] : $user->class_name),
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

    // Tandai tugas sebagai selesai / batal selesai (toggle)
    public function toggleComplete(Task $task)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && strtolower($task->class_name) !== strtolower($user->class_name)) {
            abort(403);
        }

        $existing = $user->completedTasks()->where('task_id', $task->id)->first();

        if ($existing) {
            $user->completedTasks()->detach($task);
        } else {
            $user->completedTasks()->attach($task, ['created_at' => now(), 'status' => 'pending']);
        }

        return redirect()->route('dashboard');
    }

    // Siswa menandai tugas selesai (submit ke verifikasi PJ)
    public function markAsDone(Request $request, Task $task)
    {
        $user = Auth::user();
        if (strtolower($task->class_name) !== strtolower($user->class_name)) {
            abort(403);
        }

        $validated = $request->validate([
            'photos' => 'required|array|min:1',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $existing = $user->completedTasks()->where('task_id', $task->id)->first();

        if ($existing) {
            if ($existing->pivot->status === 'pending') {
                return redirect()->route('dashboard')->with('info', 'Tugas sudah dalam antrian verifikasi.');
            }
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

        // Simpan foto bukti
        foreach ($request->file('photos') as $photo) {
            $path = $photo->store('proof-photos/'.$task->id.'/'.$user->id, 'public');
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

    // PJ Mapel: konfirmasi tugas siswa
    public function confirmTask(Task $task)
    {
        $user = Auth::user();

        if (! $user->isPjMapel()) {
            abort(403);
        }

        $pivotRow = DB::table('task_user')
            ->where('task_id', $task->id)
            ->where('status', 'pending')
            ->first();

        if (! $pivotRow) {
            return redirect()->route('pj.dashboard')->with('error', 'Tidak ada tugas pending untuk dikonfirmasi.');
        }

        DB::table('task_user')
            ->where('task_id', $task->id)
            ->where('user_id', $pivotRow->user_id)
            ->update([
                'status' => 'confirmed',
                'confirmed_by' => $user->id,
            ]);

        return redirect()->route('pj.dashboard')->with('success', 'Tugas berhasil dikonfirmasi.');
    }

    // Export tugas ke CSV
    public function exportCsv(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $tasks = Task::orderBy('deadline', 'asc')->get();
        } else {
            $tasks = Task::where('class_name', $user->class_name)->orderBy('deadline', 'asc')->get();
        }

        $filename = 'tugas-'.now()->format('Y-m-d').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $tz = $request->query('tz');
        if ($tz) {
            try {
                new \DateTimeZone($tz);
            } catch (\Exception $e) {
                $tz = null;
            }
        }

        $callback = function () use ($tasks, $tz) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Judul', 'Mapel', 'Deskripsi', 'Kelas', 'Deadline', 'Status']);

            foreach ($tasks as $task) {
                $deadline = $task->deadline;
                if ($tz) {
                    $deadline = $deadline->setTimezone($tz);
                }
                fputcsv($file, [
                    $task->title,
                    $task->subject,
                    $task->description ?? '',
                    $task->class_name,
                    $deadline->format('d/m/Y H:i'),
                    now()->gt($task->deadline) ? 'Terlewat' : 'Aktif',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
