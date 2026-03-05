<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_guest_is_redirected_to_login_from_root(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_to_login_from_orders(): void
    {
        $response = $this->get('/orders');

        $response->assertRedirect(route('login'));
    }

    public function test_customer_tracking_page_is_public(): void
    {
        $response = $this->get('/track');

        $response->assertOk();
    }
}
