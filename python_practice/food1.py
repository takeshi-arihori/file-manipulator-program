# 標準ストリームからデータを読み込む
# input関数を使用して、ターミナルから１に渡されたデータを読み取る。
# food = input('What is your favorite food?\n')
# print('Thanks for letting me know favorite food is ' + food)


# 入力ラッパーを使わずに直接バッファに書き込む(sysモジュールのstdinを使用)
# ※Python側に保存されている一時的なバッファのデータをクリアにするために、stdoutをフラッシュする必要があり、
# 一定のデータ制御に達するかフラッシュが実行された後にのみstdoutに送信されるようになること。
# これを行わないと、Pythonのプログラムが終了するまでstdoutが表示されないことがある。
# readlineを使用してstdinからデータを取得する場合、バイトから文字列にデコードする必要がある。

import sys

# ユーザーに好きな食べ物を尋ねる
sys.stdout.buffer.write(b'What is your favorite food?\n')
sys.stdout.flush()
food = sys.stdin.buffer.readline()
print('Thanks for letting me know your favorite food is ' + food.decode())

