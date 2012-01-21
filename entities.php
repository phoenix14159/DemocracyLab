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

    <!-- We get the name of the app out of the information fetched -->
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
      <p id="picture" style="background-image: url(https://graph.facebook.com/me/picture?type=normal&access_token=<?php echoEntity($token) ?>)"></p>

      <div>
        <h1><?= dl_typestring($type,'ucp') ?>, <strong><?php echo idx($basic, 'name'); ?></strong></h1>
      </div>
   </header>

    <section class="clearfix">
	<a href="<?= dl_facebook_url('index.php') ?>">back to Overview</a>

	<form method="POST" action="saveorder_post.php">
	<input type="hidden" name="type" value="<?= $type ?>">
	<input type="hidden" name="user" value="<?= $democracylab_user_id ?>">
	<?= dl_facebook_form_fields() ?>
	<ol>
		<?php
		foreach($entities as $erec) {
			?><li><input size="3" type="text" name="id<?= $erec['id'] ?>" value="<?= $erec['rank'] ? $erec['rank'] : 0 ?>"> <?= $erec['title'] ?></li>
			<?php
		}
		?>
	</ol>
	<input type="submit" value="Change Rankings">
	</form>

	<a href="<?= dl_facebook_url('addentity.php',$type) ?>">add <?= dl_typestring($type,'ucs') ?></a>
    </section>
    <section id="guides" class="clearfix">
	</section>
  </body>
</html>