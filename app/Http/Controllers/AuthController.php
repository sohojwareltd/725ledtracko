<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Case-sensitive username lookup (matching original login.php)
        $user = User::whereRaw('BINARY username = ?', [$request->username])->first();

        if (!$user || $user->password !== $request->password) {
            return back()->withErrors(['login' => 'The user name or password are NOT correct, please:'])->withInput(['username' => $request->username]);
        }

        // Log audit (matching original)
        DB::table('useraudit')->insert([
            'User' => $user->username,
            'Date' => now(),
            'AuditDescription' => 'Session Started',
        ]);

        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();

        return $this->redirectByRole($user);
    }

    private function redirectByRole(User $user)
    {
        // Original: Admin (Pepe, Ale, Luis) -> secreta.php -> orders.php
        // Technician -> secretarepair.php -> repair.php
        $username = strtolower(trim($user->username ?? ''));
        if (in_array($username, ['pepe', 'ale', 'luis'], true)) {
            return redirect('/orders');
        }
        return redirect('/repair');
    }

    public function logout(Request $request)
    {
        // Log audit
        if (Auth::check()) {
            DB::table('useraudit')->insert([
                'User' => Auth::user()->username,
                'Date' => now(),
                'AuditDescription' => 'Session Closed',
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
