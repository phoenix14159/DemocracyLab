<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.php');

$type = pg_escape_string($_REQUEST['type']);
$name = pg_escape_string($_REQUEST['name']);
$description = pg_escape_string($_REQUEST['description']);
$entityid = intval($_REQUEST['entityid']);

if($entityid) {
	$result = pg_query($dbconn, "SELECT * FROM democracylab_entities WHERE entity_id = $entityid");
	$row = pg_fetch_object($result);
	if($democracylab_user_role > 0 || $row->user_id == 0 || $row->user_id == $democracylab_user_id) {
		pg_query($dbconn, "DELETE FROM democracylab_entities WHERE entity_id = $entityid");
		pg_query($dbconn, "DELETE FROM democracylab_rankings WHERE entity_id = $entityid");
	}
}

header("Location: " . dl_facebook_redirect_url('entities.php',$_REQUEST['type']) );
?>