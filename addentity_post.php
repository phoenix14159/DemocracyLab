<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/prelib.inc');

$type = pg_escape_string($_REQUEST['type']);
$name = pg_escape_string($_REQUEST['name']);
$description = pg_escape_string($_REQUEST['description']);

pg_query($dbconn, "INSERT INTO democracylab_entities (type,title,description) VALUES ('$type','$name','$description')");

header("Location: " . dl_facebook_redirect_url('entities.php',$_REQUEST['type']) );
?>