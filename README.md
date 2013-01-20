# PHP Application Server

Ziel des Projekts ist die Entwicklung eines multithreaded Application Servers für PHP, geschrieben in PHP.
Geschrieben in PHP um möglichst vielen Entwicklern aus der PHP Gemeinde die Mitarbeit zu ermöglichen und
das Projekt durch die möglichst breite Unterstützung der PHP Community als Standardlösung für Enterprise
Application im PHP Umfeld zu etablieren.

## Highlights

* Doctrine als Standard Persistence Provider
* Session Beans (Stateful, Stateless + Singleton)
* Message Beans
* Timer Service
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
PHP 5.3.x immer wieder Segmentation Faults auf sich allerdings auf das frühe Entwicklungsstadium der pthreads
Library zurückführen lassen. Aktuell verwenden wir für die Entwicklung PHP 5.4.10.

## Installation

Je nach Debian Version & PHP Konfiguration müssen vorab folgende Libraries müssen installiert werden:

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
        memcached

Einen guten Überblick über die Fehlermeldungen und die Libraries die für die Behebung notwendig sind findet man
unter http://www.robo47.net/text/6-PHP-Configure-und-Compile-Fehler#mcrypt.

PHP 5.4.x für Debian 6.0.x mit folgender Konfiguration kompilieren:

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