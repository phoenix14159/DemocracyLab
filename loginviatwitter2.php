<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));

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
  $_SESSION['status'] = 'verified';
  echo "<pre>"; //MOREMORE
echo "connection:\n";//MOREMORE
print_r($connection); echo "\n"; //MOREMORE
echo "_SESSION:\n";//MOREMORE
print_r($_SESSION); echo "\n"; //MOREMORE
$content = $connection->get('account/verify_credentials'); //MOREMORE
echo "content:\n";//MOREMORE
print_r($content);echo "\n"; //MOREMORE
echo "</pre>"; //MOREMORE
//MOREMORE looking for user_id and screen_name
//MOREMORE  header('Location: ./summary.php');
} else {
  /* Save HTTP status for error dialog on connnect page.*/
  header('Location: ./cleartwittersessions.php');
}
