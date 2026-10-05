<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login', ['classes' => User::CLASS_OPTIONS]);
    }

    public function showRegisterForm()
    {
        return view('auth.register', ['classes' => User::CLASS_OPTIONS]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|lowercase|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'class_name' => ['required', Rule::in(User::CLASS_OPTIONS)],
        ]);

        $user = User::create([...$validated, 'role' => 'siswa', 'academic_year_id' => AcademicYear::currentId()]);
        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
            'class_name' => ['required', Rule::in(User::CLASS_OPTIONS)],
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['username' => 'Username atau password salah.']);
        }

        if (strtolower($credentials['class_name']) !== strtolower($user->class_name)) {
            return back()->withErrors(['class_name' => 'Nama kelas tidak cocok dengan akun ini.']);
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
}
