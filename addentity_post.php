<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.php');

$type = pg_escape_string($_REQUEST['type']);
$name = pg_escape_string($_REQUEST['name']);
$description = pg_escape_string($_REQUEST['description']);

pg_query($dbconn, "INSERT INTO democracylab_entities (type,title,description,user_id) VALUES ('$type','$name','$description',$democracylab_user_id)");

header("Location: " . dl_facebook_redirect_url('entities.php',$_REQUEST['type']) );
?>