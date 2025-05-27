<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manipulator Program</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-900 min-h-screen flex flex-col">
    <nav class="bg-gray-800 shadow-sm border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('file-manipulator.index') }}"
                            class="text-xl font-bold text-white hover:text-gray-300 transition-colors duration-200">
                            File Manipulator
                        </a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('file-manipulator.index') }}"
                            class="border-transparent text-gray-300 hover:text-white hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200 {{ request()->routeIs('file-manipulator.index') ? 'border-blue-500 text-white' : '' }}">
                            📁 ファイル操作
                        </a>
                        <a href="{{ route('logs.index') }}"
                            class="border-transparent text-gray-300 hover:text-white hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200 {{ request()->routeIs('logs.index') ? 'border-blue-500 text-white' : '' }}">
                            📊 ログ
                        </a>
                    </div>
                </div>

                <!-- モバイルメニューボタン -->
                <div class="sm:hidden flex items-center">
                    <button type="button"
                        class="text-gray-300 hover:text-white focus:outline-none focus:text-white transition-colors duration-200"
                        onclick="toggleMobileMenu()">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- モバイルメニュー -->
            <div id="mobile-menu" class="sm:hidden hidden">
                <div class="px-2 pt-2 pb-3 space-y-1 border-t border-gray-700">
                    <a href="{{ route('file-manipulator.index') }}"
                        class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('file-manipulator.index') ? 'bg-gray-700 text-white' : '' }}">
                        📁 ファイル操作
                    </a>
                    <a href="{{ route('logs.index') }}"
                        class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('logs.index') ? 'bg-gray-700 text-white' : '' }}">
                        📊 ログ
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>

    <main class="flex-1 py-8">
        @yield('content')
    </main>

    <footer class="bg-gray-800 border-t border-gray-700">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-400 text-sm">
                &copy; {{ date('Y') }} File Manipulator Program
            </p>
        </div>
    </footer>
</body>

</html>
