<?php
/*
 * Input variables:
 * DL_BASESCRIPT = absolute directory root
 *
 * Output Variables:
 * $dbconn = Postgresql database connection [from preinc]
 *
 * $basic = some kind of Facebook data structure
 * $my_id = Facebook user id
 * $app_id = Facebook application id
 * $app_info = Facebook application info
 * $democracylab_user_id = DemocracyLab user id
 * $democracylab_user_role = DemocracyLab user role (0=normal, 1=superuser)
 * 
 */
require_once(DL_BASESCRIPT . '/lib/prelib.php');

require_once(DL_BASESCRIPT . '/FBUtils.php');
require_once(DL_BASESCRIPT . '/AppInfo.php');
require_once(DL_BASESCRIPT . '/utils.php');

$token = FBUtils::login(AppInfo::getHome());
if ($token) {

	// Fetch the viewer's basic information, using the token just provided
	$basic = FBUtils::fetchFromFBGraph("me?access_token=$token");
	$my_id = assertNumeric(idx($basic, 'id'));

	// Fetch the basic info of the app that they are using
	$app_id = AppInfo::appID();
	$app_info = FBUtils::fetchFromFBGraph("$app_id?access_token=$token");
	
	$result = pg_query($dbconn, "SELECT * FROM democracylab_users WHERE fb_id = $my_id");
	$row = pg_fetch_object($result);
	if($row) {
		$democracylab_user_id = $row->user_id;
		$democracylab_user_role = $row->role;
	} else {
		$rname = pg_escape_string(idx($basic,'name'));
		$result = pg_query($dbconn, "INSERT INTO democracylab_users (fb_id,name) VALUES ($my_id,'$rname')");
		$result = pg_query($dbconn, "SELECT LASTVAL()");
		$row = pg_fetch_array($result);
		$democracylab_user_id = $row[0];
		$democracylab_user_role = 0;
	}

} else {
	// Stop running if we did not get a valid response from logging in
	exit("Invalid credentials");
}

function democracylab_hover_javascript() {
	?>
<script>
$(function () {
	$(".hover-describe").each( function (index,elem) {
		$(elem).mouseenter( function() {
			var newid = $(elem).attr('dl_id');
			var oldid = $("#description-block").attr('dl_id');
			if(newid != oldid) {
				$("#description-block").attr('dl_id',newid);
				var newdata = $("#description-block").data('dl_' + newid);
				if(newdata) {
					$("#description-block").html(newdata);
				} else {
					$("#description-block").html('<span class="instructions">(fetching description...)</span>');
					var data = {};
					data['entityid'] = newid;
					$.ajax({
						url: '<?= dl_facebook_url("getdescription_ajax.php") ?>',
						context: document.body,
						data: data,
						type: "GET",
						dataType: 'html',
						success: function (data) {
							var rtrnid = $("#description-block").attr('dl_id');
							$("#description-block").data('dl_' + newid,data);
							if(newid == rtrnid) {
								$("#description-block").html(data).data('dl_' + newid,data);
							}
						},
						global: false
					})
				}
			}
		});
	});
});
</script>
<?php
}
?>

