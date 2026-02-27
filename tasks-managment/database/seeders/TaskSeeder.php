<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();
        $users = User::all();

        foreach ($projects as $project) {
            for ($i = 0; $i < 5; $i++) {
                Task::create([
                    'project_id' => $project->id,
                    'assigned_user_id' => $users->random()->id,
                    'title' => 'Task ' . ($i + 1) . ' for ' . $project->name,
                    'description' => 'This is a seeded task.',
                    'status' => collect(['todo', 'in_progress', 'done'])->random(),
                    'priority' => collect(['low', 'medium', 'high'])->random(),
                    'due_date' => now()->addDays(rand(3, 14)),
                ]);
            }
        }
    }
}

