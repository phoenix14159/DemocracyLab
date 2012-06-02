<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.php');

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

	<?php $result = pg_query($dbconn,"SELECT * FROM democracylab_issues"); 
	while($row = pg_fetch_object($result)) {
		?>
		<div id="issue-section" class="clearfix">
			<div class="icon"></div>
			<div class="title"><?= $row->title ?></div>
			<div class="clearfix"></div>
			<p><?= $row->description ?></p>
			<p><a href="<?php
				$tmpc = $democracylab_community_id;
				$tmpi = $democracylab_issue_id;
				$democracylab_community_id = $row->community_id;
				$democracylab_issue_id = $row->issue_id;
				echo dl_facebook_url('index.php');
				$democracylab_community_id = $tmpc;
				$democracylab_issue_id = $tmpi;
				?>">Choose "<?= $row->title ?>" as the issue to explore</a></p>
		</div>
		<?php
	} ?>

<div id="adding-section" class="clearfix">
	<a href="<?= dl_facebook_url('index.php') ?>">Return to the overview page</a>
</div>

<div id="footer" class="clearfix">
</div>

  </body>
</html>
