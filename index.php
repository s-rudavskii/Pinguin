<?php include('./core/db.php'); ?>
<?php
$filterEmail = isset($_COOKIE['email']) ? $_COOKIE['email'] : '';
$auth = true;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Site Monitor</title>
  <link rel="stylesheet" type="text/css" href="./core/style.css" media="screen" />
  <link rel="stylesheet/less" type="text/css" href="./core/styles.less" />
  <link rel="stylesheet" type="text/css" href="./core/icon.css" />
  <link href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAADHklEQVR42u2V20tUQRzHZ+ZcdFdX3dW2JKU9JtRDEXZRwyjEKNtUFoSIwDAJIo18yYL+gR7SQEqFohYMeuglzXKVsqvmJRN6TlzDSBNXXfd42z17pt8su5vHvBa+RMMe5je/+f0+37kvRhtc8H+BdQmUt3t01YcMs38DLG+XgREdZoQFTts/WKTtkhNxQuaNrITuP4Ff75zIUOdnukY9XulBvjSoEbDVvbEIxgRnvF5EBoO+9GZ2Ut164NfeDl90uz21Y/IcEnlRenRqp1Yg784Li8602akoPmTQichsirZX5kgla4FXvPpq/+GaKvbMehFHOEQILz0+s0srcLy6xaI3mp3U70cqVVWB48i2RGOfT4jKvJ29xbcUuOz1iBCpTHcPfp9I80EewQSIGGGOk54UpWkFjt56JkUaNw1Q1a8yP2VFoSQ1KX5SFXX7ao5ZBjTwl0MpZE7+1P/NFYd5TDHGNADEmMAvpelchlMjwIr17nug+pnJRAjLUBQVJZvjkE4fVVB7ckcT6yx1fMmflT1Ph0YnEc8TBgnEh86N48KRMFcrUN+X650ad8AoQiKBGAWmb4qNxslmYy2MDw2NjJe63DLlOY4u4LBZIyE65oSjOL1lSQFWcu29qfNTrl5EUWzQFRgd7Au4cDCJsvVeMGoEHuSOiInb31qS0b+Qt+RNzrnfJcJMehCle4APIEKCEKan4kD7lx/KZzEmPr3tfKZ3MWvFp+Jg1fN6pHiLtFEYwJQdFwoDAC3hYWdF3tnlGKu+RelVTWXq3Nyd4CmhoRxYbkxE8VJPRUHNSvlreuwOVDZn+abd7+CEh5ZKFXT6wx+vFnSslrvm13RvVbNZmfF0INhsXm/I6ruSN7qWvH/o/6C1tZWXZXkrXBaz3+83wabGgTsSbD0hJIZdInYBVTid0Ga1CrULfCr0TYA9BfYYfMOFhYVjvwk0NDQkg8BlCN4NzUSoDUwAEgSwdeEEEAmKMfA0uFSwPVBPwjcEQm1Go/Ge1Wr1L7tEjY2NHAQyMA8D5QEQEeoLzYS9hVDPQhwT8kKcYrPZ6GLWhu/BTycpUCi23O7rAAAAAElFTkSuQmCC" rel="icon" type="image/x-icon">
  <script type="text/javascript" src="./core/less.min.js"></script>
  <script type="text/javascript" src="./core/jquery.min.js"></script>
  <script type="text/javascript" src="./core/script.js"></script>
</head>
<body>
<div class="topLine">
  <div class="topLineBox">
    <div class="topLineEmail">
      <?php if($auth) { ?>
      <input id="filterEmail" type="text" placeholder="Enter you email" value="<?php echo $filterEmail; ?>">
      <?php } ?>
    </div>
    <div class="topLineLogo">
      <span class="icon-public"></span>
      Site Monitor
    </div>
  </div>
</div>
<?php if($auth) { ?>
<div class="content">
  <?php
  $res = $db->query("SELECT * FROM site" . ($filterEmail ? " WHERE email = '" . $filterEmail . "'" : ''));
  if ($res) {
    while($row = $res->fetch(PDO::FETCH_ASSOC)){
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

      echo "
        <div class='contentItem'>
          <div class='contentItemIconBox'>
            <span class='icon-http'></span>
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
<div class="pandaBox">
  <h2>Sorry, access denied ...</h2>
  <p>Our secret battle panda reported:<br>
    "<strong>Your IP address is not in the whitelist.</strong>"</p>

  <p>Please contact the email address: <br> ILovePanda@mesija.net</p>
</div>
<?php }?>
<div id="footer">
  Site Monitor 1.0 &nbsp; &nbsp; &nbsp; &copy; 2015 Create by Mesija
</div>
</body>
</html>