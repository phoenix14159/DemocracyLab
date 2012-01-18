<?php
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
        <h1>Add <?= $typestring ?>, <strong><?php echo idx($basic, 'name'); ?></strong></h1>
      </div>
   </header>

    <section class="clearfix">
	<a href="entities.php?type=<?= $type ?>&state=<?= $_REQUEST['state'] ?>&code=<?= $_REQUEST['code'] ?>">back to <?= $typestrings ?></a>
	<form method="POST" action="addentity_post.php">
		<input type="hidden" name="type" value="<?= $type ?>">
		<input type="hidden" name="state" value="<?= $_REQUEST['state'] ?>">
		<input type="hidden" name="code" value="<?= $_REQUEST['code'] ?>">
		Name: <input name="name"><br>
		Description: <input name="description"><br>
		<input type="submit" value="Add <?= $typestring ?>">
	</form>
    </section>
    <section id="guides" class="clearfix">
	</section>
  </body>
</html>