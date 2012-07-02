<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')) . '/..');
require_once(DL_BASESCRIPT . '/lib/prelib.php');

$result = @pg_query("SELECT COUNT(1) FROM migrations");
if(!$result) {
	pg_query($dbconn, "CREATE TABLE migrations ( 
			name VARCHAR(512) PRIMARY KEY NOT NULL, 
			ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP )" );
}
function do_migration( $name ) {
	global $dbconn;
	$result = pg_query($dbconn, "SELECT * FROM migrations WHERE name = '$name'" );
	$row = pg_fetch_object($result);
	if( $row ) return 0; // migration already done
	echo "Migration $name ... ";
	return 1; // migration not done, so do it
}
function record_migration( $name ) {
	global $dbconn;
	pg_query($dbconn, "INSERT INTO migrations ( name ) VALUES ( '$name' )");
	echo "successful\n";
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function add_democracylab_tables() {
	global $dbconn;
	if( !do_migration(__FUNCTION__) ) return;
	pg_query($dbconn,  "CREATE TABLE democracylab_users (
		user_id SERIAL PRIMARY KEY,
		fb_id BIGINT NOT NULL,
		name TEXT)" );
	pg_query($dbconn, "CREATE TABLE democracylab_entities (
		entity_id SERIAL PRIMARY KEY,
		type INT,
		title TEXT NOT NULL,
		description TEXT NOT NULL)" );
	pg_query($dbconn, "CREATE TABLE democracylab_rankings (
		ranking_id SERIAL PRIMARY KEY,
		user_id INT,
		entity_id INT,
		ranking INT,
		type INT)" );
	record_migration(__FUNCTION__);
}
add_democracylab_tables();
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
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
			case 1: return -5;
			case 2: return -4;
			case 3: return -3;
			case 4: return -2;
			default: return -1;
		}
	}
}
function add_rating_column() {
	global $dbconn;
	if( !do_migration(__FUNCTION__) ) return;
	pg_query($dbconn, "ALTER TABLE democracylab_rankings ADD COLUMN rating INT" );
	$result1 = pg_query($dbconn, "SELECT DISTINCT user_id, type FROM democracylab_rankings");
	while($row1 = pg_fetch_object($result1)) {
		$positives = array();
		$negatives = array();
		$result2 = pg_query($dbconn, "SELECT * FROM democracylab_rankings WHERE user_id = {$row1->user_id} AND type = {$row1->type}");
		while($row2 = pg_fetch_object($result2)) {
			if($row2->ranking < 0) {
				$negatives[$row2->ranking] = $row2;
			}
			if($row2->ranking > 0) {
				$positives[$row2->ranking] = $row2;
			}
		}
		
		ksort($positives);
		$idx = 1;
		foreach($positives as $key => $row) {
			$row->rating = ranking_conv($idx);
			$row->ranking = $idx;
			$idx += 1;
		}
		
		ksort($negatives);
		$negatives = array_reverse($negatives);
		$idx = -1;
		foreach($negatives as $key => $row) {
			$row->rating = ranking_conv($idx);
			$row->ranking = $idx;
			$idx -= 1;
		}
		
		$sql = "INSERT INTO democracylab_rankings (user_id,entity_id,type,ranking,rating) VALUES";
		$comma = " ";
		foreach($positives as $row) {
			$sql .= $comma . "({$row->user_id},{$row->entity_id},{$row->type},{$row->ranking},{$row->rating})";
			$comma = ',';
		}
		foreach($negatives as $row) {
			$sql .= $comma . "({$row->user_id},{$row->entity_id},{$row->type},{$row->ranking},{$row->rating})";
			$comma = ',';
		}
		pg_query("DELETE FROM democracylab_rankings WHERE user_id = {$row1->user_id} AND type = {$row1->type}");
		pg_query($sql);
	}
	record_migration(__FUNCTION__);
}
add_rating_column();
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function add_user_permissions() {
	global $dbconn;
	if( !do_migration(__FUNCTION__) ) return;
	pg_query($dbconn, "ALTER TABLE democracylab_users ADD COLUMN role INT NOT NULL DEFAULT 0" );
	pg_query($dbconn, "ALTER TABLE democracylab_entities ADD COLUMN user_id INT NOT NULL DEFAULT 0" );
	pg_query($dbconn, "UPDATE democracylab_users SET role = 1");
	record_migration(__FUNCTION__);
}
add_user_permissions();
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function add_communities_and_issues() {
	global $dbconn;
	if( !do_migration(__FUNCTION__) ) return;
	pg_query($dbconn, "CREATE TABLE democracylab_communities (
		community_id SERIAL PRIMARY KEY,
		title TEXT NOT NULL,
		description TEXT NOT NULL)" );
	pg_query($dbconn, "CREATE TABLE democracylab_issues (
		issue_id SERIAL PRIMARY KEY,
		community_id INT NOT NULL,
		title TEXT NOT NULL,
		description TEXT NOT NULL)" );
	pg_query($dbconn, "ALTER TABLE democracylab_entities ADD COLUMN community_id INT NOT NULL DEFAULT 0" );
	pg_query($dbconn, "ALTER TABLE democracylab_entities ADD COLUMN issue_id INT NOT NULL DEFAULT 0" );
	pg_query($dbconn, "INSERT INTO democracylab_communities (title,description) VALUES ('DemocracyLab Demo','')");
	pg_query($dbconn, "INSERT INTO democracylab_communities (title,description) VALUES ('University of Portland CST 491','')");
	pg_query($dbconn, "INSERT INTO democracylab_issues (community_id,title,description) VALUES (1,'Oregon''s Budget','')");
	pg_query($dbconn, "INSERT INTO democracylab_issues (community_id,title,description) VALUES (2,'Capital Improvement Fund','')");
	pg_query($dbconn, "UPDATE democracylab_entities SET community_id = 2, issue_id = 2");
	record_migration(__FUNCTION__);
}
add_communities_and_issues();
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function add_communities_and_issues2() {
	global $dbconn;
	if( !do_migration(__FUNCTION__) ) return;
	pg_query($dbconn, "ALTER TABLE democracylab_rankings ADD COLUMN community_id INT NOT NULL DEFAULT 0" );
	pg_query($dbconn, "ALTER TABLE democracylab_rankings ADD COLUMN issue_id INT NOT NULL DEFAULT 0" );
	pg_query($dbconn, "UPDATE democracylab_rankings SET community_id = 2, issue_id = 2");
	record_migration(__FUNCTION__);
}
add_communities_and_issues2();
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function add_additional_indicies_1() {
	global $dbconn;
	if( !do_migration(__FUNCTION__) ) return;
	pg_query($dbconn, "CREATE INDEX democracylab_entities_itci ON democracylab_entities (entity_id,type,community_id,issue_id)" );
	pg_query($dbconn, "CREATE INDEX democracylab_entities_tci  ON democracylab_entities (type,community_id,issue_id)" );
	pg_query($dbconn, "CREATE INDEX democracylab_rankings_u ON democracylab_rankings (user_id)" );
	pg_query($dbconn, "CREATE INDEX democracylab_rankings_tciu ON democracylab_rankings (type,community_id,issue_id,user_id)" );
	record_migration(__FUNCTION__);
}
add_additional_indicies_1();
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function add_twitter_login() {
	global $dbconn;
	if( !do_migration(__FUNCTION__) ) return;
	pg_query($dbconn, "ALTER TABLE democracylab_users ALTER COLUMN fb_id SET DEFAULT 0" );
	pg_query($dbconn, "ALTER TABLE democracylab_users ADD COLUMN twitter_id BIGINT NOT NULL DEFAULT 0" );
	record_migration(__FUNCTION__);
}
add_twitter_login();
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function add_linkedin_login() {
	global $dbconn;
	if( !do_migration(__FUNCTION__) ) return;
	pg_query($dbconn, "ALTER TABLE democracylab_users ADD COLUMN linkedin_id BIGINT NOT NULL DEFAULT 0" );
	record_migration(__FUNCTION__);
}
add_linkedin_login();
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function add_linkedin_login2() {
	global $dbconn;
	if( !do_migration(__FUNCTION__) ) return;
	pg_query($dbconn, "ALTER TABLE democracylab_users DROP COLUMN linkedin_id" );
	pg_query($dbconn, "ALTER TABLE democracylab_users ADD COLUMN linkedin_id TEXT NOT NULL" );
	record_migration(__FUNCTION__);
}
add_linkedin_login2();
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

pg_close( $dbconn );

echo "Migrations completed successfully\n";

?>
