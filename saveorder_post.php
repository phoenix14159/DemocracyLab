<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/prelib.inc');

$postdata_data = 
    array(
		'type' => $_REQUEST['type'],
		'user' => $_REQUEST['user']
    );
foreach($_REQUEST as $key => $value) {
	if(preg_match("/^id(\d+)$/",$key,$matches)) {
		$postdata_data[$matches[1]] = $value;
	}
}
$postdata = http_build_query($postdata_data);
$opts_post['http']['content'] = $postdata;
$context_post = stream_context_create($opts_post);
$data = file_get_contents( "${baseurl}/save_order", false, $context_post );
$jdata = json_decode($data,true);

header("Location: " . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 'https' : 'http') . "://" . $_SERVER["HTTP_HOST"] . "/index.php?state={$_REQUEST['state']}&code={$_REQUEST['code']}")
?>