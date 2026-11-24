# Install Zabbix Server on FreeBSD
Repositories for teaching purposes at SPOS DK

![FreeBSD and ZabbixServer OSY AI](../../../Images/osy-FreeBSD-ZabbixServer.webp)

Repository pro vyuku na SPOS DK

## Automatická instalace Zabbix serveru 8.0 LTS na OS FreeBSD 14.3

Vychází z řešení [smejdil-osy-2026-zbx8-inst](https://github.com/sposdknl/smejdil-osy-2026-zbx8-inst).
Kombinuje binární balíčky z větve `latest` s kompilací jediného portu:

| Součást | Způsob instalace |
| --- | --- |
| Apache 2.4, PHP 8.4 (`mod_php84`) | binární balíček |
| MariaDB 12.3 LTS | binární balíček |
| Zabbix agent a frontend 8.0 | binární balíček |
| Závislosti a nástroje pro kompilaci | binární balíček |
| Zabbix server 8.0 | kompilace portu `net-mgmt/zabbix8-server` |

Balíček `zabbix8-server` v repository je sestavený s `mysql84-client`, který koliduje s klientem MariaDB.
Proto se server kompiluje s podporou MariaDB 12.3 (volba `DEFAULT_VERSIONS+= mysql=123m`).
Podpora SNMP je zapnutá, například pro monitoring [pfSense](../../pfSnese/README.md).

## Spuštění

Potřebujete alespoň 4 GB volné RAM pro VM.

```console
vagrant up
vagrant ssh
```

První spuštění trvá déle kvůli kompilaci serveru. Opakovaný `vagrant provision` již nainstalovaný
server znovu nekompiluje a databázi znovu neimportuje.

## Přihlášení

- Web: <http://127.0.0.1:8080/>, uživatel `Admin`
- Heslo se vygeneruje ve VM:

```console
sudo cat /var/db/zabbix-install/admin-password
```

## Monitoring

| Host | Kontrola |
| --- | --- |
| `spos-zabbix8` | šablona `FreeBSD by Zabbix agent` |
| `sposdk.cz` | `sposdk.cert.days` – počet dnů do konce platnosti TLS certifikátu |

## Ověření

```console
sudo service mysql-server status
sudo service zabbix_server status
sudo service zabbix_agentd status
sudo service apache24 status

/usr/local/sbin/zabbix_server -V
/usr/local/sbin/zabbix_agentd -t sposdk.cert.days
```

## Řešení potíží

- Chyba při `pkg` nebo `git clone`: ověřte síť VM a spusťte `vagrant provision`.
- Web není dostupný: `sudo apachectl configtest`.
- Server nebo agent neběží: zkontrolujte `/var/log/zabbix/`.

## Nápověda

- FreeBSD port [zabbix8-server](https://www.freshports.org/net-mgmt/zabbix8-server/)
- Zabbix 8.0 LTS [dokumentace](https://www.zabbix.com/documentation/8.0/en/)

...
