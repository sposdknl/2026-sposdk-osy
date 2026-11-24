#!/usr/bin/env bash
set -eu

export ASSUME_ALWAYS_YES=yes
export IGNORE_OSVERSION=yes

# Repository latest odpovida hlavni vetvi stromu portu
mkdir -p /usr/local/etc/pkg/repos
cat > /usr/local/etc/pkg/repos/FreeBSD.conf <<'EOF'
FreeBSD: { url: "pkg+https://pkg.FreeBSD.org/${ABI}/latest", mirror_type: "srv", enabled: yes }
EOF
pkg update -f

# Nastroje a zavislosti portu jako binarni balicky, kompiluje se jen agent
pkg install -y git pkgconf muon samurai gmake curl pcre2

# Strom portu (existujici se zachova)
if [ ! -f /usr/ports/net-mgmt/zabbix8-agent/Makefile ]; then
  git clone --depth 1 https://git.FreeBSD.org/ports.git /usr/ports
fi

# Nastaveni kompilace bez dotazu, FreeBSD 14.3 je po EOL
touch /etc/make.conf
sed -i '' -E '/^[[:space:]]*(BATCH|ALLOW_UNSUPPORTED_SYSTEM)[[:space:]]*[+?:!]?=/d' /etc/make.conf
cat >> /etc/make.conf <<'EOF'
BATCH=yes
ALLOW_UNSUPPORTED_SYSTEM=yes
EOF

# Kompilace, vytvoreni balicku a instalace zabbix agenta
if ! pkg info -e zabbix8-agent; then
  cd /usr/ports/net-mgmt/zabbix8-agent
  # Pripadne dalsi chybejici zavislosti z balicku
  make install-missing-packages || true
  make package
  make install
  make clean
fi

/usr/local/sbin/zabbix_agentd -V

# Povoleni sluzby zabbix agent
/usr/local/etc/rc.d/zabbix_agentd enable

# Restart sluzby zabbix agent
/usr/local/etc/rc.d/zabbix_agentd restart

# EOF
