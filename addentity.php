<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.inc');

$type = $_REQUEST['type'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="stylesheets/screen.css" media="screen">
	<title><?php echo(idx($app_info, 'name')) ?> Add New <?= dl_typestring($type,'ucs') ?></title>
  <?php echo('<meta property="fb:app_id" content="' . AppInfo::appID() . '" />'); ?>
</head>
<body>
<header class="clearfix">
</header>

<?php if($type == 1) { ?>
	<section id="value-section" class="clearfix">
		<div class="icon"></div>
		<div class="title">Add a New Value</div>
		<div style="clear: both"></div>
		<p>Values are the beliefs and principles that form the basis of our decisions.
		They are why we think about the world the way we do.</p>
<?php } else if($type == 2) { ?>
	<section id="objective-section" class="clearfix">
		<div class="icon"></div>
		<div class="title">Add a New Objective</div>
		<div style="clear: both"></div>
		<p>Objectives are statements of our goals and priorities. Objectives are based on
		our values, and are statements of what we hope to achieve.</p>
<?php } else if($type == 3) { ?>
	<section id="policy-section" class="clearfix">
		<div class="icon"></div>
		<div class="title">Add a New Policy</div>
		<div style="clear: both"></div>
		<p>Policies are plans of action. They are detailed descriptions of how we
		can achieve our objectives, including a prudent assessment of likely
		costs and benefits.</p>
<?php } ?>

<form method="POST" action="addentity_post.php">
<input type="hidden" name="type" value="<?= $type ?>">
<?= dl_facebook_form_fields() ?>
<div class="field-legend">Name: </div><div class="field-contents"><input name="name"></div>
<div class="clearfix"></div>
<div class="field-legend">Description: </div><div class="field-contents"><textarea name="description" rows=3 cols=50></textarea></div>
<div class="clearfix"></div>
<input type="submit" value="Add <?= dl_typestring($type,'ucs') ?>">
</form>

<a href="<?= dl_facebook_url('entities.php',$type) ?>">Go back to the <?= dl_typestring($type,'ucp') ?> page</a>
</section>

<section id="footer" class="clearfix">
</section>
</body>
</html>