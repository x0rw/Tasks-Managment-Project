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
        // Seed one known user per role for testing
        User::factory()->create([
            'name'  => 'Admin User',
            'email' => 'admin@app.com',
            'role'  => 'admin',
        ]);

        User::factory()->create([
            'name'  => 'Manager User',
            'email' => 'manager@app.com',
            'role'  => 'manager',
        ]);

        User::factory()->create([
            'name'  => 'Regular User',
            'email' => 'user@app.com',
            'role'  => 'user',
        ]);

        // Extra random users (role defaults to 'user')
        User::factory(5)->create();

        $this->call([
            ProjectSeeder::class,
            TaskSeeder::class,
        ]);
    }
}
