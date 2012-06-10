<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.php');

$filter_id = pg_escape_string($_REQUEST['id']);
$filter_rating = pg_escape_string($_REQUEST['rating']);

$data = array();
if($filter_id) {
	if($filter_rating == 7) { $filter_rating = 10;}
	if($filter_rating == 6) { $filter_rating = 8;}
	if($filter_rating == 5) { $filter_rating = 6;}
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
		$data[$row->entity_id] = array( -1, array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,    0,    0,   0 ));
		//							   -5 -4 -3 -2 -1  0  1  2  3  4  5  6  7  8  9 10
		//                              0  1  2  3  4  5  6  7  8  9    10    11    12
	}
	$rating = $row->rating;
	if($rating == 6) { $rating = 5;}
	if($rating == 8) { $rating = 6;}
	if($rating == 10) { $rating = 7;}
	$rating = $rating + 5;
	if($rating < 0) { $ranking = 0; }
	if($rating > 12 ) { $ranking = 12; }
	$data[$row->entity_id][1][$rating] += 1;
}

$result = pg_query("SELECT * FROM democracylab_rankings 
				WHERE user_id = {$democracylab_user_id}
				  AND community_id = {$democracylab_community_id}
			   	  AND issue_id = {$democracylab_issue_id}");
while($row = pg_fetch_object($result)) {
	if(!isset($data[$row->entity_id])) {
		$data[$row->entity_id] = array( -1 );
	}
	$rating = $row->rating;
	if($rating == 6) { $rating = 5;}
	if($rating == 8) { $rating = 6;}
	if($rating == 10) { $rating = 7;}
	$rating = $rating + 5;
	if($rating < 0) { $ranking = 0; }
	if($rating > 12 ) { $ranking = 12; }
	$data[$row->entity_id][0] = $rating;
}

echo json_encode($data);
?>