<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ProofPhoto;
use App\Models\Subject;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    private function authorizeAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }
    }

    // Ringkasan / statistik
    public function index()
    {
        $this->authorizeAdmin();

        $userCount = User::count();
        $roleCounts = User::select('role', DB::raw('count(*) as total'))->groupBy('role')->pluck('total', 'role');
        $classCounts = User::select('class_name', DB::raw('count(*) as total'))->whereNotNull('class_name')->groupBy('class_name')->pluck('total', 'class_name');

        $taskCount = Task::count();
        $taskActive = Task::where('deadline', '>=', now())->count();
        $taskExpired = Task::where('deadline', '<', now())->count();

        $pending = DB::table('task_user')->where('status', 'pending')->count();
        $confirmed = DB::table('task_user')->where('status', 'confirmed')->count();
        $workedTaskCount = (int) DB::table('task_user')->selectRaw('COUNT(DISTINCT task_id) as c')->value('c');
        $unworked = max(0, $taskCount - $workedTaskCount);

        $taskByClass = Task::select('class_name', DB::raw('count(*) as total'))->groupBy('class_name')->orderBy('class_name')->pluck('total', 'class_name');
        $subjectByClass = Task::select('subject', 'class_name', DB::raw('count(*) as total'))
            ->groupBy('subject', 'class_name')->orderBy('class_name')->orderByRaw('count(*) desc')->get();

        $subjectNames = $subjectByClass->pluck('subject')->unique()->sort()->values();

        return view('admin.index', compact(
            'userCount', 'roleCounts', 'classCounts',
            'taskCount', 'taskActive', 'taskExpired',
            'pending', 'confirmed', 'unworked',
            'taskByClass', 'subjectByClass', 'subjectNames'
        ));
    }

    // Daftar semua tugas
    public function tasks(Request $request)
    {
        $this->authorizeAdmin();

        $query = Task::query()->with('user');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }
        if ($request->filled('class_name')) {
            $query->where('class_name', $request->class_name);
        }
        if ($request->filled('subject')) {
            $query->where('subject', $request->subject);
        }
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        $tasks = $query->orderBy('deadline', 'asc')->paginate(20)->withQueryString();
        $subjects = Subject::orderBy('name')->pluck('name');

        return view('admin.tasks', [
            'tasks' => $tasks,
            'subjects' => $subjects,
            'classOptions' => User::CLASS_OPTIONS,
            'academicYears' => AcademicYear::orderByDesc('name')->get(),
        ]);
    }

    public function storeTask(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject' => ['required', Rule::exists('subjects', 'name')],
            'deadline' => 'required|date',
            'description' => 'nullable|string',
            'class_name' => ['required', Rule::in(User::CLASS_OPTIONS), $this->classMustMatchYear($request)],
            'academic_year_id' => ['nullable', Rule::exists('academic_years', 'id')],
        ]);

        // Tahun ajaran tugas selalu mengikuti angkatan kelasnya
        $validated['academic_year_id'] = User::yearIdForClass($validated['class_name']);

        Task::create([
            'user_id' => auth()->id(),
            ...$validated,
        ]);

        return redirect()->route('admin.tasks')->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function updateTask(Request $request, Task $task)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject' => ['required', Rule::exists('subjects', 'name')],
            'deadline' => 'required|date',
            'description' => 'nullable|string',
            'class_name' => ['nullable', Rule::in(User::CLASS_OPTIONS), $this->classMustMatchYear($request)],
            'academic_year_id' => ['nullable', Rule::exists('academic_years', 'id')],
        ]);

        if (empty($validated['class_name'])) {
            unset($validated['class_name']);
        } else {
            $validated['academic_year_id'] = User::yearIdForClass($validated['class_name']);
        }

        $task->update($validated);

        return back()->with('success', 'Tugas berhasil diperbarui.');
    }

    // Kelas yang dikirim harus termasuk tahun ajaran yang dipilih (jika ada)
    private function classMustMatchYear(Request $request): \Closure
    {
        return function ($attribute, $value, $fail) use ($request) {
            $yearId = $request->input('academic_year_id');
            if ($yearId && ! in_array($value, User::classesForYearId((int) $yearId), true)) {
                $fail('Kelas tidak termasuk tahun ajaran yang dipilih.');
            }
        };
    }

    public function destroyTask(Task $task)
    {
        $this->authorizeAdmin();

        foreach ($task->proofPhotos as $photo) {
            Storage::disk('public')->delete($photo->photo_path);
        }

        $task->delete();

        return back()->with('success', 'Tugas berhasil dihapus.');
    }

    // Verifikasi & bukti foto
    public function verifications(Request $request)
    {
        $this->authorizeAdmin();

        $status = $request->input('status', 'pending');

        $rows = DB::table('task_user')
            ->join('tasks', 'tasks.id', '=', 'task_user.task_id')
            ->join('users', 'users.id', '=', 'task_user.user_id')
            ->leftJoin('users as confirmers', 'confirmers.id', '=', 'task_user.confirmed_by')
            ->select(
                'task_user.*',
                'tasks.title as task_title',
                'tasks.subject',
                'tasks.class_name',
                'tasks.deadline',
                'users.name as user_name',
                'users.username as user_username',
                'confirmers.name as confirmer_name'
            )
            ->orderBy('task_user.created_at', $status === 'confirmed' ? 'desc' : 'asc');

        if ($request->filled('class_name')) {
            $rows->where('tasks.class_name', $request->class_name);
        }

        if ($status === 'pending') {
            $rows->where('task_user.status', 'pending');
        } elseif ($status === 'confirmed') {
            $rows->where('task_user.status', 'confirmed');
        } else {
            $rows->whereIn('task_user.status', ['pending', 'confirmed']);
        }

        $items = $rows->paginate(20)->withQueryString();

        $photosByTask = [];
        if ($items->isNotEmpty()) {
            $taskIds = $items->unique('task_id')->pluck('task_id');
            foreach (ProofPhoto::whereIn('task_id', $taskIds)->get()->groupBy('task_id') as $tid => $photos) {
                foreach ($photos as $p) {
                    $photosByTask[$p->task_id][$p->user_id][] = $p;
                }
            }
        }

        return view('admin.verifications', [
            'items' => $items,
            'status' => $status,
            'photosByTask' => $photosByTask,
            'classOptions' => User::CLASS_OPTIONS,
        ]);
    }

    public function confirmTask(Task $task, Request $request)
    {
        $this->authorizeAdmin();

        $userId = $request->input('user_id');
        $updated = DB::table('task_user')
            ->where('task_id', $task->id)
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->where('status', 'pending')
            ->update(['status' => 'confirmed', 'confirmed_by' => auth()->id()]);

        return back()->with('success', $updated ? 'Tugas dikonfirmasi.' : 'Tidak ada tugas pending untuk dikonfirmasi.');
    }

    public function unconfirmTask(Task $task, Request $request)
    {
        $this->authorizeAdmin();

        $userId = $request->input('user_id');
        $updated = DB::table('task_user')
            ->where('task_id', $task->id)
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->where('status', 'confirmed')
            ->update(['status' => 'pending', 'confirmed_by' => null]);

        return back()->with('success', $updated ? 'Verifikasi dibatalkan (kembali pending).' : 'Tidak ada tugas terverifikasi untuk dibatalkan.');
    }

    // Mata Pelajaran (dropdown CRUD)
    public function subjects()
    {
        $this->authorizeAdmin();

        $subjects = Subject::orderBy('name')->paginate(30);

        return view('admin.subjects', [
            'subjects' => $subjects,
            'usedCount' => DB::table('tasks')->count() + DB::table('users')->whereNotNull('pj_subject')->count(),
        ]);
    }

    public function storeSubject(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('subjects', 'name')],
        ]);

        Subject::create($validated);

        return back()->with('success', 'Mapel ditambahkan.');
    }

    public function updateSubject(Request $request, Subject $subject)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('subjects', 'name')->ignore($subject->id)],
        ]);

        Subject::where('id', $subject->id)->update(['name' => $validated['name'], 'updated_at' => now()]);

        return back()->with('success', 'Mapel diperbarui.');
    }

    public function destroySubject(Subject $subject)
    {
        $this->authorizeAdmin();

        $subject->delete();

        return back()->with('success', 'Mapel dihapus.');
    }

    // Tahun Ajaran CRUD
    public function academicYears()
    {
        $this->authorizeAdmin();

        $academicYears = AcademicYear::withCount('pjAssignments')->orderByDesc('name')->paginate(30);

        return view('admin.academic-years', [
            'academicYears' => $academicYears,
        ]);
    }

    public function storeAcademicYear(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:20', Rule::unique('academic_years', 'name')],
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($validated['is_active']) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        AcademicYear::create($validated);

        return back()->with('success', 'Tahun ajaran ditambahkan.');
    }

    public function updateAcademicYear(Request $request, AcademicYear $academicYear)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:20', Rule::unique('academic_years', 'name')->ignore($academicYear->id)],
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($validated['is_active']) {
            AcademicYear::where('is_active', true)->where('id', '!=', $academicYear->id)->update(['is_active' => false]);
        }

        $academicYear->update($validated);

        return back()->with('success', 'Tahun ajaran diperbarui.');
    }

    public function destroyAcademicYear(AcademicYear $academicYear)
    {
        $this->authorizeAdmin();

        $academicYear->delete();

        return back()->with('success', 'Tahun ajaran dihapus.');
    }
}
