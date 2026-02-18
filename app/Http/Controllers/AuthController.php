<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        $usernameColumn = Schema::hasColumn('users', 'UserName') ? 'UserName' : 'username';
        $passwordColumn = Schema::hasColumn('users', 'Password') ? 'Password' : 'password';
        $username = trim($request->username);

        // Try exact-case lookup first, then fallback to case-insensitive lookup
        $user = User::whereRaw("BINARY {$usernameColumn} = ?", [$username])->first();
        if (!$user) {
            $user = User::whereRaw("LOWER({$usernameColumn}) = ?", [strtolower($username)])->first();
        }

        $storedPassword = $user ? (string) ($user->{$passwordColumn} ?? $user->password ?? '') : '';
        $hashInfo = password_get_info($storedPassword);
        $isHashedPassword = !empty($hashInfo['algo']);

        $passwordIsValid = false;
        if ($storedPassword !== '') {
            if ($isHashedPassword) {
                $passwordIsValid = password_verify($request->password, $storedPassword);
            } else {
                $passwordIsValid = hash_equals($storedPassword, $request->password);
            }
        }

        if (!$user || !$passwordIsValid) {
            return back()->withErrors(['login' => 'The user name or password are NOT correct, please:'])->withInput(['username' => $username]);
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
        $role = strtolower(trim((string) ($user->role ?? '')));
        if ($role === 'admin') {
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
