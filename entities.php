<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.inc');

$type = $_REQUEST['type'];
$typestrings = '';
if($type == 1) {
	$typestrings = 'Values';
	$typestring = 'Value';
}
if($type == 2) {
	$typestrings = 'Objectives';
	$typestring = 'Objective';
}
if($type == 3) {
	$typestrings = 'Policies';
	$typestring = 'Policy';
}
$postdata = http_build_query(
    array(
        'type' => $type,
		'user' => $democracylab_user_id
    )
);
$opts_post['http']['content'] = $postdata;
$context_post = stream_context_create($opts_post);
$data = file_get_contents( "${baseurl}/entities_and_order", false, $context_post );
$jdata = json_decode($data,true);
/*
if(isset($jdata['error'])) {
	echo "Error calling api/insert\n";
	exit;
}
if(!isset($jdata['ok'])) {
	echo "Remote site is down when calling api/insert\n";
	exit;
}
*/

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
        <h1><?= $typestrings ?>, <strong><?php echo idx($basic, 'name'); ?></strong></h1>
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
		foreach($jdata['entities'] as $erec) {
			?><li><input size="3" type="text" name="id<?= $erec['id'] ?>" value="<?= $erec['rank'] ? $erec['rank'] : 0 ?>"> <?= $erec['title'] ?></li>
			<?php
		}
		?>
	</ol>
	<input type="submit" value="Change Rankings">
	</form>

	<a href="<?= dl_facebook_url('addentity.php',$type) ?>">add <?= $typestring ?></a>
    </section>
    <section id="guides" class="clearfix">
	</section>
  </body>
</html>