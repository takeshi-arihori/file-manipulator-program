# File Manipulator 手順

## reverse inputpath outputpath:
#### inputpath にあるファイルを受け取り、outputpath に inputpath の内容を逆にした新しいファイルを作成します。
```
python file_manipulator.py reverse test.txt reversed_sample.txt
```

#### 出力
```
test.txt の内容を reversed_sample.txt に逆にしました。
```


## copy inputpath outputpath:
#### inputpath にあるファイルのコピーを作成し、outputpath として保存します。
```
python file_manipulator.py copy test.txt copied_sample.txt
```

#### 出力:
```
test.txt の内容を copied_sample.txt にコピーしました。
```

## duplicate-contents inputpath n:
#### inputpath にあるファイルの内容を読み込み、その内容を複製し、複製された内容を inputpath に n 回複製します。
```
python file_manipulator.py duplicate-contents test.txt 3
```

#### 出力
```
test.txt の内容を 3 回複製しました。
```

## replace-string inputpath needle newstring:
#### inputpath にあるファイルの内容から文字列 'needle' を検索し、'needle' の全てを 'newstring' に置き換えます。
```
python file_manipulator.py replace-string test.txt "Hello" "Hi"
```

#### 出力
```
test.txt の内容から Hello を Hi に置き換えました。
```
