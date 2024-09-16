import sys
import random

guess_min = 0
guess_max = 0

while True:
    # ユーザーに2つの数字(n, m)を入力してもらう
    guess_min = int(input('小さい数字を入力してください。 ')) 
    guess_max = int(input('大きい数字を入力してください。 '))

    # Validate: n < m となっているかチェック
    if guess_max <= guess_min :
        print('大きい数字が間違っています。大きい数字が ' +  str(guess_min) + ' 以上になるように正しく入力してください。')
    else :
        print('ゲームを開始します!!')
        break

# ゲームの回数を指定
num_of_games = int(input('ゲームの回数を数字で入力してください。'))
random_num = random.randint(guess_min, guess_max)

# loop 処理を書く(回数制限を含める)
while num_of_games > 0:
    guess_num = int(input('数値を入力してください。'))
    if guess_num == random_num:
        print('推測した数字と一致しました。ゲームを終了します。')
        break
    elif guess_num > random_num:
        print('正しい数字は、推測した数値より小さいです。')
    else:
        print('正しい数字は、推測した数値より大きいです。')
    num_of_games -= 1


# ゲームの結果を表示
if num_of_games <= 0:
    print('ゲームオーバーです。')
else:
    print(str(num_of_games) + ' 回でゲームクリアです。')