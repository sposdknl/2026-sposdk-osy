# Install pfSense
Repositories for teaching purposes at SPOS DK

Repository pro vyuku na SPOS DK

## Install OS

[pfSense](https://www.pfsense.org/download/)

- pfSense Vagrant Box (Image) - [cs-c4300/pfsense](https://portal.cloud.hashicorp.com/vagrant/discover/cs-c4300/pfsense)
  (pfSense CE 2.7.2, jiná verze boxu není k dispozici)
- Webové rozhraní: <https://localhost:8888>, uživatel `admin`, heslo `admin`

![pfSense Dashboard](../../Images/pfsense-box.PNG)

## Rozhraní

| Rozhraní | Síťová karta | IP adresa | Síť |
| --- | --- | --- | --- |
| WAN | `em0` | `10.1.1.100` (DHCP) | VirtualBox host-only |
| LAN | `vtnet1` | `192.168.1.1` | VirtualBox `intnet` |
| OPT1 | `vtnet0` | `10.0.2.15` | Vagrant NAT |

![pfSense Interfaces](../../Images/pfsense-box-Interfaces.PNG)

Stanice v síti LAN je vidět v ARP tabulce:

![pfSense ARP Table](../../Images/pfsense-box-ARP_Table.PNG)

## Monitoring pomocí SNMP

Služba se zapíná v *Services / SNMP*. Community `public` je vhodná jen pro výuku.

![pfSense SNMP](../../Images/pfsense-box-SNMP.PNG)

...
