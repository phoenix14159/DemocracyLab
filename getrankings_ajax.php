<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.php');

$filter_id = pg_escape_string($_REQUEST['id']);
$filter_rating = pg_escape_string($_REQUEST['rating']);

$data = array();
if($filter_id) {
	$result = pg_query("SELECT * FROM democracylab_rankings 
					WHERE community_id = {$democracylab_community_id}
				   	  AND issue_id = {$democracylab_issue_id}
				      AND user_id IN (SELECT user_id FROM democracylab_rankings 
										WHERE community_id = {$democracylab_community_id}
				   	  					  AND issue_id = {$democracylab_issue_id}
				      					  AND entity_id = ${filter_id}
				      					  AND rating = ${filter_rating})");
} else {
	$result = pg_query("SELECT * FROM democracylab_rankings 
					WHERE community_id = {$democracylab_community_id}
				   	  AND issue_id = {$democracylab_issue_id}");
}
while($row = pg_fetch_object($result)) {
	if(!isset($data[$row->entity_id])) {
		$data[$row->entity_id] = array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 );
		//							   -5 -4 -3 -2 -1  0  1  2  3  4  5  6  7  8  9 10
		//                              0  1  2  3  4  5  6  7  8  9 10 11 12 13 14 15
		$rating = $row->rating + 5;
		if($rating < 0) { $ranking = 0; }
		if($rating > 15 ) { $ranking = 15; }
		$data[$row->entity_id][$rating] += 1;
	}
}

echo json_encode($data);
?>