<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
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

        $query = User::query()->withCount('tasks');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%$s%")->orWhere('username', 'like', "%$s%")->orWhere('email', 'like', "%$s%"));
        }
        if ($request->filled('class_name')) {
            $query->where('class_name', $request->class_name);
        }
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('class_name')->orderBy('role')->paginate(20)->withQueryString();

        return view('admin.users', [
            'users' => $users,
            'roleOptions' => ['siswa', 'ketua_kelas', 'sekretaris', 'pj_mapel', 'admin'],
            'classOptions' => User::CLASS_OPTIONS,
            'statusOptions' => ['active', 'banned', 'suspended'],
        ]);
    }

    public function storeUser(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'class_name' => ['required', Rule::in(User::CLASS_OPTIONS)],
            'role' => ['required', Rule::in(['siswa', 'ketua_kelas', 'sekretaris', 'pj_mapel', 'admin'])],
            'pj_subject' => ['nullable', 'required_if:role,pj_mapel'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = 'active';
        if (($validated['role'] ?? '') !== 'pj_mapel') {
            $validated['pj_subject'] = null;
        }

        $validated['academic_year_id'] = AcademicYear::currentId();

        User::create($validated);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function updateUser(Request $request, User $user)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'class_name' => ['required', Rule::in(User::CLASS_OPTIONS)],
            'role' => ['required', Rule::in(['siswa', 'ketua_kelas', 'sekretaris', 'pj_mapel', 'admin'])],
            'pj_subject' => ['nullable', 'required_if:role,pj_mapel'],
        ]);

        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
            return back()->with('error', 'Tidak bisa menghapus peran admin pada akun sendiri.');
        }

        if ($validated['role'] !== 'pj_mapel') {
            $validated['pj_subject'] = null;
        }

        $validated['academic_year_id'] = AcademicYear::currentId();

        $user->update($validated);

        return back()->with('success', 'Data user diperbarui.');
    }

    public function destroyUser(User $user)
    {
        $this->authorizeAdmin();

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        foreach ($user->proofPhotos as $photo) {
            Storage::disk('public')->delete($photo->photo_path);
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }

    public function sendResetLink(Request $request, User $user)
    {
        $this->authorizeAdmin();

        if (empty($user->email)) {
            return back()->with('error', "User {$user->name} belum punya email terdaftar.");
        }

        $status = Password::sendResetLink(['email' => $user->email]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', "Link reset dikirim ke email {$user->email}.")
            : back()->with('error', "Gagal mengirim link reset ke {$user->email}.");
    }

    public function banUser(User $user)
    {
        $this->authorizeAdmin();

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa ban akun sendiri.');
        }

        $user->update(['status' => 'banned']);

        return back()->with('success', "User {$user->name} berhasil dibanned.");
    }

    public function suspendUser(User $user)
    {
        $this->authorizeAdmin();

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa suspend akun sendiri.');
        }

        $user->update(['status' => 'suspended']);

        return back()->with('success', "User {$user->name} berhasil disuspend.");
    }

    public function activateUser(User $user)
    {
        $this->authorizeAdmin();

        $user->update(['status' => 'active']);

        return back()->with('success', "User {$user->name} berhasil diaktifkan.");
    }
}
