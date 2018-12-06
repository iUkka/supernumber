FROM 	ubuntu:12.04
MAINTAINER Aleksei Korolev <zme-yukka@yandex.ru>
RUN     apt-get update > /dev/null
RUN	apt-get install python-software-properties -y > /dev/null
RUN	add-apt-repository ppa:ondrej/php > /dev/null
RUN 	add-apt-repository ppa:ondrej/apache2> /dev/null
RUN     apt-get update > /dev/null
RUN 	apt-get install -y build-essential make php5-dev php-pear apache2 wget libapache2-mod-php5 > /dev/null
RUN	wget --no-check-certificate -q http://ponce.cc/slackware/sources/repo/PDFlib-Lite-7.0.5p3.tar.gz -P /tmp \
	&& tar -xzf /tmp/PDFlib-Lite-7.0.5p3.tar.gz -C /tmp 

WORKDIR /tmp/PDFlib-Lite-7.0.5p3
RUN ./configure && make && make install
COPY ./www /var/www
RUN     echo "/usr/local/" | pecl install pdflib-3.0.1
RUN     echo "extension=pdf.so" >> /etc/php5/cli/php.ini \
        && echo "extension=pdf.so" >> /etc/php5/apache2/php.ini
#Clean
RUN	rm -rf /tmp/PDF*
RUN	apt-get --purge remove  python-software-properties build-essential make wget -y \
	&& apt-get autoremove -y
 
#Apache
#Mod default
RUN	sed -i -e 's/ServerAdmin webmaster@localhost/ServerAdmin webmaster@localhost\n        DirectoryIndex  supernumber.php/' /etc/apache2/sites-available/default \
	&& echo "ServerName localhost" >> /etc/apache2/apache2.conf
CMD	/usr/sbin/apache2ctl -D FOREGROUND

EXPOSE 80

