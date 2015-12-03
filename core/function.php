<?php

define('ICO', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAABblBMVEUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABNA2d/AAAAeXRSTlMAAQIDBAUHCgsNDg8RFhcZGxwdICEiIyQlJicsLS80OD0+P0BDRUZHSU5QUVdZXV9hYmVoaWprbnFyd3h6e35/gYKDhoqPkJKanKCio6SlpqeoqaqvsrO5vMHEyMnKz9PU1tjZ2tvf4eTl5u3v8fLz9fb4+fr7/P3+ATrpiQAAAsZJREFUeAGFlYlT6kgQh5uniIfiIR7rga43iojooqIu4n3oiiIeLireCiwKCun+75dpEoxh4vsqlZr+Zb7MZDJVA+X0Bk4TWCBxGuiF31LhuSLC0nXlqYAf6btBAzd98ANBhQi/X6QEzaezS1guIO2aTWsHTdgBKbOE8hEIZ0FCyzsLqchKMPIpun1GgiuRFAvvLRLhCAtk5qtF23GCeOIQrer5jMiPoIzWPBHmBrRyaEhrDeQIKd9avqJiuqsgYVXM6u+y+BoRs3aZYM8i4rUxbVSI6AyknCGR0mgI+5GINuXCJhJhvyH0YIFlYNpHq3mBRtuBWRbPPAZhQYwQKC7LB8arAKri9PEnBwExwoJBCAlhkZvbhOQEcCLRNgeLQghJPow2uOkjTNcC1KaJfBxsCMGwIDb+n3vctrhD3TA5Cd0ht4WDPd4DNtDj5n0WV6tfrkukS9cvtYzzNnSDnkMWHtQuMRIlxlT9gYVD0GFNocCnllPITKmlj6uUVSeM8SuTpWhLlFul1yX58ZhOOOAk+hV4EwnvVxXlxwdfgSOHAv2/qaz89lcFOQdohNEwJmOcMYa1elgpCj1mQk9RUIaBqX/CIl1mQhcWeapX/wEhX+aC1oO/24taOWgmDGo90AvwxxtqTJsJ06jx1gFRTebNLGebSp2ikBGNbELcb82EW9E1kRX3DHSuh/f9DTMocIIUJwpmGvz74fVONbPnkYgiciGCRJi3fw8vsIAyAhJGFPHswpBOiBHwpQnKaHrhz50wxNZbIdBdBxjouCNeECsYcLGAab9Fn1r8aWTBJfu0Io9zzVrUPPeohrLlqIlrJxDeh9eCwbXwPWonULwGJLQ9mx1Zz20goS54jiacr9RJhGMyPxTpH4nw70/ClUSYQXOUv0CC4z/dCMmkboRUq8nJzsdz/jV2vDRus40vHcde80L41L//fzUtmIRA41D7AAAAAElFTkSuQmCC');

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

function alert($data, $error = false)
{
  $message = array(
    'error' => $error,
    'data' => $data
  );
  exit(json_encode($message));
}

if (isset($_POST['action'])){
  switch($_POST['action']){
    case 'delete':
      $res = $db->query("
        SELECT pass
        FROM site
        WHERE id = " . $db->quote($_POST['id']) . "
      ");
      $pass = $res->fetch()['pass'];
      if ($pass && $pass != md5($_POST['pass'])) {
        alert('Invalid password', true);
      }
      $db->query("
        DELETE FROM site
        WHERE id = " . $db->quote($_POST['id']) . "
      ");
      alert('Site ID: ' . $_POST['id'] . ' delete');
      break;
    case 'add':
      $res = $db->query("
        SELECT id
        FROM site
        WHERE url = " . $db->quote($_POST['url']) . "
      ");

      if ($res->rowCount() > 0) {
        alert("Site exist, ID " . $res->fetch()['id'], true);
      }

      $code = getStatus($_POST['url']);
      $status = $code < 400 && $code != 0 ? 1 : 0;

      $statusBridge = 0;
      if ($_POST['bridge']) {
        $statusBridge = getStatus($_POST['url'] . 'bridge2cart/bridge.php') < 400 && $code != 0 ? 1 : 0;
      }

      $db->query("
        INSERT INTO site (url, email, comment, is_bridge, bridge_isset, active, code, pass)
        VALUES (
          " . $db->quote($_POST['url']) . ",
          " . $db->quote($_POST['email']) . ",
          " . $db->quote($_POST['comment']) . ",
          " . $db->quote($_POST['bridge']) . ",
          " . $db->quote($statusBridge) . ",
          " . $db->quote($status) . ",
          " . $db->quote($code) . ",
          " . $db->quote($_POST['pass'] != '' ? md5($_POST['pass']) : '') . "
        )
      ");

      alert('Site ' . $_POST['url'] . ' add');
      break;
  }
}