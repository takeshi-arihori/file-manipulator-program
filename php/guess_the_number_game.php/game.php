<?php
/**
 * Undocumented function
 *
 * @param [type] $prompt
 * @return void
 */
function getUserInput($prompt) {
    echo $prompt;
    return trim(fgets(STDIN));
}

/**
 * Undocumented function
 *
 * @return void
 */
function main() {
    // ユーザーに最小値と最大値を入力してもらいます。
    $n = intval(getUserInput("最小値を入力してください: "));
    $m = intval(getUserInput("最大値を入力してください: "));

    // 最小値が最大値以下であることを確認します。
    while ($n > $m) {
        echo "最小値は最大値以下である必要があります。\n";
        $n = intval(getUserInput("最小値を再入力してください: "));
        $m = intval(getUserInput("最大値を再入力してください: "));
    }

    // n から m の範囲で乱数を生成します。
    $random_number = rand($n, $m);

    // ユーザーが乱数を推測するための試行回数を制限します。
    $max_attempts = intval(getUserInput("please input!!"));
    echo "あなたは {$max_attempts} 回の試行で数字を推測する必要があります。\n";

    for ($attempt = 0; $attempt < $max_attempts; $attempt++) {
        $guess = intval(getUserInput(($attempt + 1) . " 回目の推測: "));

        if ($guess == $random_number) {
            echo "正解！\n";
            break;
        } elseif ($guess < $random_number) {
            echo "もっと大きい数字です。\n";
        } else {
            echo "もっと小さい数字です。\n";
        }

        if ($attempt == $max_attempts - 1) {
            echo "残念！正解は {$random_number} でした。\n";
        }
    }
}

main();

?>
