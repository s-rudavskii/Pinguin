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

      $iconActive       = 'icon-vpn_lock';
      $iconActiveColor  = 'color-red';
      $iconActiveAlt    = 'Error';

      if ($row['code'] < 400 && $row['code'] != 0) {
        $iconActive       = 'icon-public';
        $iconActiveColor  = 'color-green';
        $iconActiveAlt    = 'Online';
      }

      if (($row['code'] >= 400 && $row['code'] <= 500) || $row['code'] == 0) {
        $iconActive       = 'icon-public';
        $iconActiveColor  = 'color-grey';
        $iconActiveAlt    = 'Offline';
      }

      $iconComment = $row['comment'] != ''
        ? "<span class='icon-sms color-black' title='... User comment:\n\n{$row['comment']}'></span>"
        : '';

      $urlForming = preg_replace(
        '/(http:\/\/|https:\/\/)([^\/]+)(.*)/',
        '<span class="none">$1</span><a href="' . $row['url'] . '" target="_blank"><span class="color-black">$2</span></a>$3',
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

      $iconColorLock = '';
      $iconDeletePass = 0;
      if ($row['pass'] != ''){
        $iconColorLock = 'color-green';
        $iconDeletePass = 1;
      }

      echo "
        <div class='contentItem id-{$row['id']}'>
          <div class='contentItemIconBox'>
            <span class='icon-http {$iconBridgeColor}' title='{$iconBridgeText}'></span>
            <span class='icon-lock contentItemLock {$iconColorLock}'></span>
            <span class='icon-history contentItemHistory'></span>
            |
            <span class='icon-close' onclick=\"deleteSite({$row['id']},{$iconDeletePass},'{$row['url']}')\"></span>
          </div>
          <span class='{$iconActive} {$iconActiveColor}' title='{$row['code']} | {$iconActiveAlt}'></span>
          <div class='contentItemUrl'>{$urlForming}</div>
          {$iconComment}
        </div>";
    }
  }
  ?>
  <div class="contentItem contentItemLast">
    <form action="./" method="post" class="form-row">
      <span>
        <input class="slide-up" id="url" type="text" placeholder="http://" />
        <label for="url">URL</label>
      </span>
      <span>
        <input class="slide-up" id="email" type="text" placeholder="test@mail.com" />
        <label for="email">Email</label>
      </span>
      <span>
        <input class="slide-up" id="comment" type="text" placeholder="..." />
        <label for="comment">Comment</label>
      </span>
      <span style="width: 200px; padding-left: 60px; text-align: left;">
        <div class="squaredOne" style="display: inline-block; position: absolute; margin-top: -5px; margin-left: -40px;">
          <input style="width: 20px;" id="bridge" type="checkbox" />
          <label for="bridge"></label>
        </div> Is bridge
      </span>
      <span>
        <input class="slide-up" id="password" type="text" placeholder="Not required" />
        <label for="password">Password</label>
      </span>
      <span>
        <input class="form-submit" type="button" value="Add new site" />
      </span>
    </form>
  </div>
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
<div id="alertBox"></div>
</body>
</html>