<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/prelib.inc');

$postdata = http_build_query(
    array(
		'type' => $_REQUEST['type'],
        'name' => $_REQUEST['name'],
		'description' => $_REQUEST['description']
    )
);
$opts_post['http']['content'] = $postdata;
$context_post = stream_context_create($opts_post);
$data = file_get_contents( "${baseurl}/add_entity", false, $context_post );
$jdata = json_decode($data,true);

header("Location: " . dl_facebook_redirect_url('entities.php',$_REQUEST['type']) );
?>