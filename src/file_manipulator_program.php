<?php
session_start();

// reverseコマンドの実行処理
$command = isset($_POST['command']) ? $_POST['command'] : '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($command === 'reverse') {
        // アップロードされたファイルの処理
        if (isset($_FILES['input_file']) && $_FILES['input_file']['error'] === UPLOAD_ERR_OK) {
            $inputContent = file_get_contents($_FILES['input_file']['tmp_name']);
            if ($inputContent === false) {
                $message = 'ファイルの読み込みに失敗しました。';
            } else {
                // ファイルの文字エンコーディングを検出
                $encoding = mb_detect_encoding($inputContent, ['UTF-8', 'SJIS', 'EUC-JP', 'ASCII'], true);

                // 検出できない場合はUTF-8と仮定
                if (!$encoding) {
                    $encoding = 'UTF-8';
                }

                // 一旦UTF-8に変換してから処理
                $utf8Content = mb_convert_encoding($inputContent, 'UTF-8', $encoding);

                // 文字列を反転
                $reversedUtf8 = '';
                $length = mb_strlen($utf8Content, 'UTF-8');

                // マルチバイト文字に対応した反転処理
                for ($i = $length - 1; $i >= 0; $i--) {
                    $reversedUtf8 .= mb_substr($utf8Content, $i, 1, 'UTF-8');
                }

                // 出力ファイルの準備
                $outputFilename = 'reversed_' . $_FILES['input_file']['name'];

                // ファイルをダウンロードさせる
                header('Content-Type: text/plain; charset=UTF-8');
                header('Content-Disposition: attachment; filename="' . $outputFilename . '"');
                header('Content-Length: ' . strlen($reversedUtf8));
                echo $reversedUtf8;
                exit;
            }
        } else {
            $message = 'ファイルのアップロードに失敗しました。';
        }
    } else {
        $message = '未対応のコマンドです。';
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
    </style>
</head>

<body>
    <div class="container">
        <h1>File Manipulator Program</h1>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="command" value="reverse">
            <div>
                <label for="input_file">処理したいファイルを選択:</label>
                <input type="file" id="input_file" name="input_file" required class="file-input">
            </div>
            <button type="submit">実行してダウンロード</button>
        </form>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
    </div>
</body>

</html>
