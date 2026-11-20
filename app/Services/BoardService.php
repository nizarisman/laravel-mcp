<?php

namespace App\Services;

use App\Models\Board;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class BoardService
{
    public function all(): Collection
    {
        return Board::withCount('tasks')->latest()->get();
    }

    /**
     * Boards a user owns or is a member of (used by the web UI).
     */
    public function forUser(User $user): Collection
    {
        return Board::accessibleBy($user)->withCount('tasks')->latest()->get();
    }

    public function create(User $user, array $data): Board
    {
        return $user->boards()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
        ]);
    }

    public function update(Board $board, array $data): Board
    {
        $board->update([
            'title' => $data['title'] ?? $board->title,
            'description' => array_key_exists('description', $data) ? $data['description'] : $board->description,
        ]);

        return $board->fresh();
    }

    public function delete(Board $board): void
    {
        $board->delete();
    }

    public function addMember(Board $board, User $member): void
    {
        if ($board->user_id !== $member->id) {
            $board->members()->syncWithoutDetaching([$member->id]);
        }
    }

    public function removeMember(Board $board, User $member): void
    {
        $board->members()->detach($member->id);
    }
}
