#!/bin/bash
start=`date +%s`

touch /home/vagrant/last-apt-update

#if [ ! -f "/home/vagrant/.composer/config.json" ]; 
#	then 
#			 TOKEN=`cat /vagrant/github`
#       mkdir /home/vagrant/.composer/
#       touch /home/vagrant/.composer/config.json
#			 echo '{ "config": {"github-oauth":{"github.com": ' > /home/vagrant/.composer/config.json
#			 echo "\"$TOKEN\"" >> home/vagrant/.composer/config.json
#			 echo '}}}' >> home/vagrant/.composer/config.json
#			 rm /vagrant/github
#fi;

lastUpdate=$(</home/vagrant/last-apt-update)

if [ $((start-lastUpdate)) -gt 86400 ]; then apt-get update; apt-get -y dist-upgrade; fi;

 
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
service php5-fpm restart

apt-get install debconf-utils -y

debconf-set-selections <<< "mysql-server mysql-server/root_password password localpassword"
 
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password localpassword"

echo "Installing Mysql"
apt-get install mysql-server -y


sed -i "s/^bind-address/#bind-address/" /etc/mysql/my.cnf
mysql -u root -plocalpassword -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'localpassword' WITH GRANT OPTION; FLUSH PRIVILEGES;"
sudo /etc/init.d/mysql restart
mysql -uroot -plocalpassword -e 'create database localdatabase;' 2> /dev/null
mysql -uroot -plocalpassword -e 'create database testing;' 2> /dev/null

echo "Installing nodejs npm bower phantomjs and gulp"
apt-get install nodejs npm phantomjs -y
if [ ! -f "/usr/bin/node" ]; then ln /usr/bin/nodejs /usr/bin/node ; fi;

if [ ! -f "/usr/local/bin/bower" ]; then npm install -g bower; fi;
if [ ! -f "/usr/local/bin/gulp" ]; then npm install -g gulp; fi;
#Windows does not like npm long path names, so lets hide them
if [ ! -h "/vagrant/node_modules" ]; then ln -s /home/vagrant/node_modules /vagrant/node_modules; mkdir /home/vagrant/node_modules; fi

echo "Configuring Nginx"
rm /etc/nginx/sites-available/nginx_vhost 2> /dev/null
rm /etc/nginx/sites-enabled/nginx_vhost 2> /dev/null
cp /var/www/provision/config/nginx_vhost /etc/nginx/sites-available/caravel
 
if [ ! -h "/etc/nginx/sites-enabled/" ]; then ln -s /etc/nginx/sites-available/caravel /etc/nginx/sites-enabled/; fi
 
rm -rf /etc/nginx/sites-available/default
 
service nginx restart

echo "installing composer"
if [ ! -f "/usr/local/bin/composer" ]; then echo "installing composer"; curl -sS https://getcomposer.org/installer | php; mv composer.phar /usr/local/bin/composer; fi

echo "installing laravel"
su -c 'composer global require "laravel/installer=~1.1"' vagrant

#link for web directory
if [ ! -h "/home/vagrant/www" ]; then ln -s /vagrant/src /home/vagrant/www; fi;



 if ! grep -q 'cd /vagrant' "/home/vagrant/.profile"; then
   echo 'cd /vagrant' >> /home/vagrant/.profile
 fi

if [ ! -f "/vagrant/src/.env" ]; then cp /vagrant/src/server/.env.example /vagrant/src/server/.env ; fi;


 if ! grep -q 'PATH="~/.composer/vendor/bin:/vagrant/bin:$PATH"' "/home/vagrant/.profile"; then
   echo 'PATH="~/.composer/vendor/bin:/vagrant/bin:$PATH"' >> /home/vagrant/.profile
 fi

 updatedb

cp /vagrant/provision/config/99-caravel /etc/update-motd.d/
chmod +x /etc/update-motd.d/99-caravel

cd /vagrant
npm install
cd /vagrant/src/server
composer update



end=`date +%s`
runtime=$((end-start))
echo $start > /home/vagrant/last-apt-update
echo "install took $runtime seconds"