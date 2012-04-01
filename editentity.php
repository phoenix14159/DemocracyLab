<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.inc');

if($democracylab_user_role == 0) {
	header('Location: ' . dl_facebook_url('index.php') );
	exit;
}

$type = intval($_REQUEST['type']);
$entityid = isset($_REQUEST['entityid']) ? intval($_REQUEST['entityid']) : 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="stylesheets/screen.css" media="screen">
	<title><?php echo(idx($app_info, 'name')) ?> Edit Existing <?= dl_typestring($type,'ucs') ?></title>
  <?php echo('<meta property="fb:app_id" content="' . AppInfo::appID() . '" />'); ?>
</head>
<body>
<header class="clearfix">
</header>

<?php if(!$entityid) {
	if($type == 1) { ?>
		<section id="value-section" class="clearfix">
			<div class="icon"></div>
			<div class="title">Edit an Existing Value</div>
			<div style="clear: both"></div>
			<p>Choose a value:</p>
	<?php } else if($type == 2) { ?>
		<section id="objective-section" class="clearfix">
			<div class="icon"></div>
			<div class="title">Edit an Existing Objective</div>
			<div style="clear: both"></div>
			<p>Choose an objective:</p>
	<?php } else if($type == 3) { ?>
		<section id="policy-section" class="clearfix">
			<div class="icon"></div>
			<div class="title">Edit an Existing Policy</div>
			<div style="clear: both"></div>
			<p>Choose a policy:</p>
	<?php } 
	$result = pg_query($dbconn, "SELECT * FROM democracylab_entities WHERE type = {$type} ORDER BY title");
	?>
	<script type="text/javascript">
	function submitform()
	{
	  document.selectionform.submit();
	}
	</script>
	<form name="selectionform" method="GET">
		<?= dl_facebook_form_fields($type); ?>
		<select name="entityid" onchange="submitform();">
			<option value="0">-- choose one</option>
			<?php
			while($row = pg_fetch_object($result)) {
				?><option value="<?= $row->entity_id ?>"><?= htmlspecialchars($row->title) ?></option>
<?php  		} ?>
		</select> 
	</form><p></p>
	<?php
} else {
	$result = pg_query($dbconn, "SELECT * FROM democracylab_entities WHERE entity_id = {$entityid}");
	$row = pg_fetch_object($result);
	if($type == 1) { ?>
		<section id="value-section" class="clearfix">
			<div class="icon"></div>
			<div class="title"><?= htmlspecialchars($row->title) ?></div>
			<div style="clear: both"></div>
			<p></p>
	<?php } else if($type == 2) { ?>
		<section id="objective-section" class="clearfix">
			<div class="icon"></div>
			<div class="title"><?= htmlspecialchars($row->title) ?></div>
			<div style="clear: both"></div>
			<p></p>
	<?php } else if($type == 3) { ?>
		<section id="policy-section" class="clearfix">
			<div class="icon"></div>
			<div class="title"><?= htmlspecialchars($row->title) ?></div>
			<div style="clear: both"></div>
			<p></p>
	<?php } ?>

	<form method="POST" action="editentity_post.php">
	<input type="hidden" name="entityid" value="<?= $entityid ?>">
	<?= dl_facebook_form_fields($type) ?>
	<div class="field-legend">Name: </div><div class="field-contents"><input name="name" value="<?= htmlspecialchars($row->title) ?>"></div>
	<div class="clearfix"></div>
	<div class="field-legend">Description: </div><div class="field-contents"><textarea name="description" rows=3 cols=50><?= htmlspecialchars($row->description) ?></textarea></div>
	<div class="clearfix"></div>
	<input type="submit" value="Update">
	</form>
	<p></p>
<?php
}
?>
<a href="<?= dl_facebook_url('entities.php',$type) ?>">Go back to the <?= dl_typestring($type,'ucp') ?> page</a>
</section>

<section id="footer" class="clearfix">
</section>
</body>
</html>