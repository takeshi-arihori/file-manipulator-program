# ファイル名を指定
pathname = 'test.txt'
contents = ''

# ファイルの読み込みを試みる
try:
    # ファイルを読み込みモードで開く
    with open(pathname) as f:
        contents = f.read()
    # ファイルの内容を表示
    print(f"{pathname} の内容は次の通りです：\n{contents}")
except FileNotFoundError:
    # ファイルが見つからない場合のエラーメッセージ
    print(f"{pathname} は存在しません。")
else:
    # ファイルの読み込みが成功した場合の処理
    try:
        # ファイルを書き込みモードで開く
        with open(pathname, 'w') as f:
            # 元の内容に新しいテキストを追加してファイルに書き込む
            f.write(contents + "\nAppending more text to this file!")
        # 追加後のファイルの内容を表示
        with open(pathname) as f:
            print(f"\n更新後の {pathname} の内容は次の通りです：\n{f.read()}")
    except:
        # ファイルの書き込みに失敗した場合のエラーメッセージ
        print(f"{pathname} への書き込みに失敗しました。")
