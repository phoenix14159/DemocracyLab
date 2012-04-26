<?php
$start_time = microtime(true);//TIMING
error_log(__FILE__ . " start");//TIMING
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.php');
$now_time = microtime(true);//TIMING
error_log(__FILE__ . " loaded lib.php in " . ($now_time - $start_time));//TIMING

$entityid = intval($_REQUEST['entityid']);

$result = pg_query("SELECT title, description FROM democracylab_entities WHERE entity_id = $entityid");
$row = pg_fetch_object($result);
$now2_time = microtime(true);//TIMING
error_log(__FILE__ . " query in " . ($now2_time - $now_time));//TIMING
$now_time = $now2_time; //TIMING
if($row->description) {
	?><b><?= $row->title ?>:</b> <?= $row->description ?><?php
} else {
	?><b><?= $row->title ?>:</b> <span class="instructions">(no description)</span><?php	
}
$now2_time = microtime(true);//TIMING
error_log(__FILE__ . " total time " . ($now2_time - $start_time));//TIMING
?>