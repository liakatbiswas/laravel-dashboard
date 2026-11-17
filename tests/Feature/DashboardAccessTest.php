<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    public function test_unprivileged_user_does_not_see_admin_statistics(): void
    {
        $student = User::factory()->create()->assignRole('student');

        $this->actingAs($student)->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Total users')
            ->assertDontSee('Active users')
            ->assertDontSee('Roles')
            ->assertSee('Registration');
    }

    public function test_user_with_users_view_permission_sees_admin_statistics(): void
    {
        $manager = $this->userWithPermissions(['users.view']);

        $this->actingAs($manager)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Total users')
            ->assertSee('Active users');
    }

    public function test_super_admin_sees_admin_statistics(): void
    {
        $this->actingAs($this->superAdmin())->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Total users')
            ->assertSee('Active users')
            ->assertSee('Roles');
    }
}
