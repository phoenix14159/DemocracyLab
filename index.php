<?php

// Enforce https on production
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == "http" && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
  header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
  exit();
}

/**
 * This sample app is provided to kickstart your experience using Facebook's
 * resources for developers.  This sample app provides examples of several
 * key concepts, including authentication, the Graph API, and FQL (Facebook
 * Query Language). Please visit the docs at 'developers.facebook.com/docs'
 * to learn more about the resources available to you
 */

// Provides access to Facebook specific utilities defined in 'FBUtils.php'
require_once('FBUtils.php');
// Provides access to app specific values such as your app id and app secret.
// Defined in 'AppInfo.php'
require_once('AppInfo.php');
// This provides access to helper functions defined in 'utils.php'
require_once('utils.php');

/*****************************************************************************
 *
 * The content below provides examples of how to fetch Facebook data using the
 * Graph API and FQL.  It uses the helper functions defined in 'utils.php' to
 * do so.  You should change this section so that it prepares all of the
 * information that you want to display to the user.
 *
 ****************************************************************************/

// Log the user in, and get their access token
$token = FBUtils::login(AppInfo::getHome());
if ($token) {

  // Fetch the viewer's basic information, using the token just provided
  $basic = FBUtils::fetchFromFBGraph("me?access_token=$token");
  $my_id = assertNumeric(idx($basic, 'id'));

  // Fetch the basic info of the app that they are using
  $app_id = AppInfo::appID();
  $app_info = FBUtils::fetchFromFBGraph("$app_id?access_token=$token");  

  // This formats our home URL so that we can pass it as a web request
  $encoded_home = urlencode(AppInfo::getHome());
  $redirect_url = $encoded_home . 'close.php';

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
        'name' => idx($basic, 'name')
    )
);
$opts_post['http']['content'] = $postdata;
$context_post = stream_context_create($opts_post);
$data = file_get_contents( "${baseurl}/get_user", false, $context_post );
$jdata = json_decode($data,true);
$democracylab_user_id = $jdata['user_id'];
/*
if(isset($jdata['error'])) {
	echo "Error calling api/insert\n";
	exit;
}
if(!isset($jdata['ok'])) {
	echo "Remote site is down when calling api/insert\n";
	exit;
}
*/
require_once("pages/home.php");	

} else {
  // Stop running if we did not get a valid response from logging in
  exit("Invalid credentials");
}
?>
