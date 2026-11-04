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

        if (! $user
            || (string) $user->academic_year_id !== (string) $validated['academic_year_id']
            || $user->class_name !== $validated['class_name']) {
            return 'Siswa terpilih tidak terdaftar di kelas / tahun ajaran tersebut.';
        }

        return null;
    }

    // Kelas harus termasuk tahun ajaran yang dipilih
    private function classMustMatchYear(Request $request): \Closure
    {
        return function ($attribute, $value, $fail) use ($request) {
            $yearId = $request->input('academic_year_id');
            if ($yearId && ! in_array($value, User::classesForYearId((int) $yearId), true)) {
                $fail('Kelas tidak termasuk tahun ajaran yang dipilih.');
            }
        };
    }

    // Aturan bisnis: 1 kelas + 1 mapel + 1 tahun ajaran = 1 PJ;
    // 1 orang hanya boleh menjadi PJ satu mapel.
    private function conflictError(array $validated, ?int $ignoreId = null): ?string
    {
        $subjectName = Subject::find($validated['subject_id'])?->name ?? 'mapel ini';

        $combo = PjAssignment::with('user')
            ->where('academic_year_id', $validated['academic_year_id'])
            ->where('class_name', $validated['class_name'])
            ->where('subject_id', $validated['subject_id'])
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->first();

        if ($combo && $combo->user_id != $validated['user_id']) {
            return sprintf(
                'Kelas %s mapel %s sudah memiliki PJ: %s (%s). Hapus atau edit PJ tersebut terlebih dahulu.',
                $validated['class_name'],
                $subjectName,
                $combo->user->name ?? 'Tidak diketahui',
                $combo->user->username ?? '-',
            );
        }

        $other = PjAssignment::with(['user', 'subject', 'academicYear'])
            ->where('user_id', $validated['user_id'])
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->first();

        if ($other) {
            return sprintf(
                '%s (%s) sudah menjadi PJ mapel %s di kelas %s (tahun ajaran %s). Satu orang hanya boleh menjadi PJ satu mapel.',
                $other->user->name ?? 'User tersebut',
                $other->user->username ?? '-',
                $other->subject->name ?? '-',
                $other->class_name,
                $other->academicYear->name ?? '-',
            );
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
            'class_name' => ['required', 'string', $this->classMustMatchYear($request)],
        ]);

        if ($err = $this->cascadeError($validated)) {
            return back()->withErrors(['user_id' => $err]);
        }

        if ($err = $this->conflictError($validated)) {
            return back()->with('error', $err);
        }

        PjAssignment::create($validated);

        // Sinkronkan role + pj_subject di user agar dashboard PJ langsung jalan
        User::find($validated['user_id'])->promoteToPj(Subject::find($validated['subject_id'])->name);

        return back()->with('success', 'PJ berhasil ditambahkan.');
    }

    public function update(Request $request, PjAssignment $pj)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'user_id' => ['required', Rule::exists('users', 'id')],
            'subject_id' => ['required', Rule::exists('subjects', 'id')],
            'academic_year_id' => ['required', Rule::exists('academic_years', 'id')],
            'class_name' => ['required', 'string', $this->classMustMatchYear($request)],
        ]);

        if ($err = $this->cascadeError($validated)) {
            return back()->withErrors(['user_id' => $err]);
        }

        if ($err = $this->conflictError($validated, $pj->id)) {
            return back()->with('error', $err);
        }

        $oldUserId = $pj->user_id;
        $pj->update($validated);

        $newUserId = (int) $validated['user_id'];

        // User baru / yang tetap: sync role + pj_subject
        User::find($newUserId)->promoteToPj(Subject::find($validated['subject_id'])->name);

        // User lama yang diganti: cabut status PJ kalau assignment-nya habis
        if ($oldUserId !== $newUserId) {
            $oldUser = User::find($oldUserId);
            if ($oldUser
                && $oldUser->role === 'pj_mapel'
                && ! PjAssignment::where('user_id', $oldUserId)->exists()) {
                $oldUser->demoteFromPj();
            }
        }

        return back()->with('success', 'PJ assignment diperbarui.');
    }

    public function destroy(PjAssignment $pj)
    {
        $this->authorizeAdmin();

        $userId = $pj->user_id;
        $pj->delete();

        // Kalau user tidak punya assignment lain, revert status PJ-nya
        $user = User::find($userId);
        if ($user && $user->role === 'pj_mapel' && ! PjAssignment::where('user_id', $userId)->exists()) {
            $user->demoteFromPj();
        }

        return back()->with('success', 'PJ assignment dihapus.');
    }
}
