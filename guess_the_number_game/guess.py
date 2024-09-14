import sys
# この課題では、ユーザーに 2 つの数字、最小数（n）と最大数（m）を入力してもらうことになります。最小値が最大値以下であることを確認することが重要です。
# ユーザーは、この 2 つの数字を入力すると、プログラムが n から m の範囲内で乱数を生成します。
# その後、ユーザーは乱数を正しく推測するまで、ゲームループの中で繰り返し数字を入力することになります。
# 与えられた範囲内の乱数を生成するには、random モジュールと randint 関数を使用してください。
# ゲームをより難しくするために、ユーザーが数字を推測するための試行回数を制限することができます。
# この場合、for 文で最大 n 回の試行を行うか、while 文でユーザーが数字を正しく当てるまで繰り返し入力する方法があります。z

# ユーザーに2つの数字(n, m)を入力してもらう
guess_min = input('小さい数字')
guess_max = input('大きい数字')
if int(min) >= int(max):
    print('break')
else:
    print('clear')

# Validate: n < m となっているかチェック

# loop 処理を書く(回数制限を含める)

# ゲームの結果を表示