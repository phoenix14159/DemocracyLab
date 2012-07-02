<?php
define('DL_BASESCRIPT',substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/')));
require_once(DL_BASESCRIPT . '/lib/prelib.php');
require_once(DL_BASESCRIPT . '/AppInfo.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>DemocracyLab</title>
	<link rel="stylesheet" href="stylesheets/screen.css" media="screen">
	<link href="images/favicon.ico" rel="shortcut icon">
	<script src="js/jquery-1.7.2.js"></script>
</head>
<body>
<header class="clearfix">
	<div style="margin-left: -5px; background-image: url(images/dl.png); width: 293px; height: 102px; float: left; margin-right: 20px;"></div>
	<div style="float: left; width: 395px;">
		<h1 style="color: #6485a2; font-size: 210%;">Welcome to our engagement platform</h1>
		<p style="color: black;
		font: 14px/1.5em 'Lucida Grande',Arial,sans-serif;
		line-height: 130%;
		margin-bottom: 5px;">We're creating new tools to help communities make better
		decisions. You can learn more about us at <a href="http://democracylab.org/">democracylab.org</a>.
		</p>
		<p style="color: black;
		font: 14px/1.5em 'Lucida Grande',Arial,sans-serif;
		line-height: 130%;">To participate, 
		we'll ask you to share the values, objectives, and
		policies that most closely reflect your thinking on the current
		issue. But first you need to login to DemocracyLab.
		</p>
	</div>
</header>

<div id="issue-section" class="clearfix">
	<div id="fb-root"></div>
    <script>
	  $(document).ready(function () {
		$('#twitter-login-button').click(function () {
			window.location='loginviatwitter.php';
		});
		$('#linkedin-login-button').click(function () {
			window.location='loginvialinkedin.php';
		});
	  });
      window.fbAsyncInit = function() {
        FB.init({
          appId      : <?= AppInfo::appID() ?>, // App ID
          channelUrl : '//democracylab.herokuapp.com/facebookchannel.php', // Channel File
          status     : true, // check login status
          cookie     : true, // enable cookies to allow the server to access the session
          xfbml      : true  // parse XFBML
        });
		FB.Event.subscribe('auth.login', function(response) {
		    var uid = response.authResponse.userID;
		    var accessToken = response.authResponse.accessToken;
			window.location = "loginviafacebook.php?uid=" + uid + "&token=" + accessToken;
		});
		FB.getLoginStatus(function(response) {
		  if (response.status === 'connected') {
		    // the user is logged in and has authenticated your
		    // app, and response.authResponse supplies
		    // the user's ID, a valid access token, a signed
		    // request, and the time the access token 
		    // and signed request each expire
		    var uid = response.authResponse.userID;
		    var accessToken = response.authResponse.accessToken;
			$('#facebook-login-button').click(function () {
				window.location = "loginviafacebook.php?uid=" + uid + "&token=" + accessToken;
			});
		  } else if (response.status === 'not_authorized') {
		    // the user is logged in to Facebook, 
		    // but has not authenticated your app
		  } else {
		    // the user isn't logged in to Facebook.
		  }
		 });
      };
      // Load the SDK Asynchronously
      (function(d){
         var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[1];
         if (d.getElementById(id)) {return;}
         js = d.createElement('script'); js.id = id; js.async = true;
         js.src = "//connect.facebook.net/en_US/all.js";
         ref.parentNode.insertBefore(js, ref);
       }(document));
    </script>
	<div style="margin-left: 80px;">
    	<div id="facebook-login-button" class="fb-login-button">Login with Facebook</div>
		<div id="twitter-login-button" style="background-image: url(images/sign-in-with-twitter-d.png); width: 151px; height: 24px; margin-top: 20px; cursor: pointer;"></div>
		<div id="linkedin-login-button" style="background-image: url(images/linkedin_login.png); width: 152px; height: 21px; margin-top: 20px; cursor: pointer;"></div>
	</div>
</div>

<?php 
$footer_include_description = true;
$footer_nologout = true;
require_once('lib/footer.php'); ?>
</body>
</html>
