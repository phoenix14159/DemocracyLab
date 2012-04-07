<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.php');

if($democracylab_user_role == 0) {
	exit;
}

$userid = pg_escape_string($_POST['userid']);
$role = $_POST['admin'] == 'admin' ? 1 : 0;

pg_query("UPDATE democracylab_users SET role = $role WHERE user_id = $userid");
?>