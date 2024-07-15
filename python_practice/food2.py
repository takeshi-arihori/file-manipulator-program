# 2つのプログラムを互いにストリームさせる
# Linux/Unixのパイプ演算子を使用して、あるプログラムの標準出力データを、次のプログラムの標準入力データとして直接渡すこと。
# 1. food2.pyとanswer.pyの2つのプログラムを作成 (food2.pyを最初に実行し、その標準出力データを標準入力としてanswer.pyに送る。 )
# 2. food2.pyが食べ物を尋ね、answer.pyが文字列を単に出力する。(answer.py箱のデータを読み、必要に応じてそれを使用する
# 3. 実行方法: `python3 food2.py | python3 answer.py`


import sys

# ユーザーに好きな食べ物を尋ねる
sys.stdout.buffer.write(b'What is your favorite food?\n')
sys.stdout.flush()

# ユーザーの入力を受け取る
food = sys.stdin.buffer.readline().capitalize()

# 入力された食べ物を標準出力に書き出す
sys.stdout.buffer.write(food + b'\n')
sys.stdout.flush()
