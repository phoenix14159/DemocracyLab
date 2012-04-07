<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.php');

$type = pg_escape_string($_POST['type']);

function ranking_conv($i) {
	if($i > 0) {
		switch($i) {
			case 1: return 10;
			case 2: return 8;
			case 3: return 6;
			case 4: return 4;
			case 5: return 3;
			case 6: return 2;
			default: return 1;
		}
	} else {
		switch($i) {
			case -1: return -5;
			case -2: return -4;
			case -3: return -3;
			case -4: return -2;
			default: return -1;
		}
	}
}

$data = array();
foreach($_POST as $key => $value) {
	if(preg_match("/^entity-(\d+)$/",$key,$matches)) {
		$data[$value] = $matches[1];
	}
}

ksort($data);
if($_POST['list'] == 'positive') {
	$idx = 1;
	$inc = 1;
} else {
	$idx = -1;
	$inc = -1;
}
$sql = '';
foreach($data as $key) {
	$sql = $sql . ",($democracylab_user_id,$type,$key,$idx," . ranking_conv($idx) . ",$democracylab_community_id,$democracylab_issue_id)";
	$idx += $inc;
}

if($_POST['list'] == 'positive') {
	$result = pg_query("DELETE FROM democracylab_rankings 
						 WHERE type = '$type' 
						   AND community_id = {$democracylab_community_id}
						   AND issue_id = {$democracylab_issue_id}
						   AND user_id = $democracylab_user_id 
						   AND ranking > 0");
} else {
	$result = pg_query("DELETE FROM democracylab_rankings 
						 WHERE type = '$type' 
						   AND community_id = {$democracylab_community_id}
						   AND issue_id = {$democracylab_issue_id}
						   AND user_id = $democracylab_user_id 
						   AND ranking < 0");	
}
if($sql) {
	$result = pg_query("INSERT INTO democracylab_rankings (user_id,type,entity_id,ranking,rating,community_id,issue_id) 
						VALUES " . substr($sql,1));
}
?>