<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));

require_once(DL_BASESCRIPT . '/lib/prelib.php');
require_once(DL_BASESCRIPT . '/utils.php');
require_once(DL_BASESCRIPT . '/AppInfo.php');
require_once(DL_BASESCRIPT . '/FBUtils.php');

$uid = pg_escape_string($_REQUEST['uid']);
$token = $_REQUEST['token'];

$result = pg_query($dbconn, "SELECT * FROM democracylab_users WHERE fb_id = $uid");
$row = pg_fetch_object($result);
if($row) {
	$democracylab_user_id = $row->user_id;
	$democracylab_user_role = $row->role;
} else {
	$basic = FBUtils::fetchFromFBGraph("me?access_token=$token");
	$rname = pg_escape_string(idx($basic,'name'));
	$result = pg_query($dbconn, "INSERT INTO democracylab_users (fb_id,name) VALUES ($uid,'$rname')");
	$result = pg_query($dbconn, "SELECT LASTVAL()");
	$row = pg_fetch_array($result);
	$democracylab_user_id = $row[0];
	$democracylab_user_role = 0;
}
session_start();
$_SESSION['democracylab_user_id'] = $democracylab_user_id;
$_SESSION['democracylab_user_role'] = $democracylab_user_role;

header('Location: summary.php');
?>
