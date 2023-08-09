import sys

# ファイルの内容を逆にする関数
def reverse_file(inputpath, outputpath):
    try:
        # 入力ファイルを読み込む
        with open(inputpath, 'r') as f:
            contents = f.read()
        # 出力ファイルに逆順の内容を書き込む
        with open(outputpath, 'w') as f:
            f.write(contents[::-1])
        print(f"{inputpath} の内容を逆にして {outputpath} に保存しました。")
    except FileNotFoundError:
        print(f"{inputpath} は存在しません。")
    except Exception as e:
        print(f"エラー: {e}")

# ファイルの内容をコピーする関数
def copy_file(inputpath, outputpath):
    try:
        # 入力ファイルを読み込む
        with open(inputpath, 'r') as f:
            contents = f.read()
        # 出力ファイルに内容を書き込む
        with open(outputpath, 'w') as f:
            f.write(contents)
        print(f"{inputpath} の内容を {outputpath} にコピーしました。")
    except FileNotFoundError:
        print(f"{inputpath} は存在しません。")
    except Exception as e:
        print(f"エラー: {e}")

# ファイルの内容を指定された回数だけ複製する関数
def duplicate_contents(inputpath, n):
    try:
        # 入力ファイルを読み込む
        with open(inputpath, 'r') as f:
            contents = f.read()
        # 入力ファイルに内容をn回複製して書き込む
        with open(inputpath, 'w') as f:
            f.write(contents * n)
        print(f"{inputpath} の内容を {n} 回複製しました。")
    except FileNotFoundError:
        print(f"{inputpath} は存在しません。")
    except Exception as e:
        print(f"エラー: {e}")

# ファイル内の特定の文字列を別の文字列に置き換える関数
def replace_string(inputpath, needle, newstring):
    try:
        # 入力ファイルを読み込む
        with open(inputpath, 'r') as f:
            contents = f.read()
        # 指定された文字列を新しい文字列に置き換える
        contents = contents.replace(needle, newstring)
        # 置き換えた内容を入力ファイルに書き込む
        with open(inputpath, 'w') as f:
            f.write(contents)
        print(f"{inputpath} 内の '{needle}' を '{newstring}' に置き換えました。")
    except FileNotFoundError:
        print(f"{inputpath} は存在しません。")
    except Exception as e:
        print(f"エラー: {e}")

# メインの実行部分
if __name__ == "__main__":
    # 引数が不足している場合のエラーチェック
    if len(sys.argv) < 3:
        print("引数が不足しています。")
        sys.exit(1)

    # コマンドを取得
    command = sys.argv[1]

    # 各コマンドに応じた関数を呼び出す
    if command == "reverse":
        reverse_file(sys.argv[2], sys.argv[3])
    elif command == "copy":
        copy_file(sys.argv[2], sys.argv[3])
    elif command == "duplicate-contents":
        duplicate_contents(sys.argv[2], int(sys.argv[3]))
    elif command == "replace-string":
        replace_string(sys.argv[2], sys.argv[3], sys.argv[4])
    else:
        print(f"無効なコマンド: {command}")
