FROM php:8.3-apache-bullseye

# 必要なパッケージのインストール
RUN apt-get update && apt-get install -y \
  git \
  curl \
  libpng-dev \
  libonig-dev \
  libxml2-dev \
  zip \
  unzip \
  nodejs \
  npm

# PHP拡張機能のインストール
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Xdebugのインストール
RUN pecl install xdebug \
  && docker-php-ext-enable xdebug \
  && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini

# Node.jsの最新版をインストール
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
  && apt-get install -y nodejs

# 作業ディレクトリの設定と必要なディレクトリの作成
WORKDIR /var/www/html
RUN mkdir -p /var/www/html/public \
  && mkdir -p /var/www/html/storage \
  && mkdir -p /var/www/html/bootstrap/cache

# Apache設定の最適化
RUN a2enmod rewrite

# Apache設定ファイルの作成
RUN echo '<VirtualHost *:80>\n\
  ServerAdmin webmaster@localhost\n\
  DocumentRoot /var/www/html/public\n\
  \n\
  <Directory /var/www/html/public>\n\
  AllowOverride All\n\
  Require all granted\n\
  Options Indexes FollowSymLinks\n\
  </Directory>\n\
  \n\
  ErrorLog ${APACHE_LOG_DIR}/error.log\n\
  CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
  </VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# アプリケーションディレクトリの権限設定
RUN chown -R www-data:www-data /var/www/html \
  && chmod -R 755 /var/www/html/storage \
  && chmod -R 755 /var/www/html/bootstrap/cache

# 開発用の設定
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/xdebug.ini

# ヘルスチェックスクリプトの作成
RUN echo '#!/bin/bash\n\
  set -e\n\
  \n\
  # Apacheのステータスを確認\n\
  if ! service apache2 status > /dev/null; then\n\
  exit 1\n\
  fi\n\
  \n\
  # PHP-FPMのステータスを確認（もし使用している場合）\n\
  if command -v php-fpm > /dev/null; then\n\
  if ! service php-fpm status > /dev/null; then\n\
  exit 1\n\
  fi\n\
  fi\n\
  \n\
  # アプリケーションのルートディレクトリが存在することを確認\n\
  if [ ! -d "/var/www/html" ]; then\n\
  exit 1\n\
  fi\n\
  \n\
  exit 0' > /usr/local/bin/healthcheck \
  && chmod +x /usr/local/bin/healthcheck

HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
  CMD healthcheck || exit 1
