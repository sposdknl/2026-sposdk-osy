# Ansible
Repositories for teaching purposes at SPOS DK

Repository pro vyuku na SPOS DK

## First step with Ansible

- Red Hat Ansible Automation Platform  [Ansible](https://www.redhat.com/en/technologies/management/ansible)

```console
vagrant up
vagrant ssh
sudo su -
cd /opt/repo/Install/Ansible/
ansible-playbook -i inventory.ini -l bastion configure_servers.yml
```
...