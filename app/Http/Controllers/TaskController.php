<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $completedIds = $user->completedTasks->pluck('id')->toArray();

        $subjects = Task::select('subject')->distinct()->orderBy('subject')->pluck('subject');
        $classOptions = User::CLASS_OPTIONS;

        return view('dashboard', compact('tasks', 'completedIds', 'subjects', 'classOptions'));
    }

    // Tambah tugas baru (hanya pengurus kelas & admin)
    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'deadline' => 'required|date|after_or_equal:now',
            'description' => 'nullable|string',
        ];

        if (Auth::user()->role === 'admin') {
            $rules['class_name'] = ['required', Rule::in(User::CLASS_OPTIONS)];
        }

        $validated = $request->validate($rules);

        Task::create([
            'user_id' => Auth::id(),
            'class_name' => Auth::user()->role === 'admin' ? $validated['class_name'] : Auth::user()->class_name,
            ...$validated,
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
            'subject' => 'required|string|max:255',
            'deadline' => 'required|date|after_or_equal:now',
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

        if ($user->completedTasks()->where('task_id', $task->id)->exists()) {
            $user->completedTasks()->detach($task);
        } else {
            $user->completedTasks()->attach($task, ['created_at' => now()]);
        }

        return redirect()->route('dashboard');
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
