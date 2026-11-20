<?php

namespace App\Services;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Board;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskService
{
    public function listForBoard(Board $board, array $filters = []): Collection
    {
        $query = $board->tasks()->with('assignee');

        if (! empty($filters['mine']) && ! empty($filters['user_id'])) {
            $query->where('assignee_id', $filters['user_id']);
        }

        if (! empty($filters['assignee'])) {
            $query->where('assignee_id', $filters['assignee']);
        }

        if (! empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->get();
    }

    public function create(Board $board, array $data): Task
    {
        return $board->tasks()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? TaskStatus::Todo->value,
            'priority' => $data['priority'] ?? TaskPriority::Medium->value,
            'assignee_id' => $data['assignee_id'] ?? null,
            'due_date' => $data['due_date'] ?? null,
        ]);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task->fresh(['assignee']);
    }

    public function move(Task $task, TaskStatus|string $status): Task
    {
        $value = $status instanceof TaskStatus ? $status->value : $status;
        $task->update(['status' => $value]);

        return $task->fresh();
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }
}
