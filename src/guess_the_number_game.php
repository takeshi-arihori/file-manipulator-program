<?php
session_start();

// 初期化
if (!isset($_SESSION['min']) || !isset($_SESSION['max']) || !isset($_SESSION['randomNumber'])) {
    $_SESSION['min'] = isset($_POST['min']) ? (int)$_POST['min'] : null;
    $_SESSION['max'] = isset($_POST['max']) ? (int)$_POST['max'] : null;

    if ($_SESSION['min'] !== null && $_SESSION['max'] !== null) {
        $_SESSION['randomNumber'] = rand($_SESSION['min'], $_SESSION['max']);
    }
}

// ゲームロジック
$message = '';
if (isset($_POST['guess'])) {
    $guess = (int)$_POST['guess'];
    $randomNumber = $_SESSION['randomNumber'];

    if ($guess < $_SESSION['min'] || $guess > $_SESSION['max']) {
        $message = "範囲外の数字です。もう一度試してください。";
    } elseif ($guess < $randomNumber) {
        $message = "もっと大きい数字です。";
    } elseif ($guess > $randomNumber) {
        $message = "もっと小さい数字です。";
    } else {
        $message = "正解です！おめでとうございます！";
        session_destroy(); // ゲーム終了後にセッションをリセット
    }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guess the Number Game</title>
</head>

<body>
    <h1>Guess the Number Game</h1>
    <?php if (!isset($_SESSION['randomNumber'])): ?>
        <form method="post">
            <label for="min">最小値を入力してください:</label>
            <input type="number" id="min" name="min" required>
            <br>
            <label for="max">最大値を入力してください:</label>
            <input type="number" id="max" name="max" required>
            <br>
            <button type="submit">ゲームを開始</button>
        </form>
    <?php else: ?>
        <p>数字を当ててください (<?php echo $_SESSION['min']; ?> - <?php echo $_SESSION['max']; ?>):</p>
        <form method="post">
            <input type="number" name="guess" required>
            <button type="submit">送信</button>
        </form>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
</body>

</html>
