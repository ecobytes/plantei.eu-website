#!/bin/bash
start=`date +%s`

touch /home/vagrant/last-apt-update

# if [ ! -f "/home/vagrant/.composer/config.json" ];
#	then
#	  TOKEN=`cat /vagrant/github`
#   mkdir /home/vagrant/.composer/
#   touch /home/vagrant/.composer/config.json
#   echo '{ "config": {"github-oauth":{"github.com": ' > /home/vagrant/.composer/config.json
#   echo "\"$TOKEN\"" >> home/vagrant/.composer/config.json
#   echo '}}}' >> home/vagrant/.composer/config.json
#   rm /vagrant/github
# fi;

lastUpdate=$(</home/vagrant/last-apt-update)

apt-get update
#
#if [ $((start-lastUpdate)) -gt 86400 ];
#then
#  apt-get -y dist-upgrade
#  apt-get -y autoremove
#fi
#
#echo "Provisioning virtual machine..."
#
apt-get install -y screen
echo "Installing git"
apt-get install git -y

echo "Installing Nginx"
apt-get install nginx -y

echo "Installing PHP"
apt-get install php5-common php5-dev php5-cli php5-fpm -y

echo "Installing PHP extensions"
apt-get install curl php5-curl php5-gd php5-mcrypt php5-mysql php5-imagick -y

php5enmod mcrypt

mv '/etc/php5/fpm/php.ini' '/etc/php5/fpm/php.original'
cp '/usr/share/php5/php.ini-development' '/etc/php5/fpm/php.ini'

# Run php-fpm as vagrant (file permissions)
sed -i -e 's:^user.*:user = vagrant:' -e 's:^group.*:group = vagrant:' /etc/php5/fpm/pool.d/www.conf

service php5-fpm restart


# Create symlink inside /var/www
ln -s /vagrant/ /var/www/plantei.eu

apt-get -y install postgresql php5-pgsql


DBNAME="localdatabase"
DBUSER="root"
DBPASS="localpassword"

sudo -u postgres createuser "${DBUSER}"
sudo -u postgres createdb -O "${DBUSER}" "${DBNAME}"
sudo -u postgres psql -c "ALTER USER ${DBUSER} WITH PASSWORD '${DBPASS}';"

# Can test connection with:
# psql -h 127.0.0.1 -U planteieu -W -c 'select * from pg_roles'


echo "Configuring Nginx"
rm /etc/nginx/sites-available/nginx_vhost 2> /dev/null
rm /etc/nginx/sites-enabled/nginx_vhost 2> /dev/null
rm /etc/nginx/sites-available/default 2> /dev/null
rm /etc/nginx/sites-enabled/default 2> /dev/null
cp /vagrant/provision/config/nginx_vhost /etc/nginx/sites-available/caravel

if [ ! -h "/etc/nginx/sites-enabled/" ]; then
  ln -s /etc/nginx/sites-available/caravel /etc/nginx/sites-enabled/
fi

rm -rf /etc/nginx/sites-available/default

service nginx restart

sudo -i -u vagrant bash /vagrant/provision/config_vagrant.sh

if [ ! "$(grep vagrant /etc/rc.local)" ]; then
  sed -i "s:^\(exit 0\):su - vagrant -c /home/vagrant/bin/start_browser-sync.sh \n\1:" /etc/rc.local
fi

if [ ! -f /home/vagrant/bin/start_browser-sync.sh ]; then
  sudo -u vagrant cp /vagrant/provision/config/start_browser-sync.sh /home/vagrant/bin
  chmod +x /home/vagrant/bin/start_browser-sync.sh
fi

/etc/rc.local

end=`date +%s`
runtime=$((end-start))
echo $start > /home/vagrant/last-apt-update
echo "install took $runtime seconds"
