FROM php:7.4-fpm

RUN apt-get update \
    && apt-get install -y --no-install-recommends vim curl debconf subversion git apt-transport-https apt-utils libpq-dev \
    build-essential locales acl mailutils wget zip unzip libzip-dev \ 
    libssl-dev libpng-dev zlib1g-dev libjpeg-dev libfreetype6-dev libwebp-dev libmcrypt-dev libjpeg62-turbo-dev \
    gnupg gnupg1 gnupg2 \
    nodejs npm
    
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql zip
COPY php.ini /usr/local/etc/php/php.ini
COPY php-fpm-pool.conf /usr/local/etc/php/pool.d/www.conf

RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer

RUN npm install -g yarn

RUN rm -rf /var/lib/apt/lists/*
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

WORKDIR /home/www/popp

EXPOSE 9000
CMD ["php-fpm"]

#A tester pour installer npm
#ENV NVM_DIR /usr/local/nvm
#ENV NODE_VERSION 9.2.0

# Install nvm with node and npm
#RUN curl https://raw.githubusercontent.com/creationix/nvm/v0.33.8/install.sh | bash \
#    && source $NVM_DIR/nvm.sh \
#    && nvm install $NODE_VERSION \
#    && nvm alias default $NODE_VERSION \
#    && nvm use default

#ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
#ENV PATH      $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH
