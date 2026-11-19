<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class WelcomePageTest extends TestCase
{
    public function test_default_site_title_is_efa_bangladesh(): void
    {
        $this->get('/')->assertOk()->assertSee('<title>EFA Bangladesh', false);
    }

    public function test_guest_sees_the_login_link(): void
    {
        $this->get('/')->assertOk()->assertSee('Log in');
    }

    public function test_authenticated_user_does_not_see_the_login_link(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/')
            ->assertOk()
            ->assertDontSee('Log in')
            ->assertSee('Dashboard');
    }
}
