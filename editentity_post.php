<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.inc');

if($democracylab_user_role == 0) {
	header('Location: ' . dl_facebook_url('index.php') );
	exit;
}

$type = pg_escape_string($_REQUEST['type']);
$name = pg_escape_string($_REQUEST['name']);
$description = pg_escape_string($_REQUEST['description']);
$entityid = intval($_REQUEST['entityid']);

if($entityid) {
	pg_query($dbconn, "UPDATE democracylab_entities SET title='$name', description='$description' WHERE entity_id = $entityid");
}

header("Location: " . dl_facebook_redirect_url('entities.php',$_REQUEST['type']) );
?>