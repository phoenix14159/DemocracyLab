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


//header('Location: http://local.democracylab.com/');
header('Location: https://morning-ocean-5589.herokuapp.com/');
?>