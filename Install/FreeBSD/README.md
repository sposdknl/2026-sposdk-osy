# Install FreeBSD
Repositories for teaching purposes at SPOS DK

Repository pro vyuku na SPOS DK

## Install OS

- Budeme instalovat OS [FreeBSD](https://www.freebsd.org/)

Nainstalujte si OS [FreeBSD 14.3](https://www.freebsd.org/releases/14.3R/announce/)
pomocí Vagrant boxu [bento/freebsd-14.3](https://portal.cloud.hashicorp.com/vagrant/discover/bento/freebsd-14.3).
Budete potřebovat [Vagrant](https://developer.hashicorp.com/vagrant/install),
[VirtualBox](https://www.virtualbox.org/wiki/Downloads) (verzi podporovanou Vagrantem)
a verzovací nástroj [Git](https://github.com/git-for-windows/git/releases/latest) pro získání souborů tohoto projektu.

> FreeBSD 14.3 je od 30. 6. 2026 bez podpory a balíčky se sestavují pro novější 14.x.
> Vagrantfile proto nastavuje `IGNORE_OSVERSION` v `/usr/local/etc/pkg.conf`. Box `bento/freebsd-14.3` je funkční, pro FreeBSD 15 ani 15.1 box Bento zatím neexistuje.

```console
git clone https://github.com/sposdknl/2026-sposdk-osy/
cd 2026-sposdk-osy/Install/FreeBSD

vagrant up
vagrant ssh
```

## Konfigurace

- Po instalaci FreeBSD se připojíme příkazem `vagrant ssh` a na uživatele root přepneme pomocí `sudo -i`.
- Provedeme stažení portů a zkompilujeme editor joe.

```console
# portsnap byl odstranen, porty se ziskavaji pomoci gitu
pkg install -y git
git clone --depth 1 -b 2026Q4 https://git.FreeBSD.org/ports.git /usr/ports

# Aktualizace portu
git -C /usr/ports pull
pkg version -v | grep upd

echo "BATCH=yes" >> /etc/make.conf
# FreeBSD 14.3 je po EOL
echo "ALLOW_UNSUPPORTED_SYSTEM=yes" >> /etc/make.conf

# Instalace editoru joe z repository z balicku
pkg install -y joe 
pkg install -y gmake

cd /usr/ports
make fetchindex

pkg info | grep joe
joe-4.8_1,1                    Joe's Own Editor

pkg delete joe-4.8_1,1
Checking integrity... done (0 conflicting)
Deinstallation has been requested for the following 1 packages (of 0 packages in the universe):

Installed packages to be REMOVED:
	joe: 4.8_1,1

Number of packages to be removed: 1

The operation will free 2 MiB.

Proceed with deinstalling packages? [y/N]: y
[1/1] Deinstalling joe-4.8_1,1...
[1/1] Deleting files for joe-4.8_1,1: 100%

# Instalace editoru joe z portu - vlastni balicek
cd /usr/ports
make search name=joe

cd /usr/ports/editors/joe
make package
ls -l work*/pkg/

pkg install work*/pkg/joe-*.pkg

pkg delete joe-4.8_1,1

# Instalace editoru joe z portu - kompilace a instalace
cd /usr/ports/editors/joe
make install clean

# Zmena root hesla
echo freebsd | pw mod user root -h 0

# Instalace dalsich programu
pkg install -y mc
pkg install -y portupgrade
pkg install -y nmap
pkg install -y apache24
```

## Další instalace

- [Apache](./Apache/)
- [MariaDB 12.3 LTS](./MariaDB/)
- [Zabbix Agent 8.0 LTS](./ZabbixAgent/)
- [Zabbix Server 8.0 LTS](./ZabbixServer/)

...
