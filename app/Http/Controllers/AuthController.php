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
        $legacyAccount = $this->legacyAccountByUsername($username);
        $legacyPasswordIsValid = $legacyAccount && hash_equals($legacyAccount['password'], $request->password);

        // Try exact-case lookup first, then fallback to case-insensitive lookup.
        // `BINARY` is MySQL-specific, so keep non-MySQL drivers compatible.
        if (DB::getDriverName() === 'mysql') {
            $user = User::whereRaw("BINARY {$usernameColumn} = ?", [$username])->first();
        } else {
            $user = User::where($usernameColumn, $username)->first();
        }
        if (!$user) {
            $user = User::whereRaw("LOWER({$usernameColumn}) = ?", [strtolower($username)])->first();
        }

        // In legacy PHP, some users were hardcoded instead of persisted in DB.
        // Create the user record lazily so Laravel session auth can proceed normally.
        if (!$user && $legacyPasswordIsValid) {
            $user = User::create([
                'UserName' => $legacyAccount['username'],
                'Password' => $legacyAccount['password'],
                'FullName' => $legacyAccount['username'],
                'Role' => $legacyAccount['role'],
                'Active' => 1,
            ]);
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

        // Accept exact legacy credentials as an allowed fallback.
        $passwordIsValid = $passwordIsValid || $legacyPasswordIsValid;

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

    private function legacyAccountByUsername(string $username): ?array
    {
        $legacyAccounts = [
            'pepe' => ['username' => 'Pepe', 'password' => '0615', 'role' => 'Admin'],
            'ale' => ['username' => 'Ale', 'password' => '1610', 'role' => 'Admin'],
            'luis' => ['username' => 'Luis', 'password' => '2088', 'role' => 'Admin'],
            'martin' => ['username' => 'Martin', 'password' => '1968', 'role' => 'Technician'],
            'luisc' => ['username' => 'LuisC', 'password' => '4351', 'role' => 'Technician'],
            'luis1c' => ['username' => 'Luis1C', 'password' => '4351', 'role' => 'Technician'],
            'hugo' => ['username' => 'Hugo', 'password' => '3096', 'role' => 'Technician'],
            'jefe' => ['username' => 'Jefe', 'password' => '2651', 'role' => 'Technician'],
            'anthony' => ['username' => 'Anthony', 'password' => '2834', 'role' => 'Technician'],
            'juan' => ['username' => 'Juan', 'password' => '1234', 'role' => 'Technician'],
            'jua1n' => ['username' => 'Jua1n', 'password' => '1234', 'role' => 'Technician'],
        ];

        return $legacyAccounts[strtolower(trim($username))] ?? null;
    }

    private function redirectByRole(User $user)
    {
        $role = strtolower(trim((string) ($user->role ?? '')));

        return match ($role) {
            'admin' => redirect('/orders'),
            'reception' => redirect('/receive'),
            'qc' => redirect('/qc'),
            default => redirect('/repair'),
        };
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
