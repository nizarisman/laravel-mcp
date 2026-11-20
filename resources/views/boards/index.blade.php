<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boards - TaskFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen text-zinc-900">
    <!-- Navbar -->
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-6xl mx-auto px-6 h-14 flex items-center justify-between">
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

    <!-- Konten Utama -->
    <main class="max-w-6xl mx-auto px-6 py-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-baseline gap-2">
                <h1 class="text-lg font-semibold">Boards</h1>
                <span class="text-xs text-gray-400">{{ $boards->count() }}</span>
            </div>

            <button onclick="document.getElementById('modal-board').classList.remove('hidden')"
                class="bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-medium px-4 py-2 rounded-lg transition">
                Board baru
            </button>
        </div>

        <!-- Grid Kartu Board -->
        <!-- Grid Kartu Board -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    @foreach ($boards as $board)
        <a href="{{ route('boards.show', $board) }}" class="block bg-white border border-gray-200 rounded-xl p-5 hover:border-zinc-400 transition">
            <div class="text-[11px] text-gray-400 font-mono mb-1">#{{ $board->id }}</div>
            <h2 class="font-semibold text-sm text-zinc-900 mb-1">{{ $board->title }}</h2>
            <p class="text-xs text-gray-500 line-clamp-2 mb-6">{{ $board->description ?? 'Tidak ada deskripsi' }}</p>
            <div class="text-[11px] text-gray-400">{{ $board->tasks_count }} task</div>
        </a>
    @endforeach
</div>
    </main>

    <!-- Modal Tambah Board -->
    <div id="modal-board" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-lg">
            <h2 class="text-base font-semibold mb-4">Buat Board Baru</h2>
            <form method="POST" action="{{ route('boards.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Judul Board</label>
                    <input type="text" name="title" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-board').classList.add('hidden')"
                        class="px-4 py-2 text-xs border border-gray-300 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs bg-zinc-900 text-white rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
