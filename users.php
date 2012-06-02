<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.php');

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
	<script src="js/jquery-1.7.1.min.js"></script>
	<?php echo('<meta property="fb:app_id" content="' . AppInfo::appID() . '" />'); ?>
</head>
<body>
<header class="clearfix">
	<div style="margin-left: -5px; background-image: url(images/dl.png); width: 293px; height: 102px; float: left; margin-right: 20px;"></div>
</header>

<script>
function change_admin(userid,node) {
	var x = node.value;
	var data = {};
	data['userid'] = userid;
	data['admin'] = x;
	$.ajax({
		url: '<?= dl_facebook_url('saveuseradmin_ajax.php') ?>',
		context: document.body,
		data: data,
		type: "POST",
		global: false
	});
}
</script>
<div id="issue-section" class="clearfix">
	<table style="color: black;">
	<?php
	$result = pg_query($dbconn, "SELECT * FROM democracylab_users ORDER BY name");
	while($row = pg_fetch_object($result)) {
		?><tr style="border: thin solid black;">
			<td style="padding: 5px;"><?= htmlspecialchars($row->name) ?></td>
			<td style="padding: 5px; color: grey;"><select onchange="change_admin(<?= $row->user_id ?>,this);"><option value="admin" <?= $row->role == 1 ? 'selected' : '' ?>>admin</option><option value="user" <?= $row->role == 0 ? 'selected' : '' ?>>user</option></select></td>
			<td style="padding: 5px; color: grey;"><?= $row->fb_id ?></td>
		</tr>
<?php
	}
	?>
	</table>
</div>

    <div id="footer" class="clearfix">
	<p><a href="<?= dl_facebook_url('index.php') ?>">back to main page</a></p>
	</div>
  </body>
</html>