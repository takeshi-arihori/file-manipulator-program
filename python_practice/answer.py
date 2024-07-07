import sys

# 標準入力からデータを読み取る
for line in sys.stdin:
  # 改行を削除し、大文字に変換して出力
  print(line.strip().upper())