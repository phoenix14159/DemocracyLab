<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.inc');

// Get the rankings data
$result = pg_query($dbconn, "SELECT type, COUNT(1) FROM democracylab_rankings WHERE user_id = $democracylab_user_id GROUP BY type");
$rankings = array();
$rankings['values'] = 0;
$rankings['objectives'] = 0;
$rankings['policies'] = 0;
function rating_cmp($a,$b) {
	if($a[2] > $b[2]) return -1;
	if($a[2] < $b[2]) return 1;
	return 0;
}
while($row = pg_fetch_array($result)) {
	if($row[1] != 0) {
		$idx = '?';
		if($row[0] == 1) $idx = 'values';
		if($row[0] == 2) $idx = 'objectives';
		if($row[0] == 3) $idx = 'policies';
		$result2 = pg_query($dbconn, "SELECT entity_id, MIN(rating), AVG(rating), VARIANCE(rating), MAX(rating)
								FROM democracylab_rankings
								WHERE type = '{$row[0]}'
								  AND ranking != 0
								GROUP BY entity_id");
		$a2 = array();
		$ids = array();
		while($row2 = pg_fetch_array($result2)) {
			$a2[$row2[0]] = array( $row2[0], '?', $row2[1], $row2[2], $row2[3], $row2[4], 0);
			$ids[] = $row2[0];
		}
		$ids = join(',',$ids);
		$result3 = pg_query($dbconn, "SELECT entity_id, title FROM democracylab_entities WHERE entity_id IN ($ids)");
		while($row3 = pg_fetch_array($result3)) {
			$a2[$row3[0]][1] = $row3[1];
		}
		$result4 = pg_query($dbconn, "SELECT entity_id, rating FROM democracylab_rankings WHERE entity_id IN ($ids) AND user_id = $democracylab_user_id");
		while($row4 = pg_fetch_array($result4)) {
			$a2[$row4[0]][6] = $row4[1];
		}
		uasort($a2,'rating_cmp');
		$rankings[$idx] = $a2;
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo(idx($app_info, 'name')) ?></title>
	<link rel="stylesheet" href="stylesheets/screen.css" media="screen">
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
	<div class="title">Oregon's Budget</div>
</section>

<?php if(!($rankings['values'] || $rankings['objectives'] || $rankings['policies'] )) { ?>
	<section id="how-it-works-section" class="clearfix">
		<p>DemocracyLab helps facilitate a structured discussion about the issues.
			Each participant ranks the values, objectives, and policies that are
			personally important. (More explanation needed.)
		</p>
	</section>	
<?php }?>
    <section id="entities-summary-section" class="clearfix">
	<div class="entity-list">
		<?php
		if($rankings['values']) {
			?><a href="<?= dl_facebook_url('entities.php',1) ?>">Explore Values</a><ol class="values-list"><?php
			foreach($rankings['values'] as $rec) {
				?><li><?= $rec[1] ?> <?php /* = (<?= $rec[6] ?>) <?= $rec[2] ?> .. <?= $rec[3] ?> .. <?= $rec[4] ?> .. <?= $rec[5] ?> */?></li><?php
			}
			?></ol><?php
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
			foreach($rankings['objectives'] as $rec) {
				?><li><?= $rec[1] ?> <?php /* = (<?= $rec[6] ?>) <?= $rec[2] ?> .. <?= $rec[3] ?> .. <?= $rec[4] ?> .. <?= $rec[5] ?> */?></li><?php
			}
			?></ol><?php
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
			foreach($rankings['policies'] as $rec) {
				?><li><?= $rec[1] ?> <?php
				/* (<?= $rec[6] ?>) <?= $rec[2] ?> .. <?= $rec[3] ?> .. <?= $rec[4] ?> .. <?= $rec[5] ?> */ ?>
				</li><?php
			}
			?></ol><?php
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
	</section>
  </body>
</html>