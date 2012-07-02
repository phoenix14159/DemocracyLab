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

		uasort($a2,'rating_cmp');
		$rankings[$idx] = $a2;
	}
}

function list_with_histogram($items) {
	foreach($items as $rec) {
		?><li class="entity-with-histogram hover-describe" dl_id="<?= $rec->id ?>"><div class="entity-name"><?= $rec->title ?></div>
			<canvas class="histogram" width="200" height="15" dl_id="<?= $rec->id ?>" dl_count="<?= $rec->count ?>"></canvas>
			</li><?php
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>DemocracyLab</title>
	<link rel="stylesheet" href="stylesheets/screen.css" media="screen">
	<link href="images/favicon.ico" rel="shortcut icon">
	<script src="js/jquery-1.7.2.js"></script>
	<!--[if IE]><script src="js/excanvas.js"></script><![endif]-->
</head>
<body>
<header class="clearfix">
	<div style="margin-left: -5px; background-image: url(images/dl.png); width: 293px; height: 102px; float: left; margin-right: 20px;"></div>
	<div style="float: left; width: 395px;">
		<h1 style="color: #6485a2; font-size: 210%;">Welcome to our engagement platform</h1>
		<p style="color: black;
		font: 14px/1.5em 'Lucida Grande',Arial,sans-serif;
		line-height: 130%;
		margin-bottom: 5px;">We're creating new tools to help communities make better
		decisions. You can learn more about us at <a href="http://democracylab.org/">democracylab.org</a>.
		</p>
		<p style="color: black;
		line-height: 1;
		font: 14px/1.5em 'Lucida Grande',Arial,sans-serif;
		line-height: 130%;">To participate, 
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
			list_with_histogram($rankings['values']); ?></ol><?php
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
			list_with_histogram($rankings['objectives']); ?></ol><?php
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
			list_with_histogram($rankings['policies']); ?></ol><?php
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
<?php 
$footer_include_description = true;
$footer_include_admin = true;
if($democracylab_issue_id == 2) { } else {
	ob_start(); ?>
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
	<?php
	$footer_extra_text = ob_get_contents();
	ob_end_clean();
}
require_once('lib/footer.php'); ?>
<script>
var G_vmlCanvasManager;
everywhere_image = new Image();
everywhere_image.src = 'images/icon-kiosk.png';

function create_a_histogram(elem,data,show_compare,show_rating) {
	var node = $(elem);
	var count = node.attr("dl_count");
	if( count > 0 ) {
		// get the canvas size
		var width = node.width();
		var height = node.height();
		// compute the coordinates
		var max = 1;
		if(data && data[1] && data[1].length > 0) {
			$.each(data[1],function (idx,ech) {
				if(ech > max) { max = ech; }
			});
		}
		var xoffset = 10;
		var ybase = 1;
		var yinc = (height - 2) / max;
		var xinc = Math.floor((width - xoffset) / 13); // data.length;
        if (G_vmlCanvasManager != undefined) { // ie IE
                G_vmlCanvasManager.initElement(elem);
        }
		var ctx = elem.getContext("2d");
		if( ctx ) {
			ctx.clearRect(0, 0, width, height);
		  
			// draw the count of users
			ctx.font = "10px sans-serif";
			ctx.fillStyle = "rgb(150,150,150)";
			if( !$.browser.msie ) {
				ctx.fillText( count, 0, height - 4 );
			}
			// draw the zero line
			ctx.strokeStyle = 'rgb(120,120,120)';
			ctx.lineWidth = 1;
			ctx.beginPath();
			ctx.moveTo( (5 * xinc) + xoffset,5);
			ctx.lineTo( (5 * xinc) + xoffset,90);
			ctx.stroke();
			// draw each box
			var colors = ["rgb(255,170,170)","rgb(255,190,190)","rgb(255,210,210)","rgb(255,230,230)","rgb(255,250,250)",
						  "rgb(250,255,250)","rgb(240,255,240)","rgb(230,255,230)","rgb(220,255,220)","rgb(210,255,210)","rgb(200,255,200)","rgb(190,255,190)","rgb(180,255,180)"];
			if(data && data[1] && data[1].length > 0) {
				$.each(data[1],function (idx,ech) {
					if(show_compare && show_rating == idx) {
						ctx.fillStyle = 'rgb(255,255,0)';
						ctx.fillRect( (idx * xinc) + xoffset - 2, 0, xinc + 3, height);
					}
					ctx.fillStyle = "rgb(0,0,0)";
					ctx.fillRect( (idx * xinc) + xoffset, height, xinc - 1, -(ech * yinc + 1));
					if(ech > 0) {
						if(show_compare && show_rating == idx) {
							ctx.fillStyle = 'rgb(255,255,0)';
						} else {
							ctx.fillStyle = colors[idx];
						}
						ctx.fillRect( (idx * xinc) + xoffset + 1, height - 1, xinc - 3, -(ech * yinc + 1)+2);
					}
				});
				if(data[0] >= 0) {
					ctx.fillStyle = "rgb(0,0,0)";
					ctx.beginPath();
					ctx.arc( (data[0] * xinc) + xoffset + (xoffset / 2) + 2, height / 2, 3, 0, Math.PI*2, false); 
					ctx.closePath();
					ctx.fill();
				}
			} else {
				$.each([0,0,0,0,0,0,0,0,0,0,0,0,0],function (idx,ech) {
					if(show_compare && show_rating == idx) {
						ctx.fillStyle = 'rgb(255,255,0)';
						ctx.fillRect( (idx * xinc) + xoffset - 1, 0, xinc + 1, height);
					}
					ctx.fillStyle = "rgb(0,0,0)";
					var yh = Math.ceil(ech * yinc + 1);
					ctx.fillRect( (idx * xinc) + xoffset, height-yh, xinc - 1, yh);
				});
				if(data && data[0] && data[0] >= 0) {
					ctx.fillStyle = "rgb(0,0,0)";
					ctx.beginPath();
					ctx.arc( (data[0] * xinc) + xoffset + (xoffset / 2) + 2, height / 2, 3, 0, Math.PI*2, false); 
					ctx.closePath();
					ctx.fill();
				}
			}
			/*
			if(show_compare) {
				ctx.drawImage(everywhere_image,(show_rating * xinc) + xoffset,0);
			}
			*/
		} else {
			//backup for no canvas
		}
	}
}
$(function () {
	
	$('#entities-summary').attr('dl_histos','X');
	var data = {};
	data['community'] = <?= $democracylab_community_id ?>;
	data['issue'] = <?= $democracylab_community_id ?>;
	$.ajax({
		url: '<?= dl_facebook_url('getrankings_ajax.php') ?>',
		context: document.body,
		dataType: 'json',
		data: data,
		type: "POST",
		global: false,
		success: function (rtrndata) {
			$('#entities-summary').data('rating_X',rtrndata);
			$(".histogram").each( function (index,elem) {
				var node = $(elem);
				var id = node.attr("dl_id");
				var arr = rtrndata['' + id];
				create_a_histogram(elem,arr);
			});
		}
	});

	$(".histogram").each( function (index,elemh) {
		var node = $(elemh);
		var count = node.attr("dl_count");
		if( count > 0 ) {
			// get the canvas size
			var width = node.width();
			var height = node.height();
			var xoffset = 10;
			var xinc = Math.floor((width - xoffset) / 13); // data.length;
			$(elemh).mousemove( function(event) {
				var theid = $(elemh).attr('dl_id');
				var therating = Math.floor((event.pageX - node.offset().left - xoffset) / xinc) - 5;
				if( therating < -5 ) { therating = -5; }
				if( therating > 7 ) { therating = 7; }
				var wasid = $('#entities-summary').attr('dl_histos');
				var newid = theid + ',' + therating;
				if(newid == wasid) { return; /*no need to draw again*/ }
				$('#entities-summary').attr('dl_histos',newid );
				var newdata = $(elemh).data('rating_' + ((therating < 0) ? ('m' + (0-therating)) : therating));
				if(newdata) {
					$(".histogram").each( function (index,elem) {
						var node = $(elem);
						var id = node.attr("dl_id");
						var arr = newdata['' + id];
						create_a_histogram(elem,arr,theid == id,therating + 5);
					});
				} else {
					var data = {};
					data['community'] = <?= $democracylab_community_id ?>;
					data['issue'] = <?= $democracylab_community_id ?>;
					data['id'] = theid;
					data['rating'] = therating;
					$.ajax({
						url: '<?= dl_facebook_url('getrankings_ajax.php') ?>',
						context: document.body,
						dataType: 'json',
						data: data,
						type: "POST",
						global: false,
						success: function (rtrndata) {
							$(elemh).data('rating_' + ((therating < 0) ? ('m' + (0-therating)) : therating),rtrndata);
							var stillid = $('#entities-summary').attr('dl_histos');
							if(stillid == newid) {
								$(".histogram").each( function (index,elem) {
									var node = $(elem);
									var id = node.attr("dl_id");
									var arr = rtrndata['' + id];
									create_a_histogram(elem,arr,theid == id,therating + 5);
								});
							}
						}
					});
				}
			});
			$(elemh).mouseleave( function(event) {
				$('#entities-summary').attr('dl_histos','X');
				var newdata = $('#entities-summary').data('rating_X');
				if(newdata) {
					$(".histogram").each( function (index,elem) {
						var node = $(elem);
						var id = node.attr("dl_id");
						var arr = newdata['' + id];
						create_a_histogram(elem,arr);
					});
				} else {
					var data = {};
					data['community'] = <?= $democracylab_community_id ?>;
					data['issue'] = <?= $democracylab_community_id ?>;
					$.ajax({
						url: '<?= dl_facebook_url('getrankings_ajax.php') ?>',
						context: document.body,
						dataType: 'json',
						data: data,
						type: "POST",
						global: false,
						success: function (rtrndata) {
							$('#entities-summary').data('rating_X',rtrndata);
							$(".histogram").each( function (index,elem) {
								var node = $(elem);
								var id = node.attr("dl_id");
								var arr = rtrndata['' + id];
								create_a_histogram(elem,arr);
							});
						}
					});
				}
			});
		}
	});
});
</script>
<?php democracylab_hover_javascript(); ?>
</body>
</html>
