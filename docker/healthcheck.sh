#!/bin/bash
set -e

# Apacheのステータスを確認
if ! service apache2 status > /dev/null; then
    exit 1
fi

# PHP-FPMのステータスを確認（もし使用している場合）
if command -v php-fpm > /dev/null; then
    if ! service php-fpm status > /dev/null; then
        exit 1
    fi
fi

# アプリケーションのルートディレクトリが存在することを確認
if [ ! -d "/var/www/html" ]; then
    exit 1
fi

exit 0 
