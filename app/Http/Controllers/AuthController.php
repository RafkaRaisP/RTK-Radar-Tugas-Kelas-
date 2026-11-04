<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Cek NIS saat pendaftaran: otomatis isi nama + kelas dari data siswa
    public function checkNis(Request $request, string $nis)
    {
        $student = Student::where('nis', $nis)->first();

        if (! $student) {
            return response()->json(['found' => false, 'message' => 'NIS tidak ditemukan.'], 404);
        }

        if (User::where('nis', $student->nis)->exists()) {
            return response()->json(['found' => false, 'message' => 'NIS sudah terdaftar.'], 409);
        }

        return response()->json([
            'found' => true,
            'name' => $student->name,
            'class_name' => $student->class_name,
            'year' => $student->academicYear?->name,
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nis' => ['required', 'string', 'regex:/^[0-9]{9}$/', 'max:20',
                Rule::exists('students', 'nis'), Rule::unique('users', 'nis')],
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|lowercase|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'nis.regex' => 'NIS harus 9 digit angka.',
            'nis.exists' => 'NIS tidak terdaftar di sekolah.',
            'nis.unique' => 'NIS ini sudah terdaftar sebagai akun.',
        ]);

        $student = Student::where('nis', $validated['nis'])->firstOrFail();

        $user = User::create([
            'nis' => $student->nis,
            'name' => $student->name,
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'siswa',
            'class_name' => $student->class_name,
            'academic_year_id' => $student->academic_year_id,
        ]);
        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['username' => 'Username atau password salah.']);
        }

        if (in_array($user->status, ['banned', 'suspended'])) {
            return back()->withErrors(['username' => 'Akun ini tidak aktif. Hubungi admin.']);
        }

        Auth::login($user, $request->filled('remember'));

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => $password])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
