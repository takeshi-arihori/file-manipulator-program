import random

def main():
    # ユーザーに最小値と最大値を入力してもらいます。
    n = int(input("最小値を入力してください: "))
    m = int(input("最大値を入力してください: "))

    # 最小値が最大値以下であることを確認します。
    while n > m:
        print("最小値は最大値以下である必要があります。")
        n = int(input("最小値を再入力してください: "))
        m = int(input("最大値を再入力してください: "))

    # n から m の範囲で乱数を生成します。
    random_number = random.randint(n, m)

    # ユーザーが乱数を推測するための試行回数を制限します。
    max_attempts = 5
    print(f"あなたは {max_attempts} 回の試行で数字を推測する必要があります。")

    for attempt in range(max_attempts):
        guess = int(input(f"{attempt + 1} 回目の推測: "))

        if guess == random_number:
            print("正解！")
            break
        elif guess < random_number:
            print("もっと大きい数字です。")
        else:
            print("もっと小さい数字です。")

        if attempt == max_attempts - 1:
            print(f"残念！正解は {random_number} でした。")

if __name__ == "__main__":
    main()
