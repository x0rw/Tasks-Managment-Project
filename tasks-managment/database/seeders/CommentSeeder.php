<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $tasks = Task::all();
        $users = User::all();

        if ($tasks->isEmpty() || $users->isEmpty()) {
            return;
        }

        $sampleComments = [
            ['title' => 'Looks good!',      'content' => 'I reviewed this task and everything looks on track.'],
            ['title' => 'Need more info',   'content' => 'Can we clarify the requirements before proceeding?'],
            ['title' => 'Blocked',          'content' => 'Waiting on the design team to deliver assets.'],
            ['title' => 'Done my part',     'content' => 'Finished my portion, passing it to the next person.'],
            ['title' => 'Quick note',       'content' => 'Added some additional context to the description.'],
            ['title' => 'Question',         'content' => 'Who is responsible for the final review?'],
            ['title' => 'Updated',          'content' => 'I made some changes and pushed an update.'],
            ['title' => 'Progress update',  'content' => 'About 60% done, should wrap up by tomorrow.'],
        ];

        foreach ($tasks->random(min(10, $tasks->count())) as $task) {
            $count = rand(1, 3);
            for ($i = 0; $i < $count; $i++) {
                $sample = $sampleComments[array_rand($sampleComments)];
                Comment::create([
                    'task_id' => $task->id,
                    'user_id' => $users->random()->id,
                    'title'   => $sample['title'],
                    'content' => $sample['content'],
                ]);
            }
        }
    }
}
