<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')) . '/..');
require_once(DL_BASESCRIPT . '/lib/prelib.inc');

pg_query($dbconn, "CREATE TABLE migrations ( 
		name VARCHAR(512) PRIMARY KEY NOT NULL, 
		ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP )" );
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

pg_close( $dbconn );

echo "Migrations completed successfully\n";

?>
