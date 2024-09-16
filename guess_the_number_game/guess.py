import sys
import random
# この課題では、ユーザーに 2 つの数字、最小数（n）と最大数（m）を入力してもらうことになります。最小値が最大値以下であることを確認することが重要です。
# ユーザーは、この 2 つの数字を入力すると、プログラムが n から m の範囲内で乱数を生成します。
# その後、ユーザーは乱数を正しく推測するまで、ゲームループの中で繰り返し数字を入力することになります。
# 与えられた範囲内の乱数を生成するには、random モジュールと randint 関数を使用してください。
# ゲームをより難しくするために、ユーザーが数字を推測するための試行回数を制限することができます。
# この場合、for 文で最大 n 回の試行を行うか、while 文でユーザーが数字を正しく当てるまで繰り返し入力する方法があります。z

guess_min = 0
guess_max = 0

while True:
    # ユーザーに2つの数字(n, m)を入力してもらう
    guess_min = int(input('小さい数字')) 
    guess_max = int(input('大きい数字'))

    # Validate: n < m となっているかチェック
    if guess_max <= guess_min :
        print('大きい数字が間違っています。大きい数字が ' +  str(guess_min) + ' 以上になるように正しく入力してください。')
    else :
        print('ゲームを開始します!!')
        break

num_of_games = 5 # 初期値
# ゲームの回数を指定
# num_of_games = int(input('ゲームの回数を数字で入力してください。(選択がなければ初期値は5ゲームです。)'))
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