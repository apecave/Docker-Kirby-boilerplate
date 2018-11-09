FROM nginx:1.14.0-alpine
MAINTAINER Paul Wagner, paul@oxfordservicesinc.com

#core apk package installs
RUN apk update && \
    apk upgrade && \
    apk add --no-cache --virtual .build-deps\
    php7\
    php7-session\
    php7-fpm\
    php7-opcache\
    php7-curl\
    php7-dom\
    php7-openssl\
    php7-xml\
    php7-json\
    php7-phar\
    php7-gd\
    php7-mbstring\
    php7-imagick\
    php7-ctype\
    php7-tokenizer\
    php7-xmlwriter\
    rsync\
    git\
    openssh-client\
    curl

# Small fixes
RUN ln -s /etc/php7 /etc/php && \
    ln -sf /usr/bin/php7 /usr/bin/php && \
    ln -s /usr/sbin/php-fpm7 /usr/bin/php-fpm && \
    ln -s /usr/lib/php7 /usr/lib/php && \
    sed -i "s|;*upload_max_filesize =.*|upload_max_filesize = 100M;|i" /etc/php7/php.ini  && \
    sed -i "s|;*post_max_size =.*|post_max_size = 100M|i" /etc/php7/php.ini  && \
    rm -fr /var/cache/apk/*

# Install composer global bin
RUN curl -sS https://getcomposer.org/installer | php &&\
    mv composer.phar /usr/local/bin/composer

RUN mkdir /app 
WORKDIR /app 


COPY ./docker /docker
COPY ./docker/.htpasswd /etc/nginx/
RUN chmod +x /docker/docker-entrypoint.sh
COPY ./ /app

RUN find . -name "*.git" -type d -print0 | xargs -0 /bin/rm -rf && rm -f ./docker/.htpasswd

# refactor for kirby3 or remove
#RUN crontab -l | { cat; echo "*       *       *       *       *       php -q /app/site/plugins/queue-for-kirby/worker.php > /var/log/cron-worker.log"; } | crontab -

ENV VIRTUAL_HOST localhost

#disable pw by default; override with docker-compose
ENV ENABLE_PW "false"

ENTRYPOINT ["/docker/docker-entrypoint.sh"]

ENV PORT 80
EXPOSE 80