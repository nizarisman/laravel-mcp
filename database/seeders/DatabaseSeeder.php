<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $test = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'is_admin' => true,
        ]);

        $budi = User::factory()->create(['name' => 'Budi', 'email' => 'budi@example.com']);
        $ani = User::factory()->create(['name' => 'Ani', 'email' => 'ani@example.com']);

        $boards = Board::factory(3)
            ->for($test)
            ->has(Task::factory()->count(6))
            ->create();

        $boards->each(fn(Board $board) => $board->members()->sync([$budi->id, $ani->id]));

        Task::query()->inRandomOrder()->limit(9)->get()
            ->each(fn(Task $task) => $task->update([
                'assignee_id' => fake()->randomElement([$test->id, $budi->id, $ani->id]),
            ]));
    }
}
