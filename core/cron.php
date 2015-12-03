<?php

$db = new PDO(
  'mysql:host=budyliv.mysql.ukraine.com.ua;dbname=budyliv_pinguin',
  'budyliv_pinguin',
  'b89u4vkj',
  array(
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
  )
);

/**
 * @param $url
 * @return int
 */
function getStatus($url){
  $headers = @get_headers($url);
  $code = 0;
  if ($headers) {
    $code = (int)explode(' ', $headers[0])[1];
  }

  return $code;
}


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

  if ($status != $row['active']) {
    if ($status) {
      $title .= 'Site is now online ' . preg_replace(
          '/(http:\/\/|https:\/\/)([^\/]+)(.*)/',
          '$2$3',
          trim($row['url'], '/')
        );
      $text .= '
        <p>Site: <b>' . $row['url'] . '</b></p>
        <p>Status: <span style="background-color: #207c13; padding: 2px 4px; color: white;">online</span></p>
        <p>Time: ' . date("Y-m-d H:i:s") . '</p>';
    } else {
      $title .= 'Site down ' . preg_replace(
          '/(http:\/\/|https:\/\/)([^\/]+)(.*)/',
          '$2$3',
          trim($row['url'], '/')
        );
      $text .= '
        <p>Site: <b>' . $row['url'] . '</b></p>
        <p>Status: <span style="background-color: #ca0b0b; padding: 2px 4px; color: white;">down</span></p>
        <p>Time: ' . date("Y-m-d H:i:s") . '</p>';
    }
  }

  $body = '<html><body>
    <div style="margin: 20px auto; padding: 15px; text-align: center; font-family: sans-serif;">
    <a href="http://pinguin.mesija.net/" target="_blank">
    <img src="http://pinguin.mesija.net/core/penguin-icon.png" alt="Pinguin Logo" style="display: block; margin: 0 auto 25px auto"></a>
    ' . $text . '
    </div>
    </body></html>';

  if ($title != '' && $row['email'] != '') {
    $headers = "From: pinguin@mesija.net <Pinguin>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    mail($row['email'], 'Pinguin | ' . $title, $body, $headers);
  }
}