#!/usr/bin/env bash

# Aktualizace repository
sudo pkg update

# Instalace balicku mariadb server 12.3 LTS
sudo pkg install -y mariadb123-server

# Povoleni sluzby mariadb server
sudo /usr/local/etc/rc.d/mysql-server enable

# Restart sluzby mariadb server
sudo /usr/local/etc/rc.d/mysql-server restart

# EOF
