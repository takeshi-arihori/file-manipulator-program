import sys
import markdown

def convert_markdown_to_html(input_file, output_file):
    # ファイルを読み込む
    with open(input_file, 'r') as md_file:
        md_content = md_file.read()

    # マークダウンをHTMLに変換
    html_content = markdown.markdown(md_content)

    # HTMLをファイルに書き込む
    with open(output_file, 'w') as html_file:
        html_file.write(html_content)

if __name__ == "__main__":
    # コマンドライン引数を取得
    if len(sys.argv) != 4 or sys.argv[1] != "markdown":
        print("Usage: python3 file-converter.py markdown inputfile.md outputfile.html")
        sys.exit(1)

    _, command, input_file, output_file = sys.argv

    # マークダウンをHTMLに変換
    convert_markdown_to_html(input_file, output_file)
