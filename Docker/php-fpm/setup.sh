apt-get update
apt-get upgrade
apt-get install -y libav-tools
apt-get install -y libfreetype6-dev
apt-get install -y libpng-dev
apt-get install -y libjpeg-dev
docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/
docker-php-ext-install gd
docker-php-ext-enable gd



