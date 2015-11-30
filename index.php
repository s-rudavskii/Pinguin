<?php include('./core/db.php'); ?>
<?php include('./core/function.php'); ?>
<?php include('./core/secure.php'); ?>
<?php $filter = isset($_COOKIE['email']) ? $_COOKIE['email'] : ''; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Pinguin | Private Site Monitor</title>
  <link rel="stylesheet" type="text/css" href="./core/style.css" media="screen" />
  <link rel="stylesheet/less" type="text/css" href="./core/styles.less" />
  <link rel="stylesheet" type="text/css" href="./core/icon.css" />
  <link href="<?php echo ICO; ?>" rel="icon" type="image/x-icon">
  <script type="text/javascript" src="./core/less.min.js"></script>
  <script type="text/javascript" src="./core/jquery.min.js"></script>
  <script type="text/javascript" src="./core/script.js"></script>
</head>
<body>
<div class="topLine">
  <div class="topLineBox">
    <div class="topLineEmail">
      <?php if($auth) { ?>
      <input id="filterEmail" type="text" placeholder="Search..." value="<?php echo $filter; ?>">
      <?php } ?>
    </div>
    <div class="topLineLogo">
      <img src="<?php echo ICO; ?>" alt="Pinguin Logo">
      Ping<strong>uin</strong>
    </div>
  </div>
</div>
<?php if($auth) { ?>
<div class="content">
  <?php
  $res = $db->query("
    SELECT *
    FROM site
    " . (
      $filter
        ? " WHERE email LIKE '%" . $filter . "%' or url LIKE '%" . $filter . "%' or comment LIKE '%" . $filter . "%'"
        : ''
    )
  );
  if ($res) {
    while($row = $res->fetch()){
      switch($row['active']){
        case 0:
          $iconActive       = 'icon-public';
          $iconActiveColor  = 'color-grey';
          $iconActiveAlt    = 'Offline';
          break;
        case 1:
          $iconActive       = 'icon-public';
          $iconActiveColor  = 'color-green';
          $iconActiveAlt    = 'Online';
          break;
        default:
          $iconActive       = 'icon-vpn_lock';
          $iconActiveColor  = 'color-orange';
          $iconActiveAlt    = 'Only proxy';
          break;
      }

      $iconComment = $row['comment'] != ''
        ? "<span class='icon-sms color-black' title='... User comment:\n\n{$row['comment']}'></span>"
        : '';

      $urlForming = preg_replace(
        '/(http:\/\/|https:\/\/)([^\/]+)(.*)/',
        '<span class="none">$1</span><span class="color-black">$2</span>$3',
        trim($row['url'], '/')
      );

      $iconBridgeColor = 'color-while';
      $iconBridgeText = '';
      if ($row['is_bridge']) {
        $iconBridgeColor  = 'color-red';
        $iconBridgeText = 'Bridge not exist';
        if ($row['bridge_isset']) {
          $iconBridgeColor  = 'color-green';
          $iconBridgeText = 'Bridge installed';
        }
      }

      echo "
        <div class='contentItem email-{$row['email']}'>
          <div class='contentItemIconBox'>
            <span class='icon-http {$iconBridgeColor}' title='{$iconBridgeText}'></span>
            <span class='icon-lock contentItemLock'></span>
            <span class='icon-history contentItemHistory'></span>
            |
            <span class='icon-close'></span>
          </div>
          <span class='{$iconActive} {$iconActiveColor}' title='{$iconActiveAlt}'></span>
          <div class='contentItemUrl'>{$urlForming}</div>
          {$iconComment}
        </div>";
    }
  }
  ?>
  <div class="contentItem contentItemLast">...</div>
</div>
<?php }  else { ?>
<div class="penguinBox">
  <h2>Sorry, access denied ...</h2>
  <p>Our secret penguin reported:<br>
    "<strong>Your IP address (<?php echo $_SERVER['REMOTE_ADDR']; ?>) is not in the whitelist.</strong>"</p>

  <p>Please contact the email address: <br> ILovePenguin@mesija.net</p>
</div>
<?php }?>
<div id="footer">
  Pinguin - Private Site Monitor 1.0 &nbsp; &nbsp; &nbsp; &copy; 2015 Create by Mesija
</div>
</body>
</html>