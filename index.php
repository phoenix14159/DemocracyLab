<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.php');

// Get the rankings data
$result = pg_query($dbconn, "SELECT type, COUNT(1) FROM democracylab_rankings 
							  WHERE user_id = {$democracylab_user_id} 
							    AND community_id = {$democracylab_community_id}
							    AND issue_id = {$democracylab_issue_id}
							  GROUP BY type");
$rankings = array();
$rankings['values'] = 0;
$rankings['objectives'] = 0;
$rankings['policies'] = 0;
function rating_cmp($a,$b) {
	if($a->avg > $b->avg) return -1;
	if($a->avg < $b->avg) return 1;
	return 0;
}
while($row = pg_fetch_array($result)) {
	if($row[1] != 0) {
		$idx = '?';
		if($row[0] == 1) $idx = 'values';
		if($row[0] == 2) $idx = 'objectives';
		if($row[0] == 3) $idx = 'policies';

		$a2 = array();
		$ids = array();
		$result2 = pg_query($dbconn, "SELECT entity_id, title FROM democracylab_entities 
			WHERE type = {$row[0]} 
		    AND community_id = {$democracylab_community_id}
		    AND issue_id = {$democracylab_issue_id}");
		while($row2 = pg_fetch_object($result2)) {
			$obj = new stdClass();
			$obj->id = $row2->entity_id;
			$obj->title = $row2->title;
			$obj->count = 0;
			$obj->min = 0;
			$obj->max = 0;
			$obj->std = 0;
			$obj->avg = -5;
			$obj->userval = 0;
			$a2[$obj->id] = $obj;
			$ids[] = $obj->id;
		}
		
		$ids = join(',',$ids);
		$result2 = pg_query($dbconn, "SELECT entity_id, MIN(rating), AVG(rating), STDDEV(rating), MAX(rating), COUNT(1)
								FROM democracylab_rankings
								WHERE entity_id in ({$ids})
								  AND ranking != 0
								GROUP BY entity_id");
		while($row2 = pg_fetch_array($result2)) {
			$a2[$row2[0]]->id  = $row2[0];
			$a2[$row2[0]]->min = $row2[1];
			$a2[$row2[0]]->avg = $row2[2];
			$a2[$row2[0]]->std = $row2[3];
			$a2[$row2[0]]->max = $row2[4];
			$a2[$row2[0]]->userval = 0;
			$a2[$row2[0]]->count = $row2[5];
		}

		$result4 = pg_query($dbconn, "SELECT entity_id, rating FROM democracylab_rankings WHERE entity_id IN ($ids) AND user_id = $democracylab_user_id");
		while($row4 = pg_fetch_array($result4)) {
			$a2[$row4[0]]->userval = $row4[1];
		}

		uasort($a2,'rating_cmp');
		$rankings[$idx] = $a2;
	}
}

function list_with_boxplots($items) {
	foreach($items as $rec) {
		?><li class="entity-with-boxplot hover-describe" dl_id="<?= $rec->id ?>"><div class="entity-name"><?= $rec->title ?></div>
			<canvas class="boxplot" width="200" height="15"
		        dl_count="<?= $rec->count ?>"
		        dl_min="<?= $rec->min ?>"
		        dl_max="<?= $rec->max ?>"
		        dl_avg="<?= $rec->avg ?>"
		        dl_std="<?= $rec->std ?>"
		        dl_value="<?= $rec->userval ?>"></canvas></li><?php
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo(idx($app_info, 'name')) ?></title>
	<link rel="stylesheet" href="stylesheets/screen.css" media="screen">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<?php echo('<meta property="fb:app_id" content="' . AppInfo::appID() . '" />'); ?>
</head>
<body>
<header class="clearfix">
	<div style="margin-left: -5px; background-image: url(images/dl.png); width: 293px; height: 102px; float: left; margin-right: 20px;"></div>
	<div style="float: left; width: 395px;">
		<h1 style="color: #6485a2; font-size: 220%;">Welcome to DemocracyLab</h1>
		<p style="color: black;
		line-height: 1;
		font: 14px/1.5em 'Lucida Grande',Arial,sans-serif">Welcome to our Facebook application. 
		We're creating new tools to help people,
		communities, and organizations make better collective decisions.
		Learn more at <a href="http://democracylab.org">democracylab.org</a>.
		</p>
	</div>
</header>

<section id="issue-section" class="clearfix">
	<div class="icon"></div>
	<?php $result = pg_query($dbconn,"SELECT title FROM democracylab_issues WHERE issue_id = $democracylab_issue_id"); 
	$row = pg_fetch_object($result); ?>
	<div class="title"><?= $row->title ?> <a href="<?= dl_facebook_url('chooseissue.php') ?>" style="font-size: 8pt;">(change issue)</a></div>
</section>


<?php if(!($rankings['values'] || $rankings['objectives'] || $rankings['policies'] )) { 
	if($democracylab_issue_id == 2) { ?>
		<section id="how-it-works-section" class="clearfix">
			<p>The aim of this tool is to identify issues, stimulate ideas, and create a
			dialogue around the UP Capital Improvement Fund (CIF), also known as the
			Major Project Fund. Please use this application to express and group the
			values, objectives, and policies related to the CIF. Thank you for input!
			</p>
		</section>	
	<?php } else { ?>
	<section id="how-it-works-section" class="clearfix">
		<p>DemocracyLab helps facilitate a structured discussion about the issues.
			Each participant ranks the values, objectives, and policies that are
			personally important. (More explanation needed.)
		</p>
	</section>	
	<?php } ?>
<?php } else { ?>
	<section id="description-section" class="clearfix">
		<div id="description-block" dl_id=0><span class="instructions">See a description by
			hovering over a value, objective or policy.</span></div>
	</section>
<?php } ?>
    <section id="entities-summary-section" class="clearfix">
	<div class="entity-list">
		<?php
		if($rankings['values']) {
			?><a href="<?= dl_facebook_url('entities.php',1) ?>">Explore Values</a><ol class="values-list"><?php
			list_with_boxplots($rankings['values']); ?></ol><?php
		} else {
			?><a href="<?= dl_facebook_url('entities.php',1) ?>">Please classify some Values</a>
			<p class="description">
				Values are the beliefs and principles that form the basis of our decisions.
				They are why we think about the world the way we do.
			</p>
			<p class="description">
				To participate in the structured discussion, you will need to rank 
				the values about which you feel most strongly.
			</p><?php
		}
		?>
	</div>
	<div class="entity-list">
		<?php
		if($rankings['objectives']) {
			?><a href="<?= dl_facebook_url('entities.php',2) ?>">Explore Objectives</a><ol class="objectives-list"><?php
			list_with_boxplots($rankings['objectives']); ?></ol><?php
		} else {
			?><a href="<?= dl_facebook_url('entities.php',2) ?>">Please classify some Objectives</a>
			<p class="description">
				Objectives are statements of our goals and priorities. Objectives are based on
				our values, and are statements of what we hope to achieve.
			</p>
			<p class="description">
				To participate in the structured discussion, you will need to rank the
				objectives that the best statements of your goals for this issue.
			</p><?php
		}
		?>
	</div>
	<div class="entity-list">
		<?php
		if($rankings['policies']) {
			?><a href="<?= dl_facebook_url('entities.php',3) ?>">Explore Policies</a><ol class="policies-list"><?php
			list_with_boxplots($rankings['policies']); ?></ol><?php
		} else {
			?><a href="<?= dl_facebook_url('entities.php',3) ?>">Please classify some Policies</a>
			<p class="description">
				Policies are plans of action. They are detailed descriptions of how we
				can achieve our objectives, including a prudent assessment of likely
				costs and benefits.
			</p>
			<p class="description">
				To participate in the structured discussion, you will need to rank the
				policies that you believe are the best for this issue.
			</p><?php
		}
		?>
	</div>
    </section>
    <section id="footer" class="clearfix">
	<p>DemocracyLab is a 501(c)(3) nonprofit organization aspiring to revolutionize the nature of political
		dialogue. We believe privacy is important, especially when talking about politics. We treat your
		personal information with the care and respect you deserve, and will never share any details about you
		or your political views without your permission.
		<!-- Please reference our privacy policy and terms of use for more information --></p>
	<?php
	if($democracylab_user_role > 0) {
		?><p style="text-align: center; color: black; border-top: thin dotted red; margin-top: 1em;">admin: <a href="<?= dl_facebook_url('users.php') ?>">users</a>,
		<a href="<?= dl_facebook_url('communities.php') ?>">communities</a><?php
	}
	?>
	</section>
<script>
function create_a_boxplot(elem) {
	var node = $(elem);
	var count = node.attr("dl_count");
	if( count > 0 ) {
		var min = parseInt(node.attr("dl_min"));
		var max = parseInt(node.attr("dl_max"));
		var std = parseInt(node.attr("dl_std"));
		var avg = parseInt(node.attr("dl_avg"));
		var value = parseInt(node.attr("dl_value"));
		// get the canvas size
		var width = node.width() - 12;
		var height = node.height();
		// compute the coordinates
		var midy = Math.floor(height / 2) - 1; // y-axis midpoint
		var zerox = Math.floor((width / 15) * 5) + 10; // x-axis zero point
		var minx = Math.floor((width / 15) * (min + 5)) + 10; // x-axis min point
		var maxx = Math.floor((width / 15) * (max + 5)) + 10; // x-axis max point
		var stdx1 = Math.floor((width / 15) * (avg - std + 5)) + 10; // x-axis lower stddev point
		var stdx2 = Math.floor((width / 15) * (avg + std + 5)) + 10; // x-axis upper stddev point
		var stdy1 = 3; // y-axis of stddev rect top
		var stdy2 = height - 3; // y-axis of stddev rect bottom (actually, the height of the stddev rect)
		// leave enough room to draw something
		if( minx == maxx ) {
			if( max == 10 ) minx = minx - 2;
			else if( min == -5 ) maxx = maxx + 2;
			else { minx = minx - 1; maxx = maxx + 1; }
		}
		if( stdx1 > stdx2 - 4 ) {
			if( (avg + std) >= 9 ) { stdx1 = stdx1 - 4; }
			else if( (avg - std) <= -4 ) { stdx2 = stdx2 + 4; }
			else { stdx1 = stdx1 - 3; stdx2 = stdx2 + 1; }
		}
		// draw the min-max line
		var ctx = elem.getContext("2d");
		if( ctx ) {
			// draw the zero line
			ctx.fillStyle = "rgb(0,0,0)";
			ctx.fillRect( zerox, 1, 1, height-2 );
			// draw the count of users
			ctx.fillStyle = "rgb(150,150,150)";
			if( !$.browser.msie ) {
				ctx.fillText( count, 0, height - 4 );
			}
			// draw the min-max line
			ctx.fillRect( minx, midy, maxx-minx, 3 );
			// draw the std dev box
			ctx.fillRect( stdx1, stdy1, stdx2-stdx1+1, stdy2-stdy1);
			ctx.fillStyle = "rgb(230,230,230)";
			ctx.fillRect( stdx1+2, stdy1+2, stdx2-stdx1-3, stdy2-stdy1-4);
			if( value ) {
				var vx = Math.floor((width / 15) * (value + 5)) + 10; // x-axis value point
				if( value == 10 ) vx = vx - 2;
				if( value == -5) vx = vx + 2;
				ctx.fillStyle = "rgb(0,0,0)";
				if( $.browser.msie ){
					ctx.fillRect( vx-2, midy-3, 5, 9 );
				} else {
					ctx.beginPath();
					ctx.arc(vx,midy+1,4,0,6.3,0);
					ctx.closePath();
					ctx.fill();
				}
			}
		} else {
			//backup for no canvas
		}
	}
}
$(function () {
	$(".boxplot").each( function (index,elem) {
		create_a_boxplot(elem);
	});
} );
</script>
<?php democracylab_hover_javascript(); ?>
  </body>
</html>
