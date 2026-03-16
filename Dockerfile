FROM php:8.3-apache

# SQLite3 확장 설치
RUN apt-get update && \
    apt-get install -y libsqlite3-dev && \
    docker-php-ext-install pdo pdo_sqlite && \
    rm -rf /var/lib/apt/lists/*

# mod_rewrite 활성화
RUN a2enmod rewrite

# Apache AllowOverride 설정 (DocumentRoot 디렉토리)
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# 작업 디렉토리
WORKDIR /var/www/html

# 소스 코드 복사
COPY . /var/www/html/

# 데이터/업로드 디렉토리 생성 및 권한 설정
RUN mkdir -p data uploads && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 775 data uploads

EXPOSE 80
