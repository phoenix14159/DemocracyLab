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
	<script src="lib/jquery-1.7.1.min.js"></script>
	<?php echo('<meta property="fb:app_id" content="' . AppInfo::appID() . '" />'); ?>
	<!--[if IE]><script src="js/excanvas.js"></script><![endif]-->
</head>
<body>
<header class="clearfix">
	<div style="margin-left: -5px; background-image: url(images/dl.png); width: 293px; height: 102px; float: left; margin-right: 20px;"></div>
	<div style="float: left; width: 395px;">
		<h1 style="color: #6485a2; font-size: 210%;">Welcome to our engagement platform</h1>
		<p style="color: black;
		line-height: 1;
		font: 14px/1.5em 'Lucida Grande',Arial,sans-serif;
		margin-bottom: 5px;">We're creating new tools to help communities make better
		decisions. You can learn more about us at <a href="http://democracylab.org/">democracylab.org</a>.
		</p>
		<p style="color: black;
		line-height: 1;
		font: 14px/1.5em 'Lucida Grande',Arial,sans-serif">To participate, 
		we'll ask you to share the values, objectives, and
		policies that most closely reflect your thinking on the current
		issue. Email your feedback to <a href="mailto:info@democracylab.org">info@democracylab.org</a>.
		</p>
	</div>
</header>

<div id="issue-section" class="clearfix">
	<div class="icon"></div>
	<?php $result = pg_query($dbconn,"SELECT title FROM democracylab_issues WHERE issue_id = $democracylab_issue_id"); 
	$row = pg_fetch_object($result); ?>
	<div class="title"><?= $row->title ?> <a href="<?= dl_facebook_url('chooseissue.php') ?>" style="font-size: 8pt;">(change issue)</a></div>
</div>


<?php 
	if($democracylab_issue_id == 2) { 
		if(!($rankings['values'] || $rankings['objectives'] || $rankings['policies'] )) {  ?>
		<div id="how-it-works-section" class="clearfix">
			<p>The aim of this tool is to identify issues, stimulate ideas, and create a
			dialogue around the UP Capital Improvement Fund (CIF), also known as the
			Major Project Fund. Please use this application to express and group the
			values, objectives, and policies related to the CIF. Thank you for input!
			</p>
		</div>	
	<?php } else { ?>
		<div id="description-section" class="clearfix">
			<div id="description-block" dl_id=0><span class="instructions">See a description by
				hovering over a value, objective or policy.</span></div>
		</div>
	<?php } }
	else { ?>
	<div id="how-it-works-section" class="clearfix">
		<p>
		A recent <a href="http://www.leg.state.or.us/comm/lro/2012_publications_reports/Basic_Facts_2012.pdf" target="_new">research report</a> 
		by Oregon's Legislative Revenue Office included the following table and
		comments* comparing Oregon's tax system to other states across the country:
<style>
table { border: thin solid #CCC;}
th { font-weight: bold; text-align: left; padding-left: 10px; padding-right: 10px; border: thin solid #CCC;}
td { padding-left: 10px; padding-right: 10px; border: thin solid #CCC;}
</style>
<center><table>
	<tr><th>REVENUE CATEGORIES</th><th>$ PER PERSON</th><th>RANK AMONG THE STATES</th></tr>
	<tr><th>TOTAL TAXES</th><td>$3,275</td><td>39th</td></tr>
	<tr><th>PERSONAL INCOME TAX</th><td>$1,356</td><td>5th</td></tr>
	<tr><th>CORPORATE INCOME TAX</th><td>$75</th><td>38th</td></tr>
	<tr><th>PROPERTY TAX</th><td>$1,166</th><td>28th</td></tr>
	<tr><th>GENERAL SALES TAX</th><td>0</th><td>50th</td></tr>
	<tr><th>SELECTIVE SALES TAXES</th><td>$319</th><td>44th</td></tr>
	<tr><th>OTHER TAXES</th><td>$359</th><td>12th</td></tr>
</table></center>
		</p>
	</div>	
<?php	if(($rankings['values'] || $rankings['objectives'] || $rankings['policies'] )) {  ?>
	<div id="description-section" class="clearfix">
		<div id="description-block" dl_id=0><span class="instructions">See a description by
			hovering over a value, objective or policy.</span></div>
	</div>
<?php } 
} ?>
    <div id="entities-summary" class="clearfix">
	<div class="entity-list" style="width: 238px; float: left;">
		<?php
		if($rankings['values']) {
			?><a href="<?= dl_facebook_url('entities.php',1) ?>">Step 1 - Share Your Values</a><ol class="values-list"><?php
			list_with_boxplots($rankings['values']); ?></ol><?php
		} else {
			?><a href="<?= dl_facebook_url('entities.php',1) ?>">Step 1 - Share Your Values</a>
			<p class="description">
				Values are the beliefs and principles that form the basis of our decisions. They are why we think about
				the world the way we do.
				</p>
				<p class="description">
				To participate, you'll rank the values you feel most strongly about.
			</p><?php
		}
		?>
	</div>
	<div class="entity-list" style="width: 238px; float: left;">
		<?php
		if($rankings['objectives']) {
			?><a href="<?= dl_facebook_url('entities.php',2) ?>">Step 2 - Prioritize Objectives</a><ol class="objectives-list"><?php
			list_with_boxplots($rankings['objectives']); ?></ol><?php
		} else {
			?><a href="<?= dl_facebook_url('entities.php',2) ?>">Step 2 - Prioritize Objectives</a>
			<p class="description">
				Objectives are statements of our goals relating to an issue. They are based on our values, and
				are statements of what we hope to achieve. 
			</p>
			<p class="description">
				To participate, you'll rank the objectives that are most
				important <br>to you.
			</p><?php
		}
		?>
	</div>
	<div class="entity-list" style="width: 238px; float: left;">
		<?php
		if($rankings['policies']) {
			?><a href="<?= dl_facebook_url('entities.php',3) ?>">Step 3 - Evaluate Policies</a><ol class="policies-list"><?php
			list_with_boxplots($rankings['policies']); ?></ol><?php
		} else {
			?><a href="<?= dl_facebook_url('entities.php',3) ?>">Step 3 - Evaluate Policies</a>
			<p class="description">
				Policies are plans of action. They are detailed descriptions of how we can achieve our objectives. 
			</p>
			<p class="description">
				To participate, you'll rank the <br>policies that you believe are best <br>for this issue.
			</p><?php
		}
		?>
	</div>
	<div class="clearfix"></div>
    </div>
    <div id="footer" class="clearfix">
<?php	if(($rankings['values'] || $rankings['objectives'] || $rankings['policies'] )) {  ?>
	<p style="text-align: center; color: #444;"><a style="font-size: 90%; font-style: italic;" target="_new" href="http://stattrek.com/statistics/charts/boxplot.aspx">How do I read box plot diagrams?</a><br><br></p>
<?php } ?>	

	<?php if($democracylab_issue_id == 2) { } else { ?>
		<p style="color: #444;">* Oregon's overall state and local tax burden ranks 39th on a per person basis. However, the state
		personal income tax burden is among the highest in the nation at $1,356 per person. The ranking for
		corporate income taxes is relatively low at #38, but this is prior to the imposition of higher corporate tax
		rates and a new corporate minimum called for in Measure 67. Property taxes are near the middle of
		the states, ranking # 28. The state tax burden on consumption (general sales plus selective sales) is
		the lowest in the country. In addition to being one of five states without a general sales tax, Oregon
		ranks 44th in selective sales tax collections per person. Selective sales taxes include gasoline taxes,
		tobacco taxes, alcoholic beverage taxes, real estate transfer taxes and other excise taxes on specific
		purchases. It also includes health provider taxes which have risen in Oregon and other states in recent
		years. The other tax category includes severance taxes and estate taxes.<br><br>
		</p>
	<?php } ?>
	
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
	</div>
<script>
var G_vmlCanvasManager;
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
        if (G_vmlCanvasManager != undefined) { // ie IE
                G_vmlCanvasManager.initElement(elem);
        }
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
