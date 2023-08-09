# sys モジュールをインポートします。
import sys

# ユーザーからの入力をバッファから読み取ります。
food = sys.stdin.buffer.readline().strip()

# ユーザーの回答を表示します。
print('Thanks for letting me know your favorite food is ' + food.decode())
