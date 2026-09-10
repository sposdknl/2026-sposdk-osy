#!/usr/bin/env bash

# Install uuid and gsed
sudo pkg install -y p5-UUID gsed

# Unikatni hostname
UNIQUE_HOSTNAME="fbsdsrv01-$(uuidgen)"
SHORT_HOSTNAME=$(echo $UNIQUE_HOSTNAME | cut -d'-' -f1,2)
ZBX_AGENT_CONF="/usr/local/etc/zabbix7/zabbix_agentd.conf"

# Konfigurace zabbix agentd
sudo cp -v $ZBX_AGENT_CONF $ZBX_AGENT_CONF-orig
sudo gsed -i "s/Hostname=Zabbix server/Hostname=$SHORT_HOSTNAME/g" $ZBX_AGENT_CONF
sudo gsed -i 's/Server=127.0.0.1/Server=enceladus.pfsense.cz/g' $ZBX_AGENT_CONF
sudo gsed -i 's/ServerActive=127.0.0.1/ServerActive=enceladus.pfsense.cz/g' $ZBX_AGENT_CONF
sudo gsed -i 's/# Timeout=3/Timeout=30/g' $ZBX_AGENT_CONF
sudo gsed -i 's/# HostMetadata=/HostMetadata=SPOS/g' $ZBX_AGENT_CONF
sudo diff -u $ZBX_AGENT_CONF-orig $ZBX_AGENT_CONF

# Restart sluzby zabbix-agentd
sudo /usr/local/etc/rc.d/zabbix_agentd restart

# EOF