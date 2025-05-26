@extends('layouts.app')

@section('title', 'ファイル操作ログ')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">📊 ファイル操作ログ</h1>
            <p class="text-gray-400">ファイル操作の履歴とログを確認できます</p>
        </div>

        <!-- フィルター -->
        <div class="bg-gray-800 rounded-lg p-6 mb-8 shadow-lg">
            <h2 class="text-lg font-semibold text-white mb-4">🔍 フィルター</h2>
            <form method="GET" action="{{ route('logs.index') }}"
                class="space-y-4 sm:space-y-0 sm:flex sm:flex-wrap sm:gap-4 sm:items-end">
                <div class="flex-1 min-w-0 sm:min-w-[200px]">
                    <label for="operation" class="block text-sm font-medium text-gray-300 mb-2">操作タイプ</label>
                    <select name="operation" id="operation"
                        class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all" {{ $operation === 'all' ? 'selected' : '' }}>すべて</option>
                        <option value="reverse" {{ $operation === 'reverse' ? 'selected' : '' }}>🔄 リバース</option>
                        <option value="copy" {{ $operation === 'copy' ? 'selected' : '' }}>📋 コピー</option>
                        <option value="duplicate" {{ $operation === 'duplicate' ? 'selected' : '' }}>📑 重複</option>
                        <option value="replace" {{ $operation === 'replace' ? 'selected' : '' }}>🔄 置換</option>
                    </select>
                </div>

                <div class="flex-1 min-w-0 sm:min-w-[200px]">
                    <label for="date" class="block text-sm font-medium text-gray-300 mb-2">日付</label>
                    <input type="date" name="date" id="date" value="{{ $date }}"
                        class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="flex-shrink-0">
                    <button type="submit"
                        class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 font-medium shadow-md hover:shadow-lg focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800">
                        🔍 フィルター
                    </button>
                </div>

                <div class="flex-shrink-0">
                    <a href="{{ route('logs.index') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 font-medium shadow-md hover:shadow-lg">
                        🔄 リセット
                    </a>
                </div>
            </form>
        </div>

        <!-- ログ表示 -->
        @if (empty($logs) || collect($logs)->flatten(1)->isEmpty())
            <div class="bg-gray-800 rounded-lg p-8 text-center shadow-lg">
                <div class="text-6xl mb-4">📝</div>
                <h3 class="text-xl font-semibold text-white mb-2">ログが見つかりません</h3>
                <p class="text-gray-400">指定された条件に一致するログがありません。</p>
                <p class="text-gray-500 text-sm mt-2">別の日付や操作タイプを選択してみてください。</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach ($logs as $operationType => $operationLogs)
                    @if (!empty($operationLogs))
                        <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                            <div class="bg-gray-700 px-6 py-4 border-b border-gray-600">
                                <h2 class="text-xl font-semibold text-white flex items-center justify-between">
                                    <span class="flex items-center">
                                        @switch($operationType)
                                            @case('reverse')
                                                🔄 リバース操作ログ
                                            @break

                                            @case('copy')
                                                📋 コピー操作ログ
                                            @break

                                            @case('duplicate')
                                                📑 重複操作ログ
                                            @break

                                            @case('replace')
                                                🔄 置換操作ログ
                                            @break
                                        @endswitch
                                    </span>
                                    <span class="bg-gray-600 text-gray-300 px-3 py-1 rounded-full text-sm font-medium">
                                        {{ count($operationLogs) }}件
                                    </span>
                                </h2>
                            </div>

                            <div class="p-6">
                                <div class="space-y-4">
                                    @foreach ($operationLogs as $log)
                                        <div
                                            class="bg-gray-700 rounded-lg p-4 border border-gray-600 hover:border-gray-500 transition-colors duration-200">
                                            <div
                                                class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-3 gap-2">
                                                <span class="text-sm text-gray-400 font-mono">
                                                    📅 {{ $log['timestamp'] }}
                                                </span>
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if ($log['level'] === 'ERROR') bg-red-100 text-red-800
                                                    @elseif($log['level'] === 'WARNING') bg-yellow-100 text-yellow-800
                                                    @else bg-green-100 text-green-800 @endif">
                                                    @if ($log['level'] === 'ERROR')
                                                        ❌
                                                    @elseif($log['level'] === 'WARNING')
                                                        ⚠️
                                                    @else
                                                        ✅
                                                    @endif
                                                    {{ $log['level'] }}
                                                </span>
                                            </div>

                                            <div class="mb-3">
                                                <p class="text-gray-200 text-sm leading-relaxed">{{ $log['message'] }}</p>
                                            </div>

                                            <details class="group">
                                                <summary
                                                    class="text-gray-400 text-xs cursor-pointer hover:text-gray-200 transition-colors duration-200 flex items-center gap-1">
                                                    <span
                                                        class="group-open:rotate-90 transition-transform duration-200">▶</span>
                                                    詳細ログを表示
                                                </summary>
                                                <div class="mt-3 p-3 bg-gray-800 rounded border border-gray-600">
                                                    <pre class="text-xs text-gray-200 overflow-x-auto whitespace-pre-wrap break-words">{{ $log['raw'] }}</pre>
                                                </div>
                                            </details>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
@endsection
