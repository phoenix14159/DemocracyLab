<?php
session_start();
$_SESSION['page'] = 'entities';
$_SESSION['type'] = $_REQUEST['type'];

//$baseurl = "http://localhost/~bjorn/bjornfreemanbenson.com/democracylab";
$baseurl = "http://bjornfreemanbenson.com/democracylab";
$opts_get = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"X-BFB-API-KEY: 90A60668-8CCD-11E0-BD09-DE584824019B\r\n" .
			  "X-BFB-API-VER: 1\r\n"
  )
);
$opts_post = array(
  'http'=>array(
    'method'=>"POST",
    'header'=> $opts_get['http']['header'] . "Content-type: application/x-www-form-urlencoded\r\n"
  )
);
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


header('Location: http://local.democracylab.com/');
?>