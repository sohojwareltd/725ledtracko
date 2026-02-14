<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Users from original PHP project (d:\laragon\www\725tracko\login.php)
     */
    public function run(): void
    {
        // Admin/Manager Users (redirect to secreta.php in original)
        User::create([
            'UserName' => 'Pepe',
            'Password' => Hash::make('0615'),
            'FullName' => 'Pepe - Manager',
            'Role' => 'Admin',
            'Active' => true,
            'CreatedDate' => now(),
        ]);

        User::create([
            'UserName' => 'Ale',
            'Password' => Hash::make('1610'),
            'FullName' => 'Ale - Manager',
            'Role' => 'Admin',
            'Active' => true,
            'CreatedDate' => now(),
        ]);

        User::create([
            'UserName' => 'Luis',
            'Password' => Hash::make('2088'),
            'FullName' => 'Luis - Manager',
            'Role' => 'Admin',
            'Active' => true,
            'CreatedDate' => now(),
        ]);

        // Technician Users (redirect to secretarepair.php in original)
        User::create([
            'UserName' => 'Martin',
            'Password' => Hash::make('1968'),
            'FullName' => 'Martin - Technician',
            'Role' => 'Technician',
            'Active' => true,
            'CreatedDate' => now(),
        ]);

        User::create([
            'UserName' => 'Luis1C',
            'Password' => Hash::make('4351'),
            'FullName' => 'Luis C - Technician',
            'Role' => 'Technician',
            'Active' => true,
            'CreatedDate' => now(),
        ]);

        User::create([
            'UserName' => 'Hugo',
            'Password' => Hash::make('3096'),
            'FullName' => 'Hugo - Technician',
            'Role' => 'Technician',
            'Active' => true,
            'CreatedDate' => now(),
        ]);

        User::create([
            'UserName' => 'Jefe',
            'Password' => Hash::make('2651'),
            'FullName' => 'Jefe - Technician',
            'Role' => 'Technician',
            'Active' => true,
            'CreatedDate' => now(),
        ]);

        User::create([
            'UserName' => 'Anthony',
            'Password' => Hash::make('2834'),
            'FullName' => 'Anthony - Technician',
            'Role' => 'Technician',
            'Active' => true,
            'CreatedDate' => now(),
        ]);

        User::create([
            'UserName' => 'Jua1n',
            'Password' => Hash::make('1234'),
            'FullName' => 'Juan - Technician',
            'Role' => 'Technician',
            'Active' => true,
            'CreatedDate' => now(),
        ]);
    }
}
