<?php
session_start();
require_once __DIR__ . '/handlers/GuessNumberHandler.php';

// セッションの初期化
if (!isset($_SESSION['game_data'])) {
    $_SESSION['game_data'] = [
        'min' => null,
        'max' => null,
        'random_number' => null,
        'message' => ''
    ];
}

$gameData = $_SESSION['game_data'];

// ゲームの初期化
if ($gameData['random_number'] === null) {
    $min = isset($_POST['min']) ? (int)$_POST['min'] : null;
    $max = isset($_POST['max']) ? (int)$_POST['max'] : null;

    if ($min !== null && $max !== null) {
        $gameData['min'] = $min;
        $gameData['max'] = $max;
        $gameData['random_number'] = rand($min, $max);
        $_SESSION['game_data'] = $gameData;
    }
}

// 推測の処理
if ($gameData['random_number'] !== null && isset($_POST['guess'])) {
    $guess = (int)$_POST['guess'];

    if ($guess < $gameData['min'] || $guess > $gameData['max']) {
        $gameData['message'] = "範囲外の数字です。もう一度試してください。";
    } elseif ($guess < $gameData['random_number']) {
        $gameData['message'] = "もっと大きい数字です。";
    } elseif ($guess > $gameData['random_number']) {
        $gameData['message'] = "もっと小さい数字です。";
    } else {
        $gameData['message'] = "正解です！おめでとうございます！";
        // ゲームをリセット
        $_SESSION['game_data'] = [
            'min' => null,
            'max' => null,
            'random_number' => null,
            'message' => ''
        ];
        $gameData = $_SESSION['game_data'];
    }

    $_SESSION['game_data'] = $gameData;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guess the Number Game</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
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

        input[type="number"] {
            padding: 8px;
            border: 1px solid #ccc;
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
    </style>
</head>

<body>
    <div class="container">
        <h1>Guess the Number Game</h1>
        <?php if ($gameData['random_number'] === null): ?>
            <form method="post">
                <div>
                    <label for="min">最小値を入力してください:</label>
                    <input type="number" id="min" name="min" required>
                </div>
                <div>
                    <label for="max">最大値を入力してください:</label>
                    <input type="number" id="max" name="max" required>
                </div>
                <button type="submit">ゲームを開始</button>
            </form>
        <?php else: ?>
            <p>数字を当ててください (<?php echo $gameData['min']; ?> - <?php echo $gameData['max']; ?>):</p>
            <form method="post">
                <div>
                    <input type="number" name="guess" required>
                </div>
                <button type="submit">送信</button>
            </form>
            <?php if ($gameData['message']): ?>
                <div class="message"><?php echo htmlspecialchars($gameData['message']); ?></div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>

</html>