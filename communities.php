<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.php');

if($democracylab_user_role == 0) {
	header('Location: ' . dl_facebook_url('index.php') );
	exit;
}
if(isset($_POST['S45'])) {
	foreach($_POST as $key => $value) {
		if(preg_match("/title-(\d+)/",$key,$matches)) {
			$safevalue = pg_escape_string($value);
			pg_query("UPDATE democracylab_issues SET title = '{$safevalue}' WHERE issue_id = {$matches[1]}");
		}
		if(preg_match("/description-(\d+)/",$key,$matches)) {
			$safevalue = pg_escape_string($value);
			pg_query("UPDATE democracylab_issues SET description = '{$safevalue}' WHERE issue_id = {$matches[1]}");
		}
	}
	$sql = "UPDATE democracylab_issues SET ";
	header("Location: " . dl_facebook_redirect_url('index.php') );
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo(idx($app_info, 'name')) ?></title>
	<link rel="stylesheet" href="stylesheets/screen.css" media="screen">
	<script src="lib/jquery-1.7.1.min.js"></script>
	<?php echo('<meta property="fb:app_id" content="' . AppInfo::appID() . '" />'); ?>
</head>
<body>
<header class="clearfix">
	<div style="margin-left: -5px; background-image: url(images/dl.png); width: 293px; height: 102px; float: left; margin-right: 20px;"></div>
</header>

<form method=POST>
<?= dl_facebook_form_fields() ?>
<?php $result = pg_query($dbconn,"SELECT * FROM democracylab_issues"); 
while($row = pg_fetch_object($result)) {
	?>
	<div id="issue-section" class="clearfix">
		<div class="icon"></div>
		<div class="title"><input name="title-<?= $row->issue_id ?>" size=50 value="<?= $row->title ?>"></div>
		<div class="clearfix"></div>
		<p><textarea cols=50 rows=5 name="description-<?= $row->issue_id ?>"><?= $row->description ?></textarea></p>
	</div>
	<?php
} ?>
    <div id="footer" class="clearfix">
		<input type="Submit" name="S45" value="Make these changes">
	<p><a href="<?= dl_facebook_url('index.php') ?>">back to main page</a></p>
	</div>
</form>
  </body>
</html>