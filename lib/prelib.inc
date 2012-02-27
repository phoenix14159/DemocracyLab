<?php
/*
 * Input variables:
 * DL_BASESCRIPT = absolute directory root
 *
 * Output Variables:
 * $dbconn = Postgresql database connection
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

if(isset($_ENV["DATABASE_URL"])) {
	extract(parse_url($_ENV["DATABASE_URL"]));
	$dbconn = pg_connect("user=$user password=$pass host=$host dbname=" . substr($path, 1))
	    or die('Could not connect: ' . pg_last_error());
} else {
	$pwd = file_get_contents('.database_password');
	$dbconn = pg_connect("host=localhost dbname=postgres user=postgres password=$pwd")
	    or die('Could not connect: ' . pg_last_error());
}
?>

