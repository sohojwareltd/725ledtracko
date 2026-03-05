<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_accepts_legacy_password_for_existing_user(): void
    {
        User::create([
            'UserName' => 'Pepe',
            'Password' => '1234',
            'FullName' => 'Pepe',
            'Role' => 'Admin',
            'Active' => 1,
        ]);

        $response = $this->post('/login', [
            'username' => 'pepe',
            'password' => '0615',
        ]);

        $response->assertRedirect('/orders');
        $this->assertAuthenticated();
    }

    public function test_login_creates_missing_legacy_user_and_authenticates(): void
    {
        $response = $this->post('/login', [
            'username' => 'martin',
            'password' => '1968',
        ]);

        $response->assertRedirect('/repair');
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'UserName' => 'Martin',
            'Role' => 'Technician',
            'Active' => 1,
        ]);
    }
}
