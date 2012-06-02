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
						  AND democracylab_entities.community_id = {$democracylab_community_id}
						  AND democracylab_entities.issue_id = {$democracylab_issue_id}
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
						  AND democracylab_entities.community_id = {$democracylab_community_id}
						  AND democracylab_entities.issue_id = {$democracylab_issue_id}
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
	<link href="stylesheets/jquery-ui.css" rel="stylesheet" type="text/css"/>
	<script src="js/jquery-1.7.1.min.js"></script>
	<script src="js/jquery-ui-1.8.16.min.js"></script>
</head>
<body>
<header id="blank-header" class="clearfix">
</header>

<div id="issue-section" class="clearfix">
	<div class="icon"></div>
	<?php $result = pg_query($dbconn,"SELECT title FROM democracylab_issues WHERE issue_id = $democracylab_issue_id"); 
	$row = pg_fetch_object($result); ?>
	<div class="title"><?= $row->title ?></div>
</div>

<?php if($type == 1) { ?>
	<div id="value-section" class="clearfix">
		<div class="icon"></div>
		<div class="title">Values</div>
		<div style="clear: both"></div>
		<p>Values are the beliefs and principles that form the basis of our decisions. They are why we think about
		the world the way we do.</p>
		<p>
		Below is a list of values. Please order the values you feel most strongly about (as they relate to this issue)
		by dragging a value icon
		on the left to one of the boxes on the right. Order at least one, but there is no need to order them all &mdash;
		just move the ones you feel strongly about. Put the value you feel most positively about at the top of
		the positive box, the value you feel second most strongly about next, etc. If there are any values
		you feel negatively about, drag the most negative to the bottom of the negative box.
		</p>
	</div>	
<?php } else if($type == 2) { ?>
	<div id="value-section" class="clearfix">
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
	</div>	
	<div id="objective-section" class="clearfix">
		<div class="icon"></div>
		<div class="title">Objectives</div>
		<div style="clear: both"></div>
		<p>Objectives are statements of our goals relating to an issue. Objectives are based on our values, and are
		statements of what we hope to achieve.</p>
		<p>Below is a list of objectives. Please order the objectives you feel most strongly about by dragging an
		objective icon on the left to one of the boxes on the right. Order at least one, but there is no need to
		order them all &mdash; just move the ones you feel strongly about. Put the objective you feel most positively
		about at the top of the positive box, the objective you feel second most strongly about next, etc. Drag
		the objective you feel most negatively about to the bottom of the negative box.</p>
	</div>	
<?php } else if($type == 3) { ?>
	<div id="objective-section" class="clearfix">
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
	</div>	
	<div id="policy-section" class="clearfix">
		<div class="icon"></div>
		<div class="title">Policies</div>
		<div style="clear: both"></div>
		<p>Policies are plans of action. They are detailed descriptions of how we can achieve our objectives.</p>
		<p>Below is a list of policies. Please order the policies you feel most strongly about by dragging a policy icon
		on the left to one of the boxes on the right. Order at least one, but there is no need to order them all &mdash;
		just move the ones you feel strongly about. Put the policy you feel most positively about at the top of
		the positive box, the policy you feel second most strongly about next, etc. Drag the policy you feel most
		negatively about to the bottom of the negative box.</p>
	</div>	
<?php } ?>

<div id="description-section" class="clearfix">
	<div id="description-block" dl_id=0><span class="instructions">See a description by
		hovering over one of the <?= dl_typestring($type,'lcp') ?>.</span></div>
</div>

<div id="sorting-section" class="clearfix">

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
			data['list'] = 'positive';
			data['community'] = <?= $democracylab_community_id ?>;
			data['issue'] = <?= $democracylab_community_id ?>;
			
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
			data['list'] = 'negative';
			data['community'] = <?= $democracylab_community_id ?>;
			data['issue'] = <?= $democracylab_community_id ?>;
			
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
</div>

<div id="adding-section" class="clearfix">
<?php if($type == 1) { ?>
	<a href="<?= dl_facebook_url('entities.php',2) ?>">Go to step 2 &ndash; Objectives</a><br>
	<a href="<?= dl_facebook_url('index.php') ?>">Return to the overview page and view my results</a><br>
<?php } else if($type == 2) { ?>
	<a href="<?= dl_facebook_url('entities.php',3) ?>">Go to step 3 &ndash; Policies</a><br>
	<a href="<?= dl_facebook_url('index.php') ?>">Return to the overview page and view my results</a><br>
<?php } else if($type == 3) { ?>
	<a href="<?= dl_facebook_url('index.php') ?>">I'm finished! Return to the overview page and view my results</a><br>
<?php } else { ?>
	<a href="<?= dl_facebook_url('index.php') ?>">Return to the overview page</a><br>
<?php } ?>
	<?php
	$result3 = pg_query($dbconn,"SELECT COUNT(1) FROM democracylab_entities WHERE user_id = $democracylab_user_id");
	$count3 = pg_fetch_array($result3);
	if($democracylab_user_role > 0 || $count3 > 0) { ?>
		<a href="<?= dl_facebook_url('addentity.php',$type) ?>">Add a new <?= dl_typestring($type,'ucs') ?></a>
		&mdash;
		<a style="color: #768BB7" href="<?= dl_facebook_url('editentity.php',$type) ?>">Edit an existing <?= dl_typestring($type,'lcs') ?></a>
		&mdash;
		<a style="color: #768BB7" href="<?= dl_facebook_url('deleteentity.php',$type) ?>">Delete an existing <?= dl_typestring($type,'lcs') ?></a>
	<?php } ?>
</div>

<div id="footer" class="clearfix">
</div>
<?php democracylab_hover_javascript(); ?>
</body>
</html>
