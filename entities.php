<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.php');

$type = $_REQUEST['type'];

$ptype = pg_escape_string($type);
$result = pg_query($dbconn,"SELECT democracylab_entities.entity_id AS eid,
							 democracylab_entities.title AS titl,
							 dlr.ranking AS rnk
	 					FROM democracylab_entities 
						LEFT JOIN (SELECT * FROM democracylab_rankings WHERE democracylab_rankings.user_id = $democracylab_user_id) AS dlr
						ON democracylab_entities.entity_id = dlr.entity_id
						WHERE democracylab_entities.type = '$ptype'
						ORDER BY dlr.ranking");
$entities = array();
while($row = pg_fetch_object($result)) { 
	$entities[] = array( 'id' => $row->eid, 'title' => $row->titl, 'rank' => $row->rnk );
}

switch($type) {
	case 1: goto skip_your_query;
	case 2: $yourtype = 1; break;
	case 3: $yourtype = 2; break;
}
$result = pg_query($dbconn,"SELECT democracylab_entities.entity_id AS eid,
							 democracylab_entities.title AS titl,
							 dlr.ranking AS rnk
	 					FROM democracylab_entities 
						LEFT JOIN (SELECT * FROM democracylab_rankings WHERE democracylab_rankings.user_id = $democracylab_user_id) AS dlr
						ON democracylab_entities.entity_id = dlr.entity_id
						WHERE democracylab_entities.type = '$yourtype'
						  AND dlr.ranking > 0
						ORDER BY dlr.ranking");
$yourentities = array();
while($row = pg_fetch_object($result)) { 
	$yourentities[] = array( 'id' => $row->eid, 'title' => $row->titl, 'rank' => $row->rnk );
}
skip_your_query:

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="stylesheets/screen.css" media="screen">
	<title><?php echo(idx($app_info, 'name')) ?> <?= dl_typestring($type,'ucp') ?></title>
	<?php echo('<meta property="fb:app_id" content="' . AppInfo::appID() . '" />'); ?>
	<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
</head>
<body>
<header id="blank-header" class="clearfix">
</header>

<section id="issue-section" class="clearfix">
	<div class="icon"></div>
	<div class="title">Capital Improvement Fund</div>
</section>

<?php if($type == 1) { ?>
	<section id="value-section" class="clearfix">
		<div class="icon"></div>
		<div class="title">Values</div>
		<div style="clear: both"></div>
		<p>Values are the beliefs and principles that form the basis of our decisions.
		They are why we think about the world the way we do.</p>
		<p>Below is a list of values. Please order the values you feel most strongly
			about. Order at least one, but there is no need to order them all &mdash; just
			the ones you feel strongly about. Put the value you feel most strongly positive
			about at the top of the positive box, the value you feel second most strongly
			positive about next, etc. For values you feel strongly negative about, use
			the negative box in the same way.</p>
	</section>	
<?php } else if($type == 2) { ?>
	<section id="value-section" class="clearfix">
		<div class="icon"></div>
		<div class="title">Your Values</div>
		<div style="clear: both"></div>
		<?php if(count($yourentities) == 0) { ?>
			<p class="encourage">Objectives are based on our values, so we encourage you
				to <a href="<?= dl_facebook_url('entities.php',1) ?>">explore and rank values</a> before ranking objectives.</p>
		<?php } else { ?>
		<ol class="<?= dl_typestring($yourtype,'lcp') ?>-list entity-list connectedSortable">
			<?php
			foreach($yourentities as $erec) {
				?><li id="entity-<?= $erec['id']?>"><div class="entity-name"><?= $erec['title'] ?></div></li><?php
			}
			?>
		</ol>
		<?php } ?>
	</section>	
	<section id="objective-section" class="clearfix">
		<div class="icon"></div>
		<div class="title">Objectives</div>
		<div style="clear: both"></div>
		<p>Objectives are statements of our goals and priorities. Objectives are based on
		our values, and are statements of what we hope to achieve.</p>
		<p>Below is a list of objectives. Please order the objectives you feel most strongly
			about. Order at least one, but there is no need to order them all &mdash; just
			the ones you feel strongly about. Put the objective you feel most strongly positive
			about at the top of the positive box, the objective you feel second most strongly
			positive about next, etc. For objectives you feel strongly negative about, use
			the negative box in the same way.</p>
	</section>	
<?php } else if($type == 3) { ?>
	<section id="objective-section" class="clearfix">
		<div class="icon"></div>
		<div class="title">Your Objectives</div>
		<div style="clear: both"></div>
		<?php if(count($yourentities) == 0) { ?>
			<p class="encourage">Policies are based on our objectives, so we encourage you
				to <a href="<?= dl_facebook_url('entities.php',2) ?>">explore and rank objectives</a> before ranking policies.</p>
		<?php } else { ?>
		<ol class="<?= dl_typestring($yourtype,'lcp') ?>-list entity-list connectedSortable">
			<?php
			foreach($yourentities as $erec) {
				?><li id="entity-<?= $erec['id']?>"><div class="entity-name"><?= $erec['title'] ?></div></li><?php
			}
			?>
		</ol>
		<?php } ?>
	</section>	
	<section id="policy-section" class="clearfix">
		<div class="icon"></div>
		<div class="title">Policies</div>
		<div style="clear: both"></div>
		<p>Policies are plans of action. They are detailed descriptions of how we
		can achieve our objectives, including a prudent assessment of likely
		costs and benefits.</p>
		<p>Below is a list of policies. Please order the policies you feel most strongly
			about. Order at least one, but there is no need to order them all &mdash; just
			the ones you feel strongly about. Put the policy you feel most strongly positive
			about at the top of the positive box, the policy you feel second most strongly
			positive about next, etc. For policies you feel strongly negative about, use
			the negative box in the same way.</p>
	</section>	
<?php } ?>

<section id="description-section" class="clearfix">
	<div id="description-block" dl_id=0><span class="instructions">See a description by
		hovering over one of the <?= dl_typestring($type,'lcp') ?>.</span></div>
</section>

<section id="sorting-section" class="clearfix">

	<?= dl_facebook_form_fields() ?>
	
	<div id="left-sorting-box">
		<p class="sorting-box-legend"><?= dl_typestring($type,'ucp') ?>:</p>
	<ol class="<?= dl_typestring($type,'lcp') ?>-list entity-list connectedSortable" id="passive-list">
		<?php
		foreach($entities as $erec) {
			if($erec['rank'] == 0) {
				?><li id="entity-<?= $erec['id']?>" class="hover-describe" dl_id="<?= $erec['id'] ?>"><div class="entity-name"><?= $erec['title'] ?></div></li>
			<?php }
		}
		?>
	</ol>
	</div>

	<div id="right-sorting-box">
		<div>
			<p class="sorting-box-legend">You feel positive about:</p>
	<ol class="<?= dl_typestring($type,'lcp') ?>-list entity-list connectedSortable" id="active-positive-list">
		<?php
		foreach($entities as $erec) {
			if($erec['rank'] > 0) {
				?><li id="entity-<?= $erec['id']?>" class="hover-describe" dl_id="<?= $erec['id'] ?>"><div class="entity-name"><?= $erec['title'] ?></div></li>
			<?php }
		}
		?>
	</ol>
		</div>
	<p class="sorting-box-legend second-sorting-box">You feel negative about:</p>
		<div>
	<ol class="<?= dl_typestring($type,'lcp') ?>-list entity-list connectedSortable" id="active-negative-list">
		<?php
		foreach($entities as $erec) {
			if($erec['rank'] < 0) {
				?><li id="entity-<?= $erec['id']?>" class="hover-describe" dl_id="<?= $erec['id'] ?>"><div class="entity-name"><?= $erec['title'] ?></div></li>
			<?php }
		}
		?>
	</ol>
		</div>
	</div>
<script language="javascript">
$(function () {
	$("#passive-list").sortable({
		connectWith: '.connectedSortable',
           dropOnEmpty: true,
           update: function () { 
	
	} }).disableSelection();

	$("#active-positive-list").sortable({
		connectWith: '.connectedSortable',
           dropOnEmpty: true,
           update: function () { 
	
			var data = {};
			$("#active-positive-list li").each(function(i) {
				data[this.id] = (i+1);
			});
			data['type'] = <?= $type ?>;
			data['state'] = "<?= $_REQUEST['state'] ?>";
			data['code'] = "<?= $_REQUEST['code'] ?>";
			data['list'] = 'positive';
			
			$.ajax({
				url: '<?= dl_facebook_url('saveorder_ajax.php') ?>',
				context: document.body,
				data: data,
				type: "POST",
				global: false
			})
	
	} }).disableSelection();

	$("#active-negative-list").sortable({
		connectWith: '.connectedSortable',
           dropOnEmpty: true,
           update: function () { 
	
			var data = {};
			$("#active-negative-list li").each(function(i) {
				data[this.id] = -(i+1);
			});
			data['type'] = <?= $type ?>;
			data['state'] = "<?= $_REQUEST['state'] ?>";
			data['code'] = "<?= $_REQUEST['code'] ?>";
			data['list'] = 'negative';
			
			$.ajax({
				url: '<?= dl_facebook_url('saveorder_ajax.php') ?>',
				context: document.body,
				data: data,
				type: "POST",
				global: false
			})
	
	} }).disableSelection();

	});
</script>
</section>

<section id="adding-section" class="clearfix">
	<a href="<?= dl_facebook_url('index.php') ?>">Return to the overview page</a><br>
	<a href="<?= dl_facebook_url('addentity.php',$type) ?>">Add a new <?= dl_typestring($type,'ucs') ?></a>
	<?php
	$result3 = pg_query($dbconn,"SELECT COUNT(1) FROM democracylab_entities WHERE user_id = $democracylab_user_id");
	$count3 = pg_fetch_array($result3);
	if($democracylab_user_role > 0 || $count3 > 0) { ?>
		&mdash;
		<a style="color: #768BB7" href="<?= dl_facebook_url('editentity.php',$type) ?>">Edit an existing <?= dl_typestring($type,'lcs') ?></a>
		&mdash;
		<a style="color: #768BB7" href="<?= dl_facebook_url('deleteentity.php',$type) ?>">Delete an existing <?= dl_typestring($type,'lcs') ?></a>
	<?php } ?>
</section>

<section id="footer" class="clearfix">
</section>
<?php democracylab_hover_javascript(); ?>
</body>
</html>
