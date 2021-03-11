FROM php:7.4-cli

COPY . .

# Install Composer
RUN docker-php-ext-install pdo pdo_mysql && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENTRYPOINT ["php","-f","index.php"]

CMD ["php", "index.php"]