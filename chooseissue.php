<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>DemocracyLab: Choose Issue</title>
	<link href="images/favicon.ico" rel="shortcut icon">
	<link rel="stylesheet" href="stylesheets/screen.css" media="screen">
	<script src="js/jquery-1.7.2.js"></script>
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
				echo dl_facebook_url('summary.php');
				$democracylab_community_id = $tmpc;
				$democracylab_issue_id = $tmpi;
				?>">Choose "<?= $row->title ?>" as the issue to explore</a></p>
		</div>
		<?php
	} ?>

<div id="adding-section" class="clearfix">
	<a href="<?= dl_facebook_url('summary.php') ?>">Return to the overview page</a>
</div>

<div id="footer" class="clearfix">
</div>

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
