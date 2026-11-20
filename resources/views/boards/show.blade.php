<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $board->title }} - TaskFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen text-zinc-900">
    <!-- Navbar Atas -->
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 h-14 flex items-center justify-between">
            <a href="{{ route('boards.index') }}" class="flex items-center gap-2">
                <div class="w-7 h-7 bg-zinc-900 text-white rounded flex items-center justify-center font-bold text-sm">T</div>
                <span class="font-semibold text-sm">TaskFlow</span>
            </a>

            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-600">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Sub-header Board & Breadcrumb -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-3 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-xs">
                <a href="{{ route('boards.index') }}" class="text-gray-400 hover:text-zinc-900">Boards</a>
                <span class="text-gray-300">/</span>
                <span class="font-mono text-gray-400">{{ $prefix }}</span>
                <span class="font-semibold text-zinc-900">{{ $board->title }}</span>
                @if ($board->description)
                    <span class="text-gray-300">/</span>
                    <span class="text-gray-400 truncate max-w-xs">{{ $board->description }}</span>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <!-- Avatar Inisial Anggota -->
                <div class="flex -space-x-1.5 mr-1">
                    @foreach ($members as $member)
                        <span title="{{ $member->name }}"
                            class="w-6 h-6 rounded-full bg-gray-200 border border-white text-[10px] font-semibold flex items-center justify-center text-zinc-700">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </span>
                    @endforeach
                </div>

                <button onclick="document.getElementById('modal-members').classList.remove('hidden')"
                    class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Anggota
                </button>

                <button onclick="document.getElementById('modal-task').classList.remove('hidden')"
                    class="bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-medium px-3.5 py-1.5 rounded-lg transition">
                    Task baru
                </button>
            </div>
        </div>

        <!-- Bar Filter -->
        <form method="GET" action="{{ route('boards.show', $board) }}" class="max-w-7xl mx-auto px-6 py-2.5 border-t border-gray-100 flex flex-wrap items-center gap-2">
            <button type="submit" name="mine" value="{{ request('mine') ? '' : '1' }}"
                class="px-3 py-1 text-xs rounded-lg border transition {{ request('mine') ? 'bg-zinc-900 text-white border-zinc-900' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                Task saya
            </button>

            <select name="assignee" onchange="this.form.submit()"
                class="px-2.5 py-1 text-xs border border-gray-300 rounded-lg bg-white text-gray-600">
                <option value="">Semua assignee</option>
                @foreach ($members as $member)
                    <option value="{{ $member->id }}" @selected(request('assignee') == $member->id)>{{ $member->name }}</option>
                @endforeach
            </select>

            <select name="priority" onchange="this.form.submit()"
                class="px-2.5 py-1 text-xs border border-gray-300 rounded-lg bg-white text-gray-600">
                <option value="">Semua prioritas</option>
                <option value="low" @selected(request('priority') === 'low')>Low</option>
                <option value="medium" @selected(request('priority') === 'medium')>Medium</option>
                <option value="high" @selected(request('priority') === 'high')>High</option>
            </select>

            @if (request()->hasAny(['mine', 'assignee', 'priority']) && (request('mine') || request('assignee') || request('priority')))
                <a href="{{ route('boards.show', $board) }}" class="text-xs text-gray-400 hover:text-zinc-900 ml-1">Reset</a>
            @endif
        </form>
    </div>

    <!-- Papan Kanban 3 Kolom -->
    @php
        $columns = [
            'todo' => ['label' => 'To Do', 'dot' => 'bg-gray-400'],
            'in_progress' => ['label' => 'In Progress', 'dot' => 'bg-amber-400'],
            'done' => ['label' => 'Done', 'dot' => 'bg-emerald-500'],
        ];
        $priorityColors = [
            'low' => 'bg-gray-400',
            'medium' => 'bg-amber-400',
            'high' => 'bg-red-500',
        ];
    @endphp

    <main class="max-w-7xl mx-auto px-6 py-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-start">
            @foreach ($columns as $statusKey => $col)
                @php
                    $colTasks = $tasks->filter(fn ($t) => ($t->status instanceof \BackedEnum ? $t->status->value : $t->status) === $statusKey);
                @endphp
                <div class="bg-gray-100/70 rounded-xl p-3">
                    <!-- Header Kolom -->
                    <div class="flex items-center gap-2 px-1 mb-3">
                        <span class="w-2 h-2 rounded-full {{ $col['dot'] }}"></span>
                        <h2 class="text-xs font-semibold text-zinc-800">{{ $col['label'] }}</h2>
                        <span class="text-xs text-gray-400">{{ $colTasks->count() }}</span>
                    </div>

                    <!-- Daftar Kartu Task -->
                    <div class="space-y-2.5">
                        @foreach ($colTasks as $task)
                            @php
                                $statusVal = $task->status instanceof \BackedEnum ? $task->status->value : $task->status;
                                $priorityVal = $task->priority instanceof \BackedEnum ? $task->priority->value : $task->priority;
                                $priorityLabel = method_exists($task->priority, 'label') ? $task->priority->label() : ucfirst($priorityVal);
                            @endphp
                            <div class="bg-white border border-gray-200 rounded-xl p-3.5 shadow-2xs hover:border-zinc-400 transition">
                                <div class="flex items-center justify-between text-[11px] text-gray-400 font-mono mb-1.5">
                                    <span>{{ $prefix }}-{{ $task->id }}</span>
                                    <!-- Pindah Status Cepat -->
                                    <form method="POST" action="{{ route('tasks.update', $task) }}">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" class="text-[10px] text-gray-400 bg-transparent border-none focus:outline-none cursor-pointer">
                                            <option value="todo" @selected($statusVal === 'todo')>To Do</option>
                                            <option value="in_progress" @selected($statusVal === 'in_progress')>In Progress</option>
                                            <option value="done" @selected($statusVal === 'done')>Done</option>
                                        </select>
                                    </form>
                                </div>

                                <h3 class="text-xs font-medium text-zinc-900 leading-snug mb-3">{{ $task->title }}</h3>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1.5 text-[11px] text-gray-500">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $priorityColors[$priorityVal] ?? 'bg-gray-400' }}"></span>
                                        <span>{{ $priorityLabel }}</span>
                                        @if ($task->due_date)
                                            <span class="text-gray-300">·</span>
                                            <span>{{ $task->due_date->format('d M') }}</span>
                                        @endif
                                    </div>

                                    @if ($task->assignee)
                                        <span title="{{ $task->assignee->name }}"
                                            class="w-5 h-5 rounded-full bg-gray-200 text-[10px] font-semibold flex items-center justify-center text-zinc-700">
                                            {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </main>

    <!-- Modal Task Baru -->
    <div id="modal-task" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-lg">
            <h2 class="text-base font-semibold mb-4">Buat Task Baru</h2>
            <form method="POST" action="{{ route('tasks.store', $board) }}" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Judul Task</label>
                    <input type="text" name="title" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg">
                            <option value="todo">To Do</option>
                            <option value="in_progress">In Progress</option>
                            <option value="done">Done</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Prioritas</label>
                        <select name="priority" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Assignee</label>
                        <select name="assignee_id" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg">
                            <option value="">Belum ditugaskan</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tenggat</label>
                        <input type="date" name="due_date" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-task').classList.add('hidden')"
                        class="px-4 py-2 text-xs border border-gray-300 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs bg-zinc-900 text-white rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Info Anggota -->
    <div id="modal-members" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl max-w-sm w-full p-6 shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold">Anggota Board</h2>
                <button onclick="document.getElementById('modal-members').classList.add('hidden')" class="text-xs text-gray-400">Tutup</button>
            </div>
            <ul class="space-y-2.5">
                @foreach ($members as $member)
                    <li class="flex items-center justify-between text-xs">
                        <div>
                            <div class="font-medium text-zinc-900">{{ $member->name }}</div>
                            <div class="text-gray-400">{{ $member->email }}</div>
                        </div>
                        @if ($member->id === $board->user_id)
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-[10px]">Owner</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</body>
</html>
