<div id="footer" class="clearfix">
<?php if(isset($footer_extra_text) && $footer_extra_text) { 
	echo $footer_extra_text;
}
if(isset($footer_include_description) && $footer_include_description) { ?>
	<p>DemocracyLab is a 501(c)(3) nonprofit organization aspiring to revolutionize the nature of political
		dialogue. We believe privacy is important, especially when talking about politics. We treat your
		personal information with the care and respect you deserve, and will never share any details about you
		or your political views without your permission.
		<!-- Please reference our privacy policy and terms of use for more information --></p>
<?php } 
if(!(isset($footer_nologout) && $footer_nologout)) {
	?><div style="text-align: right; margin-bottom: 10px;"><a href="logout.php">logout</a></div><?php
}?>
<?php if(isset($footer_include_admin) && $footer_include_admin) {
	if($democracylab_user_role > 0) {
		?><p style="text-align: center; color: black; border-top: thin dotted red; margin-top: 1em;">admin: <a href="<?= dl_facebook_url('users.php') ?>">users</a>,
		<a href="<?= dl_facebook_url('communities.php') ?>">communities</a><?php
	}
	?>
	</div>
<?php } ?>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-2879129-3']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
