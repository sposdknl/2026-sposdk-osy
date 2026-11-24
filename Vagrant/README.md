# Vagrant: ukázkové virtuální stroje

![Více virtuálních strojů včetně pfSense propojených do jedné laboratorní sítě](../Images/vagrant-vm-gallery.webp)

V tomto adresáři jsou výchozí soubory `Vagrantfile` pro různé operační systémy. Každý soubor popisuje jeden virtuální stroj pro VirtualBox: použitý box, název stroje, jeho parametry a přesměrování portu na `127.0.0.1` hostitelského počítače (u pfSense webového rozhraní, jinak SSH). Soubory slouží jako příklady k úpravě podle konkrétní úlohy.

## Přehled souborů

| Soubor | Operační systém / box | Port na hostiteli |
| --- | --- | ---: |
| [`Vagrantfile-alma`](Vagrantfile-alma) | AlmaLinux 10 · `bento/almalinux-10` | `2201` |
| [`Vagrantfile-centos`](Vagrantfile-centos) | CentOS Stream 10 · `bento/centos-stream-10` | `2202` |
| [`Vagrantfile-rocky`](Vagrantfile-rocky) | Rocky Linux 10 · `bento/rockylinux-10` | `2203` |
| [`Vagrantfile-fedora`](Vagrantfile-fedora) | Fedora 43 · `bento/fedora-43` | `2204` |
| [`Vagrantfile-oraclelinux`](Vagrantfile-oraclelinux) | Oracle Linux 10 · `bento/oraclelinux-10` | `2205` |
| [`Vagrantfile-debian`](Vagrantfile-debian) | Debian 13 · `bento/debian-13` | `2206` |
| [`Vagrantfile-ubuntu`](Vagrantfile-ubuntu) | Ubuntu 24.04 LTS · `bento/ubuntu-24.04` | `2207` |
| [`Vagrantfile-opensuse`](Vagrantfile-opensuse) | openSUSE Leap 15 · `bento/opensuse-leap-15` | `2208` |
| [`Vagrantfile-kali`](Vagrantfile-kali) | Kali Linux Rolling · `kalilinux/rolling` | `2209` |
| [`Vagrantfile-fbsd`](Vagrantfile-fbsd) | FreeBSD 15.0-CURRENT · `freebsd/FreeBSD-15.0-CURRENT` | `2210` |
| [`Vagrantfile-bento-fbsd`](Vagrantfile-bento-fbsd) | FreeBSD 14.3-RELEASE · `bento/FreeBSD-14.3` | `2211` |
| [`Vagrantfile-pfsense`](Vagrantfile-pfsense) | pfSense CE 2.7.2 (FreeBSD 14) · `cs-c4300/pfsense` | `8888` (HTTPS) |

Soubory pro FreeBSD používají různé boxy (15.0-CURRENT a 14.3-RELEASE) a liší se i přesměrovaným SSH portem.

### pfSense

Po `vagrant up` je webové rozhraní firewallu na <https://localhost:8888> (uživatel `admin`, heslo `admin`). SSH port není pevně určen, přiděluje ho Vagrant; připojíte se příkazem `vagrant ssh`.

| Rozhraní | Síťová karta | Síť ve VirtualBoxu | IP adresa |
| --- | --- | --- | --- |
| OPT1 | `vtnet0` | NAT (Vagrant) | `10.0.2.15` |
| WAN | `em0` | host-only | DHCP `10.1.1.100`–`10.1.1.200` |
| LAN | `vtnet1` | vnitřní síť `intnet` | `192.168.1.1` |

Další stroj připojíte za firewall do sítě `intnet`. Podrobnosti a snímky obrazovky jsou v [Install/pfSnese](../Install/pfSnese/README.md).

## Jak příklad použít

Nainstalujte Vagrant a VirtualBox. Vybraný soubor zkopírujte do pracovního adresáře pod názvem `Vagrantfile`. Pro úlohu se Zabbix Agent 2 začněte Debianem 13. Po zkopírování upravte `Vagrantfile` podle zadání v [hlavním README](../README.md): doplňte automatickou instalaci agenta pomocí provisioningu. Zdejší soubor pro Debian je pouze výchozí konfigurace virtuálního stroje; Zabbix Agent 2 sám neinstaluje.

Příklad příkazů z kořene repozitáře (úpravu souboru proveďte před `vagrant up`):

```bash
cp Vagrant/Vagrantfile-debian Vagrantfile
vagrant validate
vagrant up
vagrant ssh
```

Virtuální stroj vypnete příkazem `vagrant halt`. Při kopírování jiného příkladu si zkontrolujte box, název stroje, přesměrovaný port a nastavení provideru.
