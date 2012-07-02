<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/prelib.php');

/**
 * Take the user when they return from Twitter. Get access tokens.
 * Verify credentials and redirect to based on response from Twitter.
 */

/* Start session and load lib */
session_start();
require_once(DL_BASESCRIPT . '/twitteroauth/twitteroauth.php');
require_once(DL_BASESCRIPT . '/lib/twitterconfig.php');

/* If the oauth_token is old, redirect to the connect page. */
if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
  $_SESSION['oauth_status'] = 'oldtoken';
  header('Location: ./cleartwittersessions.php');
}

/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

/* Request access tokens from twitter */
$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

/* Save the access tokens. Normally these would be saved in a database for future use. */
$_SESSION['access_token'] = $access_token;

/* Remove no longer needed request tokens */
unset($_SESSION['oauth_token']);
unset($_SESSION['oauth_token_secret']);

/* If HTTP response is 200 continue otherwise send to connect page to retry */
if (200 == $connection->http_code) {
	/* The user has been verified and the access tokens can be saved for future use */
	$content = $connection->get('account/verify_credentials');
	
	$uid = $content->id;
	$result = pg_query($dbconn, "SELECT * FROM democracylab_users WHERE twitter_id = $uid");
	$row = pg_fetch_object($result);
	if($row) {
		$democracylab_user_id = $row->user_id;
		$democracylab_user_role = $row->role;
	} else {
		if($content->name) {
			$rname = pg_escape_string($content->name);
		} else {
			$rname = pg_escape_string($content->screen_name);
		}
		$result = pg_query($dbconn, "INSERT INTO democracylab_users (twitter_id,name) VALUES ($uid,'$rname')");
		$result = pg_query($dbconn, "SELECT LASTVAL()");
		$row = pg_fetch_array($result);
		$democracylab_user_id = $row[0];
		$democracylab_user_role = 0;
	}
	$_SESSION['democracylab_user_id'] = $democracylab_user_id;
	$_SESSION['democracylab_user_role'] = $democracylab_user_role;

	header('Location: ./summary.php');
} else {
	header('Location: ./cleartwittersessions.php');
}
