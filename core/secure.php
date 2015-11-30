<?php

$res = $db->query("SELECT * FROM ip");

$ip = array();

while($row = $res->fetch()){
  $ip[] = $row['ip'];
}

$auth = false;

if (in_array($_SERVER['REMOTE_ADDR'], $ip)){
  $auth = true;
}