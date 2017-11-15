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

if [ $((start-lastUpdate)) -gt 86400 ];
then
  apt-get -y dist-upgrade
  apt-get -y autoremove
fi

echo "Provisioning virtual machine..."

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

#apt-get install debconf-utils -y


#START ADDITIONS
# Change permissions on variable data files
chmod -R a+rw /vagrant/src/server/{bootstrap/cache,storage}

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






# echo "Installing nodejs npm bower phantomjs and gulp"
# curl --silent --location https://deb.nodesource.com/setup_4.x | sudo bash -
# apt-get install nodejs -y
# if [ ! -f "/usr/bin/node" ]; then ln /usr/bin/nodejs /usr/bin/node ; fi;
# 
# if [ ! -f "/usr/local/bin/bower" ]; then npm install -g bower; fi;
# if [ ! -f "/usr/local/bin/gulp" ]; then npm install -g gulp; fi;
# #Windows does not like npm long path names, so lets hide them
# if [ ! -h "/vagrant/node_modules" ]; then ln -s /home/vagrant/node_modules /vagrant/node_modules; mkdir /home/vagrant/node_modules; fi
# 
# 
# echo "installing composer"
# if [ ! -f "/usr/local/bin/composer" ]; then echo "installing composer"; curl -sS https://getcomposer.org/installer | php; mv composer.phar /usr/local/bin/composer; fi
# 
# echo "installing laravel"
# su -c 'composer global require "laravel/installer=~1.1"' vagrant
# 
# #link for web directory
# if [ ! -h "/home/vagrant/www" ]; then ln -s /vagrant/src /home/vagrant/www; fi;
# 
# 
# 
#  if ! grep -q 'cd /vagrant' "/home/vagrant/.profile"; then
#    echo 'cd /vagrant' >> /home/vagrant/.profile
#  fi
# 
# if [ ! -f "/vagrant/src/.env" ]; then cp /vagrant/src/server/.env.example /vagrant/src/server/.env ; fi;
# 
# 
#  if ! grep -q 'PATH="~/.composer/vendor/bin:/vagrant/bin:$PATH"' "/home/vagrant/.profile"; then
#    echo 'PATH="~/.composer/vendor/bin:/vagrant/bin:$PATH"' >> /home/vagrant/.profile
#  fi
# 
#  updatedb
# 
# cp /vagrant/provision/config/99-caravel /etc/update-motd.d/
# chmod +x /etc/update-motd.d/99-caravel
# 
# cd /vagrant
# npm install
# npm update
# cd /vagrant/src/server
# composer update
# 
# echo ">>> Installing Mailhog (testing email server)"
# 
# # Download binary from github
# mailHogURL=$(curl -s https://api.github.com/repos/mailhog/MailHog/releases | grep browser_download_url | grep 'linux_386' | head -n 1 | cut -d '"' -f 4)
# 
# wget -O /usr/local/bin/mailhog "$mailHogURL"
# 
# # Make it executable
# chmod +x /usr/local/bin/mailhog
# 
# # Make it start on reboot
# sudo tee /etc/init/mailhog.conf <<EOL
# description "Mailhog"
# start on runlevel [2345]
# stop on runlevel [!2345]
# respawn
# pre-start script
#     exec su - vagrant -c "/usr/bin/env /usr/local/bin/mailhog > /dev/null 2>&1 &"
# end script
# EOL
# 
# # Start it now in the background
# sudo service mailhog start
# 
# 
end=`date +%s`
runtime=$((end-start))
echo $start > /home/vagrant/last-apt-update
echo "install took $runtime seconds"
