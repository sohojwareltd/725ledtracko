<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configuredUsers = json_decode((string) env('DEFAULT_USERS_JSON', '[]'), true);

        if (!is_array($configuredUsers) || empty($configuredUsers)) {
            return;
        }

        $usernameColumn = Schema::hasColumn('users', 'UserName') ? 'UserName' : 'username';
        $passwordColumn = Schema::hasColumn('users', 'Password') ? 'Password' : 'password';
        $roleColumn = Schema::hasColumn('users', 'Role') ? 'Role' : 'role';
        $fullNameColumn = Schema::hasColumn('users', 'FullName') ? 'FullName' : null;
        $activeColumn = Schema::hasColumn('users', 'Active') ? 'Active' : null;
        $createdDateColumn = Schema::hasColumn('users', 'CreatedDate') ? 'CreatedDate' : null;

        foreach ($configuredUsers as $user) {
            if (!is_array($user)) {
                continue;
            }

            $username = trim((string) ($user['username'] ?? ''));
            $plainOrHashedPassword = (string) ($user['password'] ?? '');

            if ($username === '' || $plainOrHashedPassword === '') {
                continue;
            }

            $hashInfo = password_get_info($plainOrHashedPassword);
            $passwordValue = !empty($hashInfo['algo'])
                ? $plainOrHashedPassword
                : Hash::make($plainOrHashedPassword);

            $data = [
                $usernameColumn => $username,
                $passwordColumn => $passwordValue,
                $roleColumn => (string) ($user['role'] ?? 'Technician'),
            ];

            if ($fullNameColumn) {
                $data[$fullNameColumn] = (string) ($user['full_name'] ?? $username);
            }

            if ($activeColumn) {
                $data[$activeColumn] = (bool) ($user['active'] ?? true);
            }

            if ($createdDateColumn) {
                $data[$createdDateColumn] = now();
            }

            User::updateOrCreate([
                $usernameColumn => $username,
            ], $data);
        }
    }
}
