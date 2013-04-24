#
# Cookbook Name:: applicationserver
# Recipe:: default
#
# Robert Lemke <rl @ robertlemke . com>
#

#
# REQUIRED PACKAGES
#

%w{
  git-core
  apache2-mpm-prefork
  apache2-prefork-dev
  php5-dev
  libxml2
  libxml2-dev
  libbz2-dev
  libxpm4
  libxpm-dev
  libc-client2007e
  libc-client2007e-dev
  libmcrypt4
  libmcrypt-dev
  libmemcached-dev
  libjpeg62
  libjpeg62-dev
  libpcre3-dev
  libpng12-0
  libpng12-dev
  libfreetype6
  libfreetype6-dev
  g++
  re2c
}.each do |package_name|
  package "#{package_name}" do
    action :install
  end
end

#
# MYSQL
#

# Note that ruby-dev must be installed on the base box already in order to compile
# mysql::ruby which in turn is necessary for database::mysql

include_recipe 'mysql::client'
include_recipe 'mysql::server'
include_recipe 'database::mysql'

node.default['mysql']['tunable']['collation-server'] = "utf8_unicode_ci"
node.default['mysql']['tunable']['max_connections'] = "150"
node.default['mysql']['delete_anonymous_users'] = true
node.default['mysql']['delete_passwordless_users'] = true

#
# APACHE
#

node.default['apache']['package'] = "apache2-mpm-prefork"

include_recipe 'apache2'
include_recipe 'apache2::logrotate'
include_recipe 'apache2::mod_rewrite'

file "/var/www/index.html" do
  action :delete
end

file "/var/www/index.php" do
  content "<?php echo(gethostname()); ?>"
  owner "root"
  group "www-data"
  mode 00775
end

#
# PHP
#

node.default['php']['install_method'] = "source"
node.default['php']['url'] = 'http://us.php.net/distributions'
node.default['php']['version'] = '5.4.14'
node.default['php']['prefix_dir'] = '/usr'

node.default['php']['configure_options'] = [
  "--with-apxs2=/usr/bin/apxs2",
  "--prefix=/usr",
  "--with-libdir=lib64",
  "--with-config-file-path=/etc/php5/apache2",
  "--with-config-file-scan-dir=/etc/php5/conf.d",
  "--enable-libxml",
  "--enable-session",
  "--with-pcre-regex",
  "--enable-xml",
  "--enable-simplexml",
  "--enable-filter",
  "--disable-debug",
  "--enable-inline-optimization",
  "--disable-rpath",
  "--disable-static",
  "--enable-shared",
  "--with-pic",
  "--with-gnu-ld",
  "--with-mysql",
  "--with-gd",
  "--with-jpeg-dir",
  "--with-png-dir",
  "--enable-exif",
  "--with-zlib",
  "--with-bz2",
  "--with-curl",
  "--with-mysqli",
  "--with-freetype-dir",
  "--enable-sockets",
  "--enable-mbstring",
  "--enable-gd-native-ttf",
  "--enable-bcmath",
  "--enable-zip",
  "--with-pear",
  "--with-openssl",
  "--enable-phar",
  "--enable-pdo",
  "--with-pdo-mysql",
  "--with-mysqli",
  "--enable-maintainer-zts",
  "--enable-roxen-zts",
  "--with-mcrypt",
  "--with-tsrm-pthreads",
  "--enable-pcntl"
]

include_recipe 'php'

template "100-general-additions.ini" do
  path "/etc/php5/conf.d/100-general-additions.ini"
  source "100-general-additions.ini"
  owner "root"
  group "root"
  mode "0644"
  notifies :restart, resources(:service => "apache2")
end

#
# PTHREADS
#

bash "build php-pthreads" do
  cwd Chef::Config[:file_cache_path]
  code <<-EOF
  git clone https://github.com/krakjoe/pthreads.git
  cd pthreads
  phpize
  ./configure --enable-shared --enable-static
  make && make install
  EOF
  not_if "php -m |grep pthreads"
end

template "pthreads.ini" do
  path "/etc/php5/conf.d/pthreads.ini"
  source "pthreads.ini"
  owner "root"
  group "root"
  mode "0644"
  notifies :restart, resources(:service => "apache2")
end

#
# FURTHER PHP MODULES
#

file "#{node['apache']['dir']}/conf.d/php.conf" do
  action :delete
  backup false
end

apache_module "php5" do
  case node['platform_family']
  when "rhel", "fedora", "freebsd"
    conf true
    filename "libphp5.so"
  end
end

php_pear "memcached" do
  action :install
end

#
# Application Server
#

mysql_database "appserver_ApplicationServer" do
  connection ({:host => "localhost", :username => 'root', :password => 'password'})
  action :create
end

mysql_database_user 'appserver' do
  connection ({:host => "localhost", :username => 'root', :password => 'password'})
  password 'appserver'
  provider Chef::Provider::Database::MysqlUser
  action :create
end

mysql_database_user 'appserver' do
  connection ({:host => "localhost", :username => 'root', :password => 'password'})
  database_name 'foo'
  privileges [:all]
  action :grant
end

web_app "applicationserver" do

  template "app.conf.erb"

  server_name "applicationserver.devbox"
  server_aliases ["applicationserver.prodbox"]

  docroot "/var/www/applicationserver/src"

end
