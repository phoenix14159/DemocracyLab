<?php
/*
 * Input variables:
 * DL_BASESCRIPT = absolute directory root
 *
 * Output Variables:
 * $dbconn = Postgresql database connection [from preinc]
 *
 * $democracylab_user_id = DemocracyLab user id
 * $democracylab_user_role = DemocracyLab user role (0=normal, 1=superuser)
 * 
 */
require_once(DL_BASESCRIPT . '/lib/prelib.php');
require_once(DL_BASESCRIPT . '/utils.php');

session_start();
if(isset($_SESSION['democracylab_user_id'])) {
	$democracylab_user_id = $_SESSION['democracylab_user_id'];
	$democracylab_user_role = $_SESSION['democracylab_user_role'];
} else {
	header('Location: index.php');
	exit();
}

$democracylab_community_id = isset($_REQUEST['community']) ? intval($_REQUEST['community']) : 1;
$democracylab_issue_id = isset($_REQUEST['issue']) ? intval($_REQUEST['issue']) : 1;

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

