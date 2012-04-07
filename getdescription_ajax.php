<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/lib.php');

$entityid = intval($_REQUEST['entityid']);

$result = pg_query("SELECT title, description FROM democracylab_entities WHERE entity_id = $entityid");
$row = pg_fetch_object($result);
if($row->description) {
	?><b><?= $row->title ?>:</b> <?= $row->description ?><?php
} else {
	?><b><?= $row->title ?>:</b> <span class="instructions">(no description)</span><?php	
}
?>