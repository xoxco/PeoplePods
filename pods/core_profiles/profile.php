<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* core_profiles/profile.php
* Handles requests to user profiles
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/messaging
/**********************************************/
	include_once("../../PeoplePods.php");
	$POD = new PeoplePod(array('debug'=>0,'authSecret'=>@$_COOKIE['pp_auth']));
	if (!$POD->libOptions('enable_core_profiles')) { 
		header("Location: " . $POD->siteRoot(false));
		exit;
	}
	
	$profile_username = $_GET['username'];
	$PROFILE_PERSON = $POD->getPerson(array('stub'=>$profile_username));	
	
	if ($POD->isAuthenticated()) { 
		$POD->currentUser()->expireAlertsAbout($PROFILE_PERSON);
	}
	if ($PROFILE_PERSON->success()) { 
		$POD->header($PROFILE_PERSON->get('nick'),$POD->siteRoot(false) . '/feeds/person/' . $PROFILE_PERSON->get('stub'));
		$PROFILE_PERSON->output();
		$POD->footer();
	} else {
		header("Status: 404 Not Found");
		echo "404 Not Found";
	}
	
	
?>