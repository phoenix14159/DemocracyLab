<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.inc');

$type = $_REQUEST['type'];

$ptype = pg_escape_string($type);
$result = pg_query($dbconn,"SELECT democracylab_entities.entity_id AS eid,
							 democracylab_entities.title AS titl,
							 dlr.ranking AS rnk
	 					FROM democracylab_entities 
						LEFT JOIN (SELECT * FROM democracylab_rankings WHERE democracylab_rankings.user_id = $democracylab_user_id) AS dlr
						ON democracylab_entities.entity_id = dlr.entity_id
						WHERE democracylab_entities.type = '$ptype'");
$rtrn = array();
$entities = array();
while($row = pg_fetch_object($result)) { 
	$entities[] = array( 'id' => $row->eid, 'title' => $row->titl, 'rank' => $row->rnk );
}

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
	<div class="title">Our First Issue</div>
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
			the ones you feel strongly about. Use 1, 2, 3, etc for your strongest positive, second
			strongest postive, third strongest, etc. For strongly negative feelings, use
			-1, -2, -3, etc.</p>
	</section>	
<?php } else if($type == 2) { ?>
	<section id="value-section" class="clearfix">
		<div class="icon"></div>
		<div class="title">Your Values</div>
		<div style="clear: both"></div>
		<p>(Soon this section will contain the values you chose.)</p>
	</section>	
	<section id="objective-section" class="clearfix">
		<div class="icon"></div>
		<div class="title">Objectives</div>
		<div style="clear: both"></div>
		<p>Objectives are statements of our goals and priorities. Objectives are based on
		our values, and are statements of what we hope to achieve.</p>
		<p>Below is a list of objectives. Please order the objectives you feel most strongly
			about. Order at least one, but there is no need to order them all &mdash; just
			the ones you feel strongly about. Use 1, 2, 3, etc for your strongest positive, second
			strongest postive, third strongest, etc. For strongly negative feelings, use
			-1, -2, -3, etc.</p>
	</section>	
<?php } else if($type == 3) { ?>
	<section id="objective-section" class="clearfix">
		<div class="icon"></div>
		<div class="title">Your Objectives</div>
		<div style="clear: both"></div>
		<p>(Soon this section will contain the objectives you chose.)</p>
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
			the ones you feel strongly about. Use 1, 2, 3, etc for your strongest positive, second
			strongest positive, third strongest, etc. For strongly negative feelings, use
			-1, -2, -3, etc.</p>
	</section>	
<?php } ?>

<section id="sorting-section" class="clearfix">

	<?= dl_facebook_form_fields() ?>
	
	<div style="width: 300px; float: left; padding: 10px;">
		<p style="padding-left: 8px; color: gray"><?= dl_typestring($type,'ucp') ?>:</p>
	<ol style="min-height: 133px; border: thin solid gray; padding: 10px; border-radius: 10px;"
		class="<?= dl_typestring($type,'lcp') ?>-list entity-list connectedSortable" id="passive-list">
		<?php
		foreach($entities as $erec) {
			if($erec['rank'] == 0) {
				?><li id="entity-<?= $erec['id']?>"><?= $erec['title'] ?></li>
			<?php }
		}
		?>
	</ol>
	</div>

	<div style="width: 300px; float: left; padding: 10px;">
		<div>
			<p style="padding-left: 8px; color: gray">You feel positive about:</p>
	<ol style="border: thin solid gray; min-height: 40px; padding: 10px; border-radius: 10px;" 
		class="<?= dl_typestring($type,'lcp') ?>-list entity-list connectedSortable" id="active-positive-list">
		<?php
		foreach($entities as $erec) {
			if($erec['rank'] > 0) {
				?><li id="entity-<?= $erec['id']?>"><?= $erec['title'] ?></li>
			<?php }
		}
		?>
	</ol>
		</div>
	<p style="padding-left: 8px; color: gray; margin-top: 10px;">You feel negative about:</p>
		<div>
	<ol style="border: thin solid gray; min-height: 40px; padding: 10px; border-radius: 10px;" 
		class="<?= dl_typestring($type,'lcp') ?>-list entity-list connectedSortable" id="active-negative-list">
		<?php
		foreach($entities as $erec) {
			if($erec['rank'] < 0) {
				?><li id="entity-<?= $erec['id']?>"><?= $erec['title'] ?></li>
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
			data['user'] = <?= $democracylab_user_id ?>;
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
			data['user'] = <?= $democracylab_user_id ?>;
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
</section>

<section id="footer" class="clearfix">
</section>
</body>
</html>