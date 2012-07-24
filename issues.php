<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.php');

if($democracylab_user_role == 0) {
	header('Location: ' . dl_facebook_url('summary.php') );
	exit;
}
if(isset($_POST['S45'])) {
	$redirect_to_choose = 0;
	foreach($_POST as $key => $value) {
		if(preg_match("/title-(\d+)/",$key,$matches)) {
			$safevalue = pg_escape_string($value);
			pg_query("UPDATE democracylab_issues SET title = '{$safevalue}' WHERE issue_id = {$matches[1]}");
		}
		if(preg_match("/description-(\d+)/",$key,$matches)) {
			$safevalue = pg_escape_string($value);
			pg_query("UPDATE democracylab_issues SET description = '{$safevalue}' WHERE issue_id = {$matches[1]}");
		}
		if(preg_match("/summary-(\d+)/",$key,$matches)) {
			$safevalue = pg_escape_string($value);
			pg_query("UPDATE democracylab_issues SET how_it_works = '{$safevalue}' WHERE issue_id = {$matches[1]}");
		}
		if(preg_match("/footer-(\d+)/",$key,$matches)) {
			$safevalue = pg_escape_string($value);
			pg_query("UPDATE democracylab_issues SET extra_footer_text = '{$safevalue}' WHERE issue_id = {$matches[1]}");
		}
	}
	foreach($_POST as $key => $value) {
		if(preg_match("/delete-(\d+)/",$key,$matches)) {
			if($value == 'delete') {
				pg_query("DELETE FROM democracylab_issues WHERE issue_id = {$matches[1]}");
				pg_query("DELETE FROM democracylab_entities WHERE issue_id = {$matches[1]}");
				pg_query("DELETE FROM democracylab_rankings WHERE issue_id = {$matches[1]}");
				if($democracylab_issue_id == $matches[1]) {
					$redirect_to_choose = 1;
				}
			}
		}
	}
	$new_title = 0;
	$new_description = '';
	$new_summary = '';
	$new_footer = '';
	foreach($_POST as $key => $value) {
		if(preg_match("/title-new/",$key,$matches)) {
			if($value != 'New Issue') {
				$new_title = pg_escape_string($value);
			}
		}
		if(preg_match("/description-new/",$key,$matches)) {
			$new_description = pg_escape_string($value);
		}
		if(preg_match("/summary-new/",$key,$matches)) {
			$new_summary = pg_escape_string($value);
		}
		if(preg_match("/footer-new/",$key,$matches)) {
			$new_footer = pg_escape_string($value);
		}
	}
	if($new_title) {
		pg_query("INSERT INTO democracylab_issues (community_id,title,description,how_it_works,extra_footer_text) VALUES (1,'{$new_title}','{$new_description}','{$new_summary}','{$new_footer}')");
	}
	if($redirect_to_choose) {
		header("Location: " . dl_facebook_redirect_url('chooseissue.php') );
	} else {
		header("Location: " . dl_facebook_redirect_url('summary.php') );
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>DemocracyLab: Edit Issues</title>
	<link href="images/favicon.ico" rel="shortcut icon">
	<link rel="stylesheet" href="stylesheets/screen.css" media="screen">
	<script src="js/jquery-1.7.2.js"></script>
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
		<p style="float: left;"><input type="checkbox" name="delete-<?= $row->issue_id ?>" value="delete"> delete this issue</p>
		<div class="clearfix"></div>
		<p>description:<br><textarea cols=50 rows=3 name="description-<?= $row->issue_id ?>"><?= $row->description ?></textarea></p>
		<p>summary page paragraph:<br><textarea cols=50 rows=5 name="summary-<?= $row->issue_id ?>"><?= $row->how_it_works ?></textarea></p>
		<p>summary page footer:<br><textarea cols=50 rows=2 name="footer-<?= $row->issue_id ?>"><?= $row->extra_footer_text ?></textarea></p>
	</div>
	<?php
} ?>
<div id="issue-section" class="clearfix">
	<div class="icon"></div>
	<div class="title"><input name="title-new" size=50 value="New Issue"></div>
	<div class="clearfix"></div>
	<p>description:<br><textarea cols=50 rows=3 name="description-new"><?= $row->description ?></textarea></p>
	<p>summary page paragraph:<br><textarea cols=50 rows=5 name="summary-new"><?= $row->how_it_works ?></textarea></p>
	<p>summary page footer:<br><textarea cols=50 rows=2 name="footer-new"><?= $row->extra_footer_text ?></textarea></p>
</div>
    <div id="footer" class="clearfix">
		<input type="Submit" name="S45" value="Make these changes">
	<p><a href="<?= dl_facebook_url('summary.php') ?>">back to main page</a></p>
	</div>
</form>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-2879129-3']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>  </body>
</html>