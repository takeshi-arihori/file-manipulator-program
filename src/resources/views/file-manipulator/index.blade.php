@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <h1 class="text-3xl font-bold text-center text-white mb-10">File Manipulator Program</h1>
        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- 反転カード -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200 flex flex-col h-full">
                <div class="text-center flex-grow">
                    <div class="text-4xl mb-4">🔄</div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">ファイル内容の反転</h3>
                    <p class="text-gray-600 text-sm mb-4">アップロードしたファイルの内容を逆順にします。</p>
                </div>
                <form method="post" action="{{ route('file-manipulator.process') }}" enctype="multipart/form-data"
                    class="space-y-3 mt-auto">
                    @csrf
                    <input type="hidden" name="command" value="reverse">
                    <input type="file" name="input_file" required
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors duration-200 font-medium">実行してダウンロード</button>
                </form>
            </div>

            <!-- コピーカード -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200 flex flex-col h-full">
                <div class="text-center flex-grow">
                    <div class="text-4xl mb-4">📄</div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">ファイルのコピー</h3>
                    <p class="text-gray-600 text-sm mb-4">ファイル内容を2回連結した新しいファイルを作成します。</p>
                </div>
                <form method="post" action="{{ route('file-manipulator.process') }}" enctype="multipart/form-data"
                    class="space-y-3 mt-auto">
                    @csrf
                    <input type="hidden" name="command" value="copy">
                    <input type="file" name="input_file" required
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100" />
                    <button type="submit"
                        class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors duration-200 font-medium">実行してダウンロード</button>
                </form>
            </div>

            <!-- 複製カード -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200 flex flex-col h-full">
                <div class="text-center flex-grow">
                    <div class="text-4xl mb-4">📑</div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">ファイル内容の複製</h3>
                    <p class="text-gray-600 text-sm mb-4">指定回数だけファイル内容を繰り返します。</p>
                </div>
                <form method="post" action="{{ route('file-manipulator.process') }}" enctype="multipart/form-data"
                    class="space-y-3 mt-auto">
                    @csrf
                    <input type="hidden" name="command" value="duplicate-contents">
                    <input type="file" name="input_file" required
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100" />
                    <input type="number" name="duplicate_count" min="1" value="1"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                        placeholder="複製回数" />
                    <button type="submit"
                        class="w-full bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 transition-colors duration-200 font-medium">実行してダウンロード</button>
                </form>
            </div>

            <!-- 置換カード -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200 flex flex-col h-full">
                <div class="text-center flex-grow">
                    <div class="text-4xl mb-4">🔤</div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">文字列置換</h3>
                    <p class="text-gray-600 text-sm mb-4">指定した文字列を別の文字列に置換します。</p>
                </div>
                <form method="post" action="{{ route('file-manipulator.process') }}" enctype="multipart/form-data"
                    class="space-y-3 mt-auto">
                    @csrf
                    <input type="hidden" name="command" value="replace-string">
                    <input type="file" name="input_file" required
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100" />
                    <input type="text" name="search_string"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                        placeholder="検索文字列" />
                    <input type="text" name="replace_string"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                        placeholder="置換文字列" />
                    <button type="submit"
                        class="w-full bg-orange-600 text-white py-2 px-4 rounded-md hover:bg-orange-700 transition-colors duration-200 font-medium">実行してダウンロード</button>
                </form>
            </div>

            <!-- マークダウン変換カード -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200 flex flex-col h-full">
                <div class="text-center flex-grow">
                    <div class="text-4xl mb-4">📝</div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Markdown → HTML</h3>
                    <p class="text-gray-600 text-sm mb-4">マークダウンファイルをHTMLに変換します。</p>
                </div>
                <form method="post" action="{{ route('file-manipulator.process') }}" enctype="multipart/form-data"
                    class="space-y-3 mt-auto">
                    @csrf
                    <input type="hidden" name="command" value="markdown-to-html">
                    <input type="file" name="input_file" accept=".md,.markdown" required
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                    <div class="text-xs text-gray-500 text-center mb-2">
                        .md または .markdown ファイルのみ
                    </div>
                    <button type="submit"
                        class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition-colors duration-200 font-medium">実行してダウンロード</button>
                </form>
            </div>
        </div>
    </div>
@endsection
