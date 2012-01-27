<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/prelib.inc');

$type = pg_escape_string($_POST['type']);
$user_id = pg_escape_string($_POST['user']);

if($_POST['list'] == 'positive') {
	$result = pg_query("DELETE FROM democracylab_rankings WHERE type = '$type' AND user_id = $user_id AND ranking > 0");
} else {
	$result = pg_query("DELETE FROM democracylab_rankings WHERE type = '$type' AND user_id = $user_id AND ranking < 0");	
}
$sql = '';
foreach($_POST as $key => $value) {
	if(preg_match("/^entity-(\d+)$/",$key,$matches)) {
		$sql = $sql . ",($user_id,'$type','" . pg_escape_string($matches[1]) . "','" . pg_escape_string($value) . "')";
	}
}
$result = pg_query("INSERT INTO democracylab_rankings (user_id,type,entity_id,ranking) VALUES " . substr($sql,1));

?>