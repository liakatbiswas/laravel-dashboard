<?php

namespace Database\Seeders;

use App\Models\AdmissionOnOff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $super = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        $email = config('permissions.super_admin.email');
        $password = config('permissions.super_admin.password');

        if (! is_string($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new \RuntimeException('SUPER_ADMIN_EMAIL must be set to a valid email address.');
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->assertStrongPassword($password);

            $user = User::create([
                'name' => 'Md. Liakat Biswas',
                'email' => $email,
                'password' => $password,
                'email_verified_at' => now(),
                'is_active' => true,
            ]);
        }

        $user->assignRole($super);

        $this->command?->info('Role & Super Admin Created successfully.');

        AdmissionOnOff::firstOrCreate(['id' => 1], ['is_on' => true]);
    }

    private function assertStrongPassword(mixed $password): void
    {
        $forbidden = ['password', 'replace-with-a-unique-long-password'];

        if (! is_string($password) || strlen($password) < 12 || in_array($password, $forbidden, true)) {
            throw new \RuntimeException(
                'SUPER_ADMIN_PASSWORD must be a unique password of at least 12 characters. Do not use the example placeholder.'
            );
        }
    }
}
