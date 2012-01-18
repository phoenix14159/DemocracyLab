<?php
/*
 * Input variables:
 * DL_BASESCRIPT = absolute directory root
 *
 * Output Variables:
 *
 * $opts_get = options for database api GET requests
 * $opts_post = options for database api POST requests
 * $baseurl = the database api url
 * 
 */
// Enforce https on production
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == "http" && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
  header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
  exit();
}

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
?>

