<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Services\BoardService;
use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BoardController extends Controller
{
    public function __construct(
        protected BoardService $boardService,
        protected TaskService $taskService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        // Ambil board milik user atau board di mana user terdaftar sebagai member
        $boards = Board::withCount('tasks')
            ->where('user_id', $user->id)
            ->orWhereHas('members', fn($q) => $q->where('users.id', $user->id))
            ->latest()
            ->get();

        return view('boards.index', compact('boards'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $request->user()->boards()->create($data);

        return redirect()->route('boards.index');
    }

    public function show(Request $request, Board $board): View
    {
        Gate::authorize('view', $board);

        $board->load(['user', 'members']);

        $tasksQuery = $board->tasks()->with('assignee');

        if ($request->boolean('mine')) {
            $tasksQuery->where('assignee_id', $request->user()->id);
        }

        if ($request->filled('assignee')) {
            $tasksQuery->where('assignee_id', $request->assignee);
        }

        if ($request->filled('priority')) {
            $tasksQuery->where('priority', $request->priority);
        }

        $tasks = $tasksQuery->get();
        $members = $board->members->prepend($board->user)->unique('id');
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $board->title), 0, 3) ?: 'TSK');

        return view('boards.show', compact('board', 'tasks', 'members', 'prefix'));
    }
}
