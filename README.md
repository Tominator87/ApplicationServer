# PHP Application Server

Ziel des Projekts ist die Entwicklung eines multithreaded Application Servers für PHP, geschrieben in PHP.
Geschrieben in PHP um möglichst vielen Entwicklern aus der PHP Gemeinde die Mitarbeit zu ermöglichen und
das Projekt durch die möglichst breite Unterstützung der PHP Community als Standardlösung für Enterprise
Application im PHP Umfeld zu etablieren.

## Highlights

* Doctrine als Standard Persistence Provider
* Session Beans (Stateful, Stateless + Singleton)
* Message Beans
* Timer Service (tbd)
* Servlet Engine
* Integrierte Message Queue
* Einfache Skalierung
* Webservices

## Technical Features

* Verwendung der phtreads Library von Joe Watkins (https://github.com/krakjoe/pthreads)
* Verwendung von DI & AOP innerhalb der jeweiligen Container
* Einsatz von Annotations zur Konfiguration der Beans
* Configration by Exception (optional Verwendung von Deployment Descriptoren möglich)

Die Implementierung einer Webanwendung sowie deren Betrieb im PHP Application Server muss so einfach wie möglich
sein. Hierzu werden zum Einen, wann immer möglich, bereits bestehenden Komponenten als Standardlösung, so z. B.
Doctrine, verwendet, zum Anderen darf, durch das Paradigma Configuration by Exception, für den Betrieb einer
Anwendung nur ein Minimum an Konfiguration notwendig sein. So wird breits durch das Standardverhalten der
jeweiligen Kompenenten ein Großteil der Verwendungsfälle abgedeckt wodurch sich der Enwickler häufig keine
deklarativen Angaben zur Konfiguration machen muss.

Um eine möglichst breite Community anzusprechen muss die Architektur des PHP Application Servers so aufgebaut werden,
das über Adapter eine möglichst große Anzahl an bereits bestehenden Anwendungen einfach migriert werden können.
Weiterhin wird die zukünftige Entwicklung von Webanwendungen auf Basis aller relevanten PHP Frameworks durch die
Bereitstellung von Libraries unterstützt.

## Requirements

* PHP5.3+ on x64 or x86
* ZTS Enabled (Thread Safety)
* Posix Threads Implementierung
* Memcached (2.1+)

Die aktuelle Version bisher lediglich auf Mac OS X 10.7+ getestet. Aufgrund der verwendeten Komponenten sollten
allerdings auch auf anderen Plattformen der Betrieb möglich sein.

### Supported PHP Versionen

Der PHP Application Server sollte auf jeder PHP Version ab 5.3.0 laufen, allerdings traten bei diversen Tests mit
PHP 5.3.x immer wieder Segmentation Faults auf die sich allerdings auf das frühe Entwicklungsstadium der pthreads
Library zurückführen lassen. Aktuell wird für die Entwicklung PHP 5.4.10 verwendet.

## Installation

Je nach Debian Version & PHP Konfiguration müssen vorab folgende Libraries müssen installiert werden:

```
apt-get install \
    apache2-prefork-dev \
    php5-dev \
    libxml2 \
    libxml2-dev \
    libcurl3-dev \
    libbz2-dev \
    libxpm4 \
    libxpm-dev \
    libc-client2007e \
    libc-client2007e-dev \
    libmcrypt4 \
    libmcrypt-dev \
    libmemcached-dev \
    libjpeg62 \
    libjpeg62-dev \
    libpng12-0 \
    libpng12-dev \
    libfreetype6 \
    libfreetype6-dev \
    g++
```

Einen guten Überblick über die Fehlermeldungen und die Libraries die für die Behebung notwendig sind findet man
unter http://www.robo47.net/text/6-PHP-Configure-und-Compile-Fehler.

PHP 5.4.x für Debian 6.0.x mit folgender Konfiguration kompilieren:

```
./configure \
    --with-apxs2=/usr/bin/apxs2 \
    --prefix=/usr \
    --with-libdir=lib64 \
    --with-config-file-path=/etc/php5/apache2 \
    --with-config-file-scan-dir=/etc/php5/conf.d \
    --enable-libxml \
    --enable-session \
    --with-pcre-regex=/usr \
    --enable-xml \
    --enable-simplexml \
    --enable-filter \
    --disable-debug \
    --enable-inline-optimization \
    --disable-rpath \
    --disable-static \
    --enable-shared \
    --with-pic \
    --with-gnu-ld \
    --with-mysql \
    --with-gd \
    --with-jpeg-dir \
    --with-png-dir \
    --with-xpm-dir \
    --enable-exif \
    --with-zlib \
    --with-bz2 \
    --with-curl \
    --with-ldap \
    --with-mysqli \
    --with-freetype-dir \
    --enable-soap \
    --enable-sockets \
    --enable-calendar \
    --enable-ftp \
    --enable-mbstring \
    --enable-gd-native-ttf \
    --enable-bcmath \
    --enable-zip \
    --with-pear \
    --with-openssl \
    --with-imap \
    --with-imap-ssl \
    --with-kerberos \
    --enable-phar \
    --enable-pdo \
    --with-pdo-mysql \
    --with-mysqli \
    --enable-maintainer-zts \
    --enable-roxen-zts \
    --with-mcrypt \
    --with-tsrm-pthreads \
    --enable-pcntl
```

Anschließend muss die pthreads Extension aus dem Github Repository ausgecheckt, compiliert und installiert werden:

```
git clone https://github.com/krakjoe/pthreads.git
cd pthreads
phpize
./configure --enable-shared --enable-static
make && make install
```

Nicht vergessen die Extension in der php.ini mit:

```
extension = pthreads.so
```

zu aktivieren.

Der PHP Application Server benötigt in der aktuellen Version Memcached. Um die hierfür für PHP notwendige PECL
Extension installieren zu können benötigen wir libmemcache. libmemcache herunterladen, kompilieren + installeren:

```
wget https://launchpad.net/libmemcached/1.0/1.0.15/+download/libmemcached-1.0.15.tar.gz
tar xvfz libmemcached-1.0.15.tar.gz
cd libmemcache-1.0.15
./configure
make
make install
```

Anschließend kann mit:


```
pecl install memcached
```

die PECL Extension installieret werden. Auch diese muss in der php.ini aktiviert werden.

Die Sourcen werden als tar.gz Archiv ausgeliefert. Basis des PHP Application Servers ist ein internes PEAR
Repository über das zusätzliche Pakete wie z. B. Doctrine installiert werden können. Im nächsten Schritt
werden die Application Server Sourcen im Apache Root Verzeichnis entpack, installiert und das PEAR Repository
initialisiert:


```
cd /var/www
tar xvfz appserver-0.4.0beta.tar.gz
ln -s appserver-0.4.0beta appserver
cd appserver
chmod +x bin/webapp
bin/webapp setup
```

Da als Standard Persistence Provider Doctrine zum Einsatz kommt, die Sourcen jedoch nicht mit dem PHP Application
Server ausgeliefert werden erfolgt die Installation im integrierten PEAR Repository mit:

```
bin/webapp channel-discover pear.doctrine-project.org
bin/webapp install doctrine/DoctrineORM
```

Anlegen der Datenbank über die MySQL Konsole mit:

```
create database appserver_ApplicationServer;
grant all on appserver_ApplicationServer.* to "appserver"@"localhost" identified by "appserver";
flush privileges;
```

Abschließend kann der PHP Application Server mit:

```
php -f server.php
```

gestartet und die notwendigen Tabellen können durch Aufruf der URL im Browser:

```
http://<appserver-ip>/appserver/examples/index.php?action=createSchema
```

erzeugt werden. Über die URL:

```
http://<appserver-ip>/appserver/examples/
```

ist eine kleine Beispiel Anwendung erreichbar die die Funktionalität des PHP Application Servers anhand eines CRUD
Beispiels demonstriert. Zusätzlich ist über die URL:

```
http://<appserver-ip>:8586/example/hello-world.do
```

Ein rudimentäres Servlet ansprechbar. Allerdings wird im aktuellen Stand hier lediglich statischer Content ausgegeben.

Über die URL:

```
http://<appserver-ip>:8586/example/index.php
```

kann ein PHP Skript, analog zur Ausführung über den Apache, aufgerufen werden. Hierbei wird im Hintergrund ein
Servlet (PhpServlet) aufgerufen, das eine PHP Runtime Umgebung bereitstellt. Allerdings handelt es sich hierbei
lediglich um eine sehr rudimentäre Implementierung, so werden z. B. globale Variablen wie $_REQUEST noch nicht
bereitgestellt.

## Usage

### Connect to the PersistenceContainer

Der Verbindungsaufbau zum PersistenceContainer erfolgt über eine Client Library. Hierbei ist in der aktuellen
Version wichtig, dass bereits eine Session existiert. Nach dem Verbindungsaufbau kann über die lookup() Methode
ein Proxyobjekt des gewünschten SessionBeans geholt werden:

```php
<?php

// initialize the session
session_start();

// initialize the connection, and the initial context
$connection = Factory::createContextConnection();
$session = $connection->createContextSession();
$initialContext = $session->createInitialContext();

// lookup the remote processor implementation
$processor = $initialContext->lookup('TechDivision\Example\Services\SampleProcessor');

// load all sample entities
$allEntities = $processor->findAll();

?>
```