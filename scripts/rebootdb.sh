#!/bin/bash

SCRIPTDIR="$(dirname $(readlink -f $0))"

cd "${SCRIPTDIR}"

. ../src/server/.env

sudo -u postgres dropdb "${DB_DATABASE}"
sudo -u postgres createdb -O "${DB_USERNAME}"  "${DB_DATABASE}"

cd ../src/server
php artisan migrate
php artisan -vvvv db:seed

sudo service php5-fpm restart

