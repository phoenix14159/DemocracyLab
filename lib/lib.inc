<?php
/*
 * Input variables:
 * DL_BASESCRIPT = absolute directory root
 *
 * Output Variables:
 *
 * $opts_get = options for database api GET requests
 * $opts_post = options for database api POST requests
 * $democracylab_user_id = (obvious)
 * $baseurl = the database api url
 *
 * $basic = some kind of Facebook data structure
 * $my_id = Facebook user id
 * $app_id = Facebook application id
 * $app_info = Facebook application info
 * 
 */
require_once(DL_BASESCRIPT . '/lib/prelib.inc');

require_once(DL_BASESCRIPT . '/FBUtils.php');
require_once(DL_BASESCRIPT . '/AppInfo.php');
require_once(DL_BASESCRIPT . '/utils.php');

$token = FBUtils::login(AppInfo::getHome());
if ($token) {

	// Fetch the viewer's basic information, using the token just provided
	$basic = FBUtils::fetchFromFBGraph("me?access_token=$token");
	$my_id = assertNumeric(idx($basic, 'id'));

	// Fetch the basic info of the app that they are using
	$app_id = AppInfo::appID();
	$app_info = FBUtils::fetchFromFBGraph("$app_id?access_token=$token");  

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
		echo "Error calling api\n";
		exit;
	}
	if(!isset($jdata['ok'])) {
		echo "Remote site is down when calling api\n";
		exit;
	}
*/
} else {
	// Stop running if we did not get a valid response from logging in
	exit("Invalid credentials");
}
?>

