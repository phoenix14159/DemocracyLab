<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.php');

if($democracylab_user_role == 0) {
	header('Location: ' . dl_facebook_url('summary.php') );
	exit;
}

$type = intval($_REQUEST['type']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <link href="images/favicon.ico" rel="shortcut icon">
  <link rel="stylesheet" href="stylesheets/screen.css" media="screen">
  <title>DemocracyLab: Add New <?= dl_typestring($type,'ucs') ?></title>
</head>
<body>
<header class="clearfix">
</header>

<?php if($type == 1) { ?>
	<div id="value-section" class="clearfix">
		<div class="icon"></div>
		<div class="title">Add a New Value</div>
		<div style="clear: both"></div>
		<p>Values are the beliefs and principles that form the basis of our decisions.
		They are why we think about the world the way we do.</p>
<?php } else if($type == 2) { ?>
	<div id="objective-section" class="clearfix">
		<div class="icon"></div>
		<div class="title">Add a New Objective</div>
		<div style="clear: both"></div>
		<p>Objectives are statements of our goals and priorities. Objectives are based on
		our values, and are statements of what we hope to achieve.</p>
<?php } else if($type == 3) { ?>
	<div id="policy-section" class="clearfix">
		<div class="icon"></div>
		<div class="title">Add a New Policy</div>
		<div style="clear: both"></div>
		<p>Policies are plans of action. They are detailed descriptions of how we
		can achieve our objectives, including a prudent assessment of likely
		costs and benefits.</p>
<?php } ?>

<form method="POST" action="addentity_post.php">
<?= dl_facebook_form_fields($type) ?>
<div class="field-legend">Name: </div><div class="field-contents"><input name="name"></div>
<div class="clearfix"></div>
<div class="field-legend">Description: </div><div class="field-contents"><textarea name="description" rows=3 cols=50></textarea></div>
<div class="clearfix"></div>
<input type="submit" value="Add <?= dl_typestring($type,'ucs') ?>">
</form>

<a href="<?= dl_facebook_url('entities.php',$type) ?>">Go back to the <?= dl_typestring($type,'ucp') ?> page</a>
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

</script>
</body>
</html>