<?php
$postdata = http_build_query(
    array(
        'user' => $democracylab_user_id
    )
);
$opts_post['http']['content'] = $postdata;
$context_post = stream_context_create($opts_post);
$data = file_get_contents( "${baseurl}/get_summary", false, $context_post );
$jdata = json_decode($data,true);

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
		if($jdata['values']) {
			?><a href="entities.php?type=1&state=<?= $_REQUEST['state'] ?>&code=<?= $_REQUEST['code'] ?>">Explore Values</a><ol><?php
			foreach($jdata['values'] as $rec) {
				?><li><?= $rec[1] ?> = (<?= $rec[6] ?>) <?= $rec[2] ?> .. <?= $rec[3] ?> .. <?= $rec[4] ?> .. <?= $rec[5] ?></li><?php
			}
			?></ol><?php
		} else {
			?><a href="entities.php?type=1&state=<?= $_REQUEST['state'] ?>&code=<?= $_REQUEST['code'] ?>">Please classify some Values</a><?php
		}
		?>
		</li>
		<li><?php
		if($jdata['objectives']) {
			?><a href="entities.php?type=2&state=<?= $_REQUEST['state'] ?>&code=<?= $_REQUEST['code'] ?>">Explore Objectives</a><ol><?php
			foreach($jdata['objectives'] as $rec) {
				?><li><?= $rec[1] ?> = (<?= $rec[6] ?>) <?= $rec[2] ?> .. <?= $rec[3] ?> .. <?= $rec[4] ?> .. <?= $rec[5] ?></li><?php
			}
			?></ol><?php
		} else {
			?><a href="entities.php?type=2&state=<?= $_REQUEST['state'] ?>&code=<?= $_REQUEST['code'] ?>">Please classify some Objectives</a><?php
		}
		?>
		</li>
		<li><?php
		if($jdata['policies']) {
			?><a href="entities.php?type=3&state=<?= $_REQUEST['state'] ?>&code=<?= $_REQUEST['code'] ?>">Explore Policies</a><ol><?php
			foreach($jdata['policies'] as $rec) {
				?><li><?= $rec[1] ?> = (<?= $rec[6] ?>) <?= $rec[2] ?> .. <?= $rec[3] ?> .. <?= $rec[4] ?> .. <?= $rec[5] ?></li><?php
			}
			?></ol><?php
		} else {
			?><a href="entities.php?type=3&state=<?= $_REQUEST['state'] ?>&code=<?= $_REQUEST['code'] ?>">Please classify some Policies</a><?php
		}
		?>
		</li>
	</ul>
	<a href="temp.php?state=<?= $_REQUEST['state'] ?>&code=<?= $_REQUEST['code'] ?>">temp</a>
    </section>
    <section id="guides" class="clearfix">
	</section>
  </body>
</html>