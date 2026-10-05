<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\PjAssignment;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminPjController extends Controller
{
    private function authorizeAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $query = PjAssignment::with(['user', 'subject', 'academicYear']);

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('class_name')) {
            $query->where('class_name', $request->class_name);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%$s%")->orWhere('username', 'like', "%$s%"));
        }

        $assignments = $query->orderBy('class_name')->paginate(20)->withQueryString();

        return view('admin.pj', [
            'assignments' => $assignments,
            'academicYears' => AcademicYear::orderByDesc('is_active')->orderByDesc('name')->get(),
            'subjects' => Subject::orderBy('name')->get(),
            'classOptions' => User::CLASS_OPTIONS,
            'usersJson' => User::whereIn('role', ['siswa', 'ketua_kelas', 'sekretaris', 'pj_mapel'])
                ->orderBy('class_name')->orderBy('name')
                ->get()
                ->map(fn ($u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                    'class_name' => $u->class_name,
                    'academic_year_id' => $u->academic_year_id,
                ])
                ->values()
                ->toJson(),
        ]);
    }

    // Pastikan siswa terpilih benar-benar terdaftar di kelas + tahun ajaran yang dikirim
    private function cascadeError(array $validated): ?string
    {
        $user = User::find($validated['user_id']);

        if (!$user
            || (string) $user->academic_year_id !== (string) $validated['academic_year_id']
            || $user->class_name !== $validated['class_name']) {
            return 'Siswa terpilih tidak terdaftar di kelas / tahun ajaran tersebut.';
        }

        return null;
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'user_id' => ['required', Rule::exists('users', 'id')],
            'subject_id' => ['required', Rule::exists('subjects', 'id')],
            'academic_year_id' => ['required', Rule::exists('academic_years', 'id')],
            'class_name' => ['required', Rule::in(User::CLASS_OPTIONS)],
        ]);

        if ($err = $this->cascadeError($validated)) {
            return back()->withErrors(['user_id' => $err]);
        }

        $exists = PjAssignment::where($validated)->exists();
        if ($exists) {
            return back()->with('error', 'PJ assignment untuk user, mapel, tahun ajaran, dan kelas ini sudah ada.');
        }

        PjAssignment::create($validated);

        // Update user role to pj_mapel if not already
        $user = User::find($validated['user_id']);
        if ($user->role !== 'pj_mapel') {
            $user->update(['role' => 'pj_mapel']);
        }

        return back()->with('success', 'PJ berhasil ditambahkan.');
    }

    public function update(Request $request, PjAssignment $pj)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'user_id' => ['required', Rule::exists('users', 'id')],
            'subject_id' => ['required', Rule::exists('subjects', 'id')],
            'academic_year_id' => ['required', Rule::exists('academic_years', 'id')],
            'class_name' => ['required', Rule::in(User::CLASS_OPTIONS)],
        ]);

        if ($err = $this->cascadeError($validated)) {
            return back()->withErrors(['user_id' => $err]);
        }

        $duplicate = PjAssignment::where($validated)->where('id', '!=', $pj->id)->exists();
        if ($duplicate) {
            return back()->with('error', 'PJ assignment untuk user, mapel, tahun ajaran, dan kelas ini sudah ada.');
        }

        $pj->update($validated);

        return back()->with('success', 'PJ assignment diperbarui.');
    }

    public function destroy(PjAssignment $pj)
    {
        $this->authorizeAdmin();

        $pj->delete();

        return back()->with('success', 'PJ assignment dihapus.');
    }
}
