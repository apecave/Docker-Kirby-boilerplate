#!/bin/sh

# insert values for variables in nginx config and copy to nginx conf.d folder
envsubst '$VIRTUAL_HOST:$ENABLE_PW' < /docker/default.conf > /etc/nginx/conf.d/default.conf 

#if the dockermachine needs to login anywhere with key credentials, they can be set up here for both the root and nobody users
#ensure ssh keys exist for nobody user
# cp -R /docker/ssh /.ssh/
# chmod 600 /.ssh/id_rsa
# chown nobody:nobody /.ssh/id_rsa

#ensure ssh keys exist for root user
# cp -R /docker/ssh/. ~/.ssh/ 
# chmod 600 ~/.ssh/id_rsa

#set ownership for volumes
#kirby needs these dirs writeable by the app
chown -R nobody:nobody public/media
chown -R nobody:nobody storage
chown -R nobody:nobody content

#install php packages either by running 'composer install' in the container, or uncomment the line below and it will install packages when you run the container
# composer install --no-dev

# run these daemons
crond
nginx
php-fpm -F
