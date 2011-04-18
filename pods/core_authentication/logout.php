<?
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* core_authentication/logout.php
* Handles requests to /logout
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme
/**********************************************/

	include_once("../../lib/Core.php");
	
	$POD = new PeoplePod();
	if (!$POD->libOptions('enable_core_authentication_login')) { 
		header("Location: " . $POD->siteRoot(false));
		exit;
	}

	setcookie('pp_user','',0,"/");
	setcookie('pp_pass','',0,"/");
	setcookie('pp_auth','',0,"/");
	
	session_destroy();
	
	if ($_SERVER['HTTP_REFERER']) { 
		header("Location: " . $_SERVER['HTTP_REFERER']);
	} else {
		header("Location: ". $POD->siteRoot(false));
	}
?>