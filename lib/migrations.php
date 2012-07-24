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
	record_migration(__FUNCTION__);
}
add_linkedin_login2();
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function add_linkedin_login3() {
	global $dbconn;
	if( !do_migration(__FUNCTION__) ) return;
	pg_query($dbconn, "ALTER TABLE democracylab_users ADD COLUMN linkedin_id TEXT NOT NULL DEFAULT ''" );
	record_migration(__FUNCTION__);
}
add_linkedin_login3();
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function add_issue_columns_1() {
	global $dbconn;
	if( !do_migration(__FUNCTION__) ) return;
	pg_query($dbconn, "ALTER TABLE democracylab_issues ADD COLUMN how_it_works TEXT NOT NULL DEFAULT ''" );
	pg_query($dbconn, "ALTER TABLE democracylab_issues ADD COLUMN extra_footer_text TEXT NOT NULL DEFAULT ''" );
	pg_query($dbconn, "UPDATE democracylab_issues SET how_it_works = '<p>A recent <a href=\"http://www.leg.state.or.us/comm/lro/2012_publications_reports/Basic_Facts_2012.pdf\" target=\"_new\">research report</a> by Oregon''s Legislative Revenue Office included the following table and comments* comparing Oregon''s tax system to other states across the country:<style>table { border: thin solid #CCC;}th { font-weight: bold; text-align: left; padding-left: 10px; padding-right: 10px; border: thin solid #CCC;}td { padding-left: 10px; padding-right: 10px; border: thin solid #CCC;}</style><center><table><tr><th>REVENUE CATEGORIES</th><th>\$ PER PERSON</th><th>RANK AMONG THE STATES</th></tr><tr><th>TOTAL TAXES</th><td>\$3,275</td><td>39th</td></tr><tr><th>PERSONAL INCOME TAX</th><td>\$1,356</td><td>5th</td></tr><tr><th>CORPORATE INCOME TAX</th><td>\$75</th><td>38th</td></tr><tr><th>PROPERTY TAX</th><td>\$1,166</th><td>28th</td></tr><tr><th>GENERAL SALES TAX</th><td>0</th><td>50th</td></tr><tr><th>SELECTIVE SALES TAXES</th><td>\$319</th><td>44th</td></tr><tr><th>OTHER TAXES</th><td>\$359</th><td>12th</td></tr></table></center></p>' WHERE issue_id = 1" );
	pg_query($dbconn, "UPDATE democracylab_issues SET extra_footer_text = '<p style=\"color: #444;\">* Oregon''s overall state and local tax burden ranks 39th on a per person basis. However, the state	personal income tax burden is among the highest in the nation at \$1,356 per person. The ranking for	corporate income taxes is relatively low at #38, but this is prior to the imposition of higher corporate tax	rates and a new corporate minimum called for in Measure 67. Property taxes are near the middle of	the states, ranking # 28. The state tax burden on consumption (general sales plus selective sales) is	the lowest in the country. In addition to being one of five states without a general sales tax, Oregon	ranks 44th in selective sales tax collections per person. Selective sales taxes include gasoline taxes,	tobacco taxes, alcoholic beverage taxes, real estate transfer taxes and other excise taxes on specific	purchases. It also includes health provider taxes which have risen in Oregon and other states in recent	years. The other tax category includes severance taxes and estate taxes.<br><br>	</p>' WHERE issue_id = 1" );
	pg_query($dbconn, "UPDATE democracylab_issues SET how_it_works = '<p>The aim of this tool is to identify issues, stimulate ideas, and create a dialogue around the UP Capital Improvement Fund (CIF), also known as the Major Project Fund. Please use this application to express and group the values, objectives, and policies related to the CIF. Thank you for input!</p>' WHERE issue_id = 2" );
	record_migration(__FUNCTION__);
}
add_issue_columns_1();
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

pg_close( $dbconn );

echo "Migrations completed successfully\n";

?>
