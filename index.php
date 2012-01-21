<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.inc');

// Get the rankings data
$result = pg_query($dbconn, "SELECT type, COUNT(1) FROM democracylab_rankings WHERE user_id = $democracylab_user_id GROUP BY type");
$rankings = array();
$rankings['values'] = 0;
$rankings['objectives'] = 0;
$rankings['policies'] = 0;
while($row = pg_fetch_array($result)) {
	if($row[1] != 0) {
		$idx = '?';
		if($row[0] == 1) $idx = 'values';
		if($row[0] == 2) $idx = 'objectives';
		if($row[0] == 3) $idx = 'policies';
		$result2 = pg_query($dbconn, "SELECT entity_id, MIN(ranking), AVG(ranking), VARIANCE(ranking), MAX(ranking)
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
		$result4 = pg_query($dbconn, "SELECT entity_id, ranking FROM democracylab_rankings WHERE entity_id IN ($ids) AND user_id = $democracylab_user_id");
		while($row4 = pg_fetch_array($result4)) {
			$a2[$row4[0]][6] = $row4[1];
		}
		$rankings[$idx] = $a2;
	}
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">

    <!-- We get the name of the app out of the information fetched -->
    <title><?php echo(idx($app_info, 'name')) ?></title>
    <link rel="stylesheet" href="stylesheets/screen.css" media="screen">

    <?php echo('<meta property="fb:app_id" content="' . AppInfo::appID() . '" />'); ?>
    <script>
      function popup(pageURL, title,w,h) {
        var left = (screen.width/2)-(w/2);
        var top = (screen.height/2)-(h/2);
        var targetWin = window.open(
          pageURL,
          title,
          'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left
          );
      }
    </script>
    <!--[if IE]>
      <script>
        var tags = ['header', 'section'];
        while(tags.length)
          document.createElement(tags.pop());
      </script>
    <![endif]-->
  </head>
  <body>
    <header class="clearfix">
      <!-- By passing a valid access token here, we are able to display -->
      <!-- the user's images without having to download or prepare -->
      <!-- them ahead of time -->
      <p id="picture" style="background-image: url(https://graph.facebook.com/me/picture?type=normal&access_token=<?php echoEntity($token) ?>)"></p>

      <div>
        <h1>Welcome, <strong><?php echo idx($basic, 'name'); ?></strong></h1>
      </div>
   </header>

    <section class="clearfix">
	<ul>
		<li><?php
		if($rankings['values']) {
			?><a href="<?= dl_facebook_url('entities.php',1) ?>">Explore Values</a><ol><?php
			foreach($rankings['values'] as $rec) {
				?><li><?= $rec[1] ?> = (<?= $rec[6] ?>) <?= $rec[2] ?> .. <?= $rec[3] ?> .. <?= $rec[4] ?> .. <?= $rec[5] ?></li><?php
			}
			?></ol><?php
		} else {
			?><a href="<?= dl_facebook_url('entities.php',1) ?>">Please classify some Values</a><?php
		}
		?>
		</li>
		<li><?php
		if($rankings['objectives']) {
			?><a href="<?= dl_facebook_url('entities.php',2) ?>">Explore Objectives</a><ol><?php
			foreach($rankings['objectives'] as $rec) {
				?><li><?= $rec[1] ?> = (<?= $rec[6] ?>) <?= $rec[2] ?> .. <?= $rec[3] ?> .. <?= $rec[4] ?> .. <?= $rec[5] ?></li><?php
			}
			?></ol><?php
		} else {
			?><a href="<?= dl_facebook_url('entities.php',2) ?>">Please classify some Objectives</a><?php
		}
		?>
		</li>
		<li><?php
		if($rankings['policies']) {
			?><a href="<?= dl_facebook_url('entities.php',3) ?>">Explore Policies</a><ol><?php
			foreach($rankings['policies'] as $rec) {
				?><li><?= $rec[1] ?> = (<?= $rec[6] ?>) <?= $rec[2] ?> .. <?= $rec[3] ?> .. <?= $rec[4] ?> .. <?= $rec[5] ?></li><?php
			}
			?></ol><?php
		} else {
			?><a href="<?= dl_facebook_url('entities.php',3) ?>">Please classify some Policies</a><?php
		}
		?>
		</li>
	</ul>
    </section>
    <section id="guides" class="clearfix">
	</section>
  </body>
</html>