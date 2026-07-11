<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        \App\Models\Employee::create([
            'user_id' => $admin->id,
            'role' => 'admin',
            'employee_code' => 'EMP001',
        ]);

        $employee = User::factory()->create([
            'name' => 'Employee User',
            'email' => 'employee@example.com',
            'password' => bcrypt('password'),
        ]);

        \App\Models\Employee::create([
            'user_id' => $employee->id,
            'role' => 'employee',
            'employee_code' => 'EMP002',
        ]);

        $this->call([
            ProductSeeder::class,
        ]);
    }
}
