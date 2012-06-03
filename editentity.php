<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.php');

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
	<link href="images/favicon.ico" rel="shortcut icon">
  <link rel="stylesheet" href="stylesheets/screen.css" media="screen">
	<title><?php echo(idx($app_info, 'name')) ?> Edit Existing <?= dl_typestring($type,'ucs') ?></title>
  <?php echo('<meta property="fb:app_id" content="' . AppInfo::appID() . '" />'); ?>
</head>
<body>
<header class="clearfix">
</header>

<?php if(!$entityid) {
	if($type == 1) { ?>
		<div id="value-section" class="clearfix">
			<div class="icon"></div>
			<div class="title">Edit an Existing Value</div>
			<div style="clear: both"></div>
			<?php if($democracylab_user_role == 0) { ?>
				<p>Choose one of the values you've entered:</p>
			<?php } else { ?>
				<p>Choose a value:</p>
			<?php } ?>
	<?php } else if($type == 2) { ?>
		<div id="objective-section" class="clearfix">
			<div class="icon"></div>
			<div class="title">Edit an Existing Objective</div>
			<div style="clear: both"></div>
			<?php if($democracylab_user_role == 0) { ?>
				<p>Choose one of the objectives you've entered:</p>
			<?php } else { ?>
				<p>Choose an objective:</p>
			<?php } ?>
	<?php } else if($type == 3) { ?>
		<div id="policy-section" class="clearfix">
			<div class="icon"></div>
			<div class="title">Edit an Existing Policy</div>
			<div style="clear: both"></div>
			<?php if($democracylab_user_role == 0) { ?>
				<p>Choose one of the policies you've entered:</p>
			<?php } else { ?>
				<p>Choose a policy:</p>
			<?php } ?>
	<?php } 
	$result = pg_query($dbconn, "SELECT * FROM democracylab_entities 
								  WHERE type = {$type} 
								    AND community_id = {$democracylab_community_id}
								    AND issue_id = {$democracylab_issue_id}
								  ORDER BY title");
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
				if($democracylab_user_role > 0 || $row->user_id == $democracylab_user_id) {
					?><option value="<?= $row->entity_id ?>"><?= htmlspecialchars($row->title) ?></option>
				<?php }
  			} ?>
		</select> 
	</form><p></p>
	<?php
} else {
	$result = pg_query($dbconn, "SELECT * FROM democracylab_entities WHERE entity_id = {$entityid}");
	$row = pg_fetch_object($result);
	if($type == 1) { ?>
		<div id="value-section" class="clearfix">
			<div class="icon"></div>
			<div class="title">Edit: <?= htmlspecialchars($row->title) ?></div>
			<div style="clear: both"></div>
			<p></p>
	<?php } else if($type == 2) { ?>
		<div id="objective-section" class="clearfix">
			<div class="icon"></div>
			<div class="title">Edit: <?= htmlspecialchars($row->title) ?></div>
			<div style="clear: both"></div>
			<p></p>
	<?php } else if($type == 3) { ?>
		<div id="policy-section" class="clearfix">
			<div class="icon"></div>
			<div class="title">Edit: <?= htmlspecialchars($row->title) ?></div>
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
	<input type="submit" value="Update this <?= dl_typestring($type,'lcs') ?>">
	&mdash; or
	<a href="<?= dl_facebook_url('deleteentity.php',$type,$entityid) ?>">delete this <?= dl_typestring($type,'lcs') ?></a>
	</form>
	<p></p>
<?php
}
?>
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

</script></body>
</html>