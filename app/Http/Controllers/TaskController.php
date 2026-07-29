<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $tasks = $query->get();
        $classOptions = User::CLASS_OPTIONS;

        return view('public.radar', compact('tasks', 'classOptions'));
    }

    // Dashboard user yang sudah login — lihat tugas kelasnya sendiri
    public function index()
    {
        $user = Auth::user();
        $completedIds = $user->completedTasks->pluck('id')->toArray();

        $all = Task::orderBy('deadline', 'asc')->get();
        $completed = $all->whereIn('id', $completedIds)->sortByDesc(function ($t) use ($user) {
            return $t->completedBy->where('id', $user->id)->first()?->pivot->created_at ?? 0;
        });
        $pending = $all->whereNotIn('id', $completedIds);

        // Urutan: tugas belum selesai dulu, baru yang sudah selesai
        $tasks = $pending->concat($completed);

        return view('dashboard', compact('tasks', 'completedIds'));
    }

    // Tambah tugas baru (hanya pengurus kelas)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'deadline' => 'required|date',
            'description' => 'nullable|string',
        ]);

        Task::create([
            'user_id' => Auth::id(),
            'class_name' => Auth::user()->class_name,
            ...$validated,
        ]);

        return redirect()->route('dashboard')->with('success', 'Tugas berhasil ditambahkan.');
    }

    // Edit tugas yang sudah ada
    public function update(Request $request, Task $task)
    {
        // Cegah user dari kelas lain mengedit tugas ini
        if (strtolower($task->class_name) !== strtolower(Auth::user()->class_name)) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'deadline' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $task->update($validated);

        return redirect()->route('dashboard')->with('success', 'Tugas berhasil diperbarui.');
    }

    // Hapus tugas
    public function destroy(Task $task)
    {
        if (strtolower($task->class_name) !== strtolower(Auth::user()->class_name)) {
            abort(403);
        }

        $task->delete();

        return redirect()->route('dashboard')->with('success', 'Tugas berhasil dihapus.');
    }

    // Tandai tugas sebagai selesai / batal selesai (toggle)
    public function toggleComplete(Task $task)
    {
        $user = Auth::user();

        if ($user->completedTasks()->where('task_id', $task->id)->exists()) {
            $user->completedTasks()->detach($task);
        } else {
            $user->completedTasks()->attach($task, ['created_at' => now()]);
        }

        return redirect()->route('dashboard');
    }
}
