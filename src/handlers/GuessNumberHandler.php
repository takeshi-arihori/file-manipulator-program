<?php
require_once __DIR__ . '/../classes/Game.php';

class GuessNumberHandler extends Game
{
    public function handleGuess($guess)
    {
        if (!$this->isInitialized()) {
            $this->message = "ゲームが初期化されていません。";
            return false;
        }

        $guess = (int)$guess;

        if ($guess < $this->min || $guess > $this->max) {
            $this->message = "範囲外の数字です。もう一度試してください。";
            return false;
        }

        if ($guess < $this->randomNumber) {
            $this->message = "もっと大きい数字です。";
            return false;
        }

        if ($guess > $this->randomNumber) {
            $this->message = "もっと小さい数字です。";
            return false;
        }

        $this->message = "正解です！おめでとうございます！";
        return true;
    }
}
