# Install Zabbix Agentd on FreeBSD
Repositories for teaching purposes at SPOS DK

![FreeBSD and ZabbixAgent OSY AI](../../../Images/osy-FreeBSD-ZabbixAgent.webp)

Repository pro vyuku na SPOS DK

## Automatická instalace Zabbix Agentd na OS FreeBSD 14.3

- Vagrantfile obsahuje sekci pro aplikaci příkazů pro instalaci monitorovacího
[Zabbix Agent](https://www.zabbix.com/) 8.0 LTS.

- Klasický agent (`zabbix_agentd`) se kompiluje z portu `net-mgmt/zabbix8-agent`.
- Nástroje a závislosti se předem nainstalují jako binární balíčky, takže se kompiluje pouze agent.
- Při opakovaném `vagrant provision` se již nainstalovaný agent znovu nekompiluje.

### Instalace závislostí z balíčků

```console
mkdir -p /usr/local/etc/pkg/repos
cat > /usr/local/etc/pkg/repos/FreeBSD.conf <<'EOF'
FreeBSD: { url: "pkg+https://pkg.FreeBSD.org/${ABI}/latest", mirror_type: "srv", enabled: yes }
EOF

pkg update -f
pkg install -y git pkgconf muon samurai gmake curl pcre2
```

### Kompilace a instalace Zabbix Agent

```console
git clone --depth 1 https://git.FreeBSD.org/ports.git /usr/ports

echo "BATCH=yes" >> /etc/make.conf
echo "ALLOW_UNSUPPORTED_SYSTEM=yes" >> /etc/make.conf

cd /usr/ports/net-mgmt/zabbix8-agent
make install-missing-packages
make package
make install
make clean

pkg info zabbix8-agent
/usr/local/sbin/zabbix_agentd -V

/usr/local/etc/rc.d/zabbix_agentd enable
/usr/local/etc/rc.d/zabbix_agentd restart
```

### Konfigurace Zabbix Agentd

```console
joe /usr/local/etc/zabbix8/zabbix_agentd.conf
...
Hostname=freebsd-8e714c18
Server=enceladus.pfsense.cz
ServerActive=enceladus.pfsense.cz
Timeout=30
HostMetadata=SPOS

/usr/local/etc/rc.d/zabbix_agentd restart
```

## Nápověda

- FreeBSD port [zabbix8-agent](https://www.freshports.org/net-mgmt/zabbix8-agent/)
- Řešení [smejdil-osy-2026-zbx8-inst](https://github.com/sposdknl/smejdil-osy-2026-zbx8-inst) a [smejdil-osy-2026-zbx8-auto-reg](https://github.com/sposdknl/smejdil-osy-2026-zbx8-auto-reg)
- Zabbix 8.0 LTS [auto-registration](https://www.zabbix.com/documentation/8.0/en/manual/discovery/auto_registration)
...
