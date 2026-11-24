<?php
declare(strict_types=1);

$apiUrl = 'http://127.0.0.1/api_jsonrpc.php';
$secretDir = '/var/db/zabbix-install';
$adminFile = $secretDir.'/admin-password';
$token = null;

function api(string $method, array $params): mixed {
    global $apiUrl, $token;
    $body = json_encode(['jsonrpc' => '2.0', 'method' => $method, 'params' => $params, 'id' => 1], JSON_THROW_ON_ERROR);
    $curl = curl_init($apiUrl);
    $headers = ['Content-Type: application/json-rpc'];
    if ($token !== null) $headers[] = 'Authorization: Bearer '.$token;
    curl_setopt_array($curl, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => $headers, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15]);
    $raw = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($raw === false) throw new RuntimeException("API $method: $err");
    $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    if (isset($data['error'])) throw new RuntimeException("API $method: ".json_encode($data['error']));
    if (!array_key_exists('result', $data)) throw new RuntimeException("API $method: neplatná odpověď");
    return $data['result'];
}

$password = is_file($adminFile) ? trim(file_get_contents($adminFile)) : 'zabbix';
$lastError = null;
for ($i = 0; $i < 30; $i++) {
    try {
        $token = api('user.login', ['username' => 'Admin', 'password' => $password]);
        break;
    } catch (Throwable $e) {
        $lastError = $e;
        sleep(2);
    }
}
if ($token === null) throw $lastError ?? new RuntimeException('Zabbix API není dostupné');

$groups = api('hostgroup.get', ['filter' => ['name' => ['SPOS Lab']], 'output' => ['groupid']]);
$groupId = $groups ? $groups[0]['groupid'] : api('hostgroup.create', ['name' => 'SPOS Lab'])['groupids'][0];
$templates = api('template.get', ['filter' => ['host' => ['FreeBSD by Zabbix agent']], 'output' => ['templateid']]);
if (!$templates) throw new RuntimeException('Chybí šablona FreeBSD by Zabbix agent');
$templateId = $templates[0]['templateid'];

function ensureHost(string $name, string $groupId, array $templates): string {
    $hosts = api('host.get', ['filter' => ['host' => [$name]], 'output' => ['hostid']]);
    if ($hosts) return $hosts[0]['hostid'];
    return api('host.create', ['host' => $name, 'groups' => [['groupid' => $groupId]],
        'templates' => $templates,
        'interfaces' => [['type' => 1, 'main' => 1, 'useip' => 1, 'ip' => '127.0.0.1',
            'dns' => '', 'port' => '10050']]])['hostids'][0];
}

ensureHost('spos-zabbix8', $groupId, [['templateid' => $templateId]]);
$existingCert = api('host.get', ['filter' => ['host' => ['sposdk.cz']], 'output' => ['hostid']]);
if (!$existingCert) {
    api('configuration.import', ['format' => 'yaml',
        'source' => file_get_contents('/tmp/sposdk.cz_hosts.yaml'),
        'rules' => [
            'host_groups' => ['createMissing' => true, 'updateExisting' => false],
            'hosts' => ['createMissing' => true, 'updateExisting' => false]
        ]]);
}
$certHostId = ensureHost('sposdk.cz', $groupId, []);
$items = api('item.get', ['hostids' => [$certHostId], 'filter' => ['key_' => ['sposdk.cert.days']], 'output' => ['itemid']]);
if (!$items) {
    $interfaces = api('hostinterface.get', [
        'hostids' => [$certHostId],
        'filter' => ['type' => 1, 'main' => 1],
        'output' => ['interfaceid']
    ]);
    if (!$interfaces) throw new RuntimeException('Host sposdk.cz nemá hlavní rozhraní Zabbix agenta');
    api('item.create', ['hostid' => $certHostId, 'name' => 'TLS certificate: days until expiry',
        'key_' => 'sposdk.cert.days', 'type' => 0, 'value_type' => 3,
        'interfaceid' => $interfaces[0]['interfaceid'],
        'delay' => '1h', 'units' => 'd', 'description' => 'Certifikát https://sposdk.cz']);
}

if (!is_file($adminFile)) {
    $users = api('user.get', ['filter' => ['username' => ['Admin']], 'output' => ['userid']]);
    if (!$users) throw new RuntimeException('Administrátor Admin nebyl nalezen');
    $newPassword = bin2hex(random_bytes(24));
    api('user.update', ['userid' => $users[0]['userid'], 'passwd' => $newPassword,
        'current_passwd' => $password]);
    file_put_contents($adminFile, $newPassword."\n");
    chmod($adminFile, 0600);
}
echo "Hosté spos-zabbix8 a sposdk.cz jsou připraveni.\n";
