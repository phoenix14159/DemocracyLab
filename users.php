<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.inc');

if($democracylab_user_role == 0) {
	header('Location: ' . dl_facebook_url('index.php') );
	exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo(idx($app_info, 'name')) ?></title>
	<link rel="stylesheet" href="stylesheets/screen.css" media="screen">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<?php echo('<meta property="fb:app_id" content="' . AppInfo::appID() . '" />'); ?>
</head>
<body>
<header class="clearfix">
	<div style="margin-left: -5px; background-image: url(images/dl.png); width: 293px; height: 102px; float: left; margin-right: 20px;"></div>
</header>

<section id="issue-section" class="clearfix">
	<table style="color: black;">
	<?php
	$result = pg_query($dbconn, "SELECT * FROM democracylab_users");
	while($row = pg_fetch_object($result)) {
		?><tr style="border: thin solid black;">
			<td style="padding: 5px;"><?= htmlspecialchars($row->name) ?></td>
			<td style="padding: 5px; color: grey;"><?= ($row->role == 1) ? 'admin' : '' ?></td>
			<td style="padding: 5px; color: grey;"><?= $row->fb_id ?></td>
		</tr>
<?php
	}
	?>
	</table>
</section>

    <section id="footer" class="clearfix">
	<p><a href="<?= dl_facebook_url('index.php') ?>">back to main page</a></p>
	</section>
  </body>
</html>