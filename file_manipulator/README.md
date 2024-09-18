# File Manipulator Program(1)

## 目的
ファイル操作のプロジェクトを通してOSとのやり取りを学ぶ

## 環境
- コンピュータ: VirtualBox 7.0
- OS: Ubuntu 24.04
- 使用言語: python
- エディタ: VSCode
- パッケージマネージャ: APTなど
- シェル: CLI

## 要件と作成に関する内容
- 引数という形で入力を受け取り、それらの引数に基づいて特定の操作を実行する。
- ファイルを操作するコマンドが含まれている。
- `reverse`: 入力ファイルの内容を反転させ、その結果を出力ファイルに書き出す。(操作するファイルのパス`fileinput`と、変更された新しいファイルを作成するためのパス`fileoutput`を受け取る。)
- `stdin`や`argv`を使用。
- 実行例：コマンド `python3 'file_manipulator.py','reverse','python_practice/data/test.txt','python_practice/dump/test-dumb.txt'` -> argvが持つリスト: `['file_manipulator.py','reverse','python_practice/data/test.txt','python_practice/dump/test-dumb.txt']`
- `open()`: file system を操作するための組み込み関数(ファイル記述子が作成され、その内容を追加、更新、削除できるようになる。)  
  ファイルオブジェクトを返す。ディスク上のファイルからの読み込みやファイルへの書き込み、パイプやソケットのようなメモリベースのファイルとのやり取りに使用できる。
- modeパラメータ(fileをどのように開くかを指定する)
  - `r`: fileを読み書き専用モードで開く
  - `w`: fileを書き込み専用モードで開く
    - fileが存在しない場合 -> fileが作成される
    - fileが存在する場合 -> 上書きされる
  - `x`: fileを書き込み専用モードで開く
    - fileが存在しない場合 -> fileが作成される
    - fileが存在する場合 -> エラーが発生する
  - `a`: fileを書き込み専用で開く
    - fileが存在しない場合 -> fileが作成される  
    fileはアペンドモードで開かれ、新しいデータがファイルの末尾に追加される。
  - `b`: fileをバイナリモードで開く(画像や音声ファイルなどのバイナリファイルに使用)
  - `t`: fileをテキストモードで開く(デフォルト)
  - `+`: 読み書き療法でファイルを開く

## プロジェクトの要件
以下のコマンドとその機能を提供する `file_manipulator.py` という Python スクリプトを作成してください。引数の入力が正しいかどうかをチェックするバリデータを必ず記述しましょう。

- `reverse inputpath outputpath:` inputpath にあるファイルを受け取り、outputpath に inputpath の内容を逆にした新しいファイルを作成します。
- `copy inputpath outputpath:` inputpath にあるファイルのコピーを作成し、outputpath として保存します。
- `duplicate-contents inputpath n:` inputpath にあるファイルの内容を読み込み、その内容を複製し、複製された内容を inputpath に n 回複製します。
- `replace-string inputpath needle newstring:` inputpath にあるファイルの内容から文字列 'needle' を検索し、'needle' の全てを 'newstring' に置き換えます。