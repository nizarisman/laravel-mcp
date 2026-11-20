<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - TaskFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="flex items-center gap-2 mb-6">
            <div class="w-7 h-7 bg-zinc-900 text-white rounded flex items-center justify-center font-bold text-sm">T</div>
            <span class="font-semibold text-zinc-900">TaskFlow</span>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <h1 class="text-lg font-semibold text-zinc-900">Masuk</h1>
            <p class="text-xs text-gray-500 mb-5">Login untuk melanjutkan</p>

            @if ($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-600 text-xs">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', 'test@example.com') }}" required autofocus
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-zinc-900">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-zinc-900">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember" class="rounded border-gray-300">
                    <label for="remember" class="text-xs text-gray-600">Ingat saya</label>
                </div>

                <button type="submit" class="w-full bg-zinc-900 hover:bg-zinc-800 text-white text-sm font-medium py-2 rounded-lg transition">
                    Masuk
                </button>
            </form>

            <div class="mt-5 text-center text-xs text-gray-500 space-y-1">
                <p>Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-zinc-900 underline">Daftar</a></p>
                <p class="text-[11px] text-gray-400">Demo: test@example.com / password</p>
            </div>
        </div>
    </div>
</body>
</html>
