FROM php:7.4-alpine

COPY . .

# Install Composer
RUN apk update && apk add zlib-dev libpng-dev libzip-dev icu-dev libxml2-dev libxslt-dev oniguruma-dev
 #        libfreetype6-dev \
 #        libjpeg62-turbo-dev \
     #    libpng-dev \
   #      zlib1g-dev libicu-dev g++ \
       #  libxml2-dev

RUN docker-php-ext-install pdo pdo_mysql 
RUN docker-php-ext-install gd
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install zip
RUN docker-php-ext-install intl
RUN docker-php-ext-install xml
RUN docker-php-ext-install xsl
RUN docker-php-ext-install mbstring

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer 
RUN php /usr/local/bin/composer install

ENTRYPOINT ["php","-f","index.php"]

CMD ["php", "index.php"]