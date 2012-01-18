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
 * Functions:
 * dl_facebook_url($page,$type=0)
 *		return the FB url for this page, includes all the FB id parameters
 *		type is optional
 * dl_facebook_form_fields($type=0)
 *		return the FB id parameters in hidden form fields
 *		type is optional
 * dl_facebook_redirect_url($page,$type=0)
 *		return the full FB url for this page, including http: or https:
 * dl_typestring($type,$idx=0)
 *		return (singular lowercase, singular uppercase, capitalized lc, capitalized uc)
 *		or, with $idx, the single string
 * 
 */
// Enforce https on production
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == "http" && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
  header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
  exit();
}

function dl_typestring($type,$idx=0) {
	if($type == 1) {
		$ar = array('lcs'=>'value','ucs'=>'Value','lcp'=>'values','ucp'=>'Values');
	} else if($type == 2) {
		$ar = array('lcs'=>'objective','ucs'=>'Objective','lcp'=>'objectives','ucp'=>'Objectives');
	} else if($type == 3) {
		$ar = array('lcs'=>'policy','ucs'=>'Policy','lcp'=>'policies','ucp'=>'Policies');
	} else {
		$ar = array('lcs'=>'?','ucs'=>'?','lcp'=>'?','ucp'=>'?');
	}
	if($idx) {
		return $ar[$idx];
	} else {
		return $ar;
	}
}
function dl_facebook_url($page,$type=0) {
	if($type) {
		return $page . "?type={$type}&state={$_REQUEST['state']}&code={$_REQUEST['code']}";
	} else {
		return $page . "?state={$_REQUEST['state']}&code={$_REQUEST['code']}";
	}
}
function dl_facebook_redirect_url($page,$type=0) {
	return ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 'https' : 'http') . "://" . $_SERVER["HTTP_HOST"] . "/" . dl_facebook_url($page,$type);
}
function dl_facebook_form_fields($type=0) {
	return "<input type='hidden' name='state' value='{$_REQUEST['state']}'>"
		.  "<input type='hidden' name='code' value='{$_REQUEST['code']}'>";
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

