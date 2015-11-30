<?php

include('./db.php');
include('./function.php');

$res = $db->query('SELECT * FROM site');

while($row = $res->fetch()){
  $code = getStatus($row['url']);
  $status = $code < 400 && $code != 0 ? 1 : 0;

  $statusBridge = 0;
  if ($row['is_bridge']) {
    $statusBridge = getStatus($row['url'] . 'bridge2cart/bridge.php') < 400 && $code != 0 ? 1 : 0;
  }

  $db->query("
    UPDATE site
    SET
      active = {$status},
      bridge_isset = {$statusBridge},
      code = {$code}
    WHERE
      id = {$row['id']}
  ");

  $title = '';
  $text = '';

  if ($status == $row['active']) {
    if ($status) {
      $title .= 'Site is now online (' . $row['url'] . ')';
      $text .= '
        <p>Site: <b>' . $row['url'] . '</b></p>
        <p>Status: <span style="background-color: #207c13; padding: 2px 4px; color: white;">online</span></p>
        <p>Time: ' . date("Y-m-d H:i:s") . '</p>';
    } else {
      $title .= 'Site down (' . $row['url'] . ')';
      $text .= '
        <p>Site: <b>' . $row['url'] . '</b></p>
        <p>Status: <span style="background-color: #ca0b0b; padding: 2px 4px; color: white;">down</span></p>
        <p>Time: ' . date("Y-m-d H:i:s") . '</p>';
    }
  }

  $body = '
    <div style="margin: 20px auto; padding: 15px; text-align: center; font-family: sans-serif;">
    <a href="http://pinguin.mesija.net/" target="_blank">
    <img src="'. ICO . '" alt="Pinguin Logo" style="display: block; margin: 0 auto 25px auto"></a>
    ' . $text . '
    </div>';

  if ($title != '' && $row['email'] != '') {
    mail($row['email'], 'Pinguin | ' . $title, $body);
  }
}