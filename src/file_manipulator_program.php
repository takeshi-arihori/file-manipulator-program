<?php
session_start();

require_once __DIR__ . '/handlers/ReverseHandler.php';
require_once __DIR__ . '/handlers/CopyHandler.php';
require_once __DIR__ . '/handlers/DuplicateHandler.php';
require_once __DIR__ . '/handlers/ReplaceHandler.php';

$command = isset($_POST['command']) ? $_POST['command'] : '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $handler = null;

    switch ($command) {
        case 'reverse':
            $handler = new ReverseHandler($_FILES['input_file']);
            break;
        case 'copy':
            $handler = new CopyHandler($_FILES['input_file']);
            break;
        case 'duplicate-contents':
            $duplicateCount = isset($_POST['duplicate_count']) ? $_POST['duplicate_count'] : 1;
            $handler = new DuplicateHandler($_FILES['input_file'], $duplicateCount);
            break;
        case 'replace-string':
            $searchString = isset($_POST['search_string']) ? $_POST['search_string'] : '';
            $replaceString = isset($_POST['replace_string']) ? $_POST['replace_string'] : '';
            $handler = new ReplaceHandler($_FILES['input_file'], $searchString, $replaceString);
            break;
        default:
            $message = '未対応のコマンドです。';
            break;
    }

    if ($handler !== null) {
        if (!$handler->handle()) {
            $message = $handler->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manipulator Program</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .container {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        h1 {
            color: #333;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
        }

        .file-input {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 4px;
        }

        .text-input {
            border: 1px solid #ccc;
            padding: 8px;
            border-radius: 4px;
            width: 100%;
        }

        button {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
            background-color: #e3f2fd;
            color: #007BFF;
        }

        .command-selector {
            margin-bottom: 20px;
        }

        .command-selector select {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>File Manipulator Program</h1>
        <form method="post" enctype="multipart/form-data" id="fileForm">
            <div class="command-selector">
                <label for="command">機能を選択:</label>
                <select name="command" id="command" onchange="updateForm()">
                    <option value="reverse">ファイル内容の反転</option>
                    <option value="copy">ファイルのコピー</option>
                    <option value="duplicate-contents">ファイル内容の複製</option>
                    <option value="replace-string">文字列置換</option>
                </select>
            </div>
            <div>
                <label for="input_file">処理したいファイルを選択:</label>
                <input type="file" id="input_file" name="input_file" required class="file-input">
            </div>
            <div id="duplicateCountDiv" style="display: none;">
                <label for="duplicate_count">複製回数:</label>
                <input type="number" id="duplicate_count" name="duplicate_count" min="1" value="1" class="text-input">
            </div>
            <div id="replaceStringDiv" style="display: none;">
                <label for="search_string">検索文字列:</label>
                <input type="text" id="search_string" name="search_string" class="text-input">
                <label for="replace_string">置換文字列:</label>
                <input type="text" id="replace_string" name="replace_string" class="text-input">
            </div>
            <button type="submit">実行してダウンロード</button>
        </form>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
    </div>

    <script>
        function updateForm() {
            const command = document.getElementById('command').value;
            const duplicateCountDiv = document.getElementById('duplicateCountDiv');
            const replaceStringDiv = document.getElementById('replaceStringDiv');

            duplicateCountDiv.style.display = command === 'duplicate-contents' ? 'block' : 'none';
            replaceStringDiv.style.display = command === 'replace-string' ? 'block' : 'none';
        }

        // 初期表示時にフォームを更新
        updateForm();
    </script>
</body>

</html>