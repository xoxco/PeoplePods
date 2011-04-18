<? 
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* core_authentication/verify.php
* Handles requests to /verify
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme
/**********************************************/


	include_once("../../lib/Core.php");
	$POD = new PeoplePod(array('lockdown'=>'login','authSecret'=>@$_COOKIE['pp_auth']));
	if (!$POD->libOptions('enable_core_authentication_creation')) { 
		header("Location: " . $POD->siteRoot(false));
		exit;
	}

	if (@$_GET['key'] != '') {
	// we've got a key to verify.
	// user does not have to be logged in - we match by key and set cookies for login.
	
		$POD->currentUser()->verify($_GET['key']);
		if ($POD->currentUser()->success()) {
			$status = 'ok';		
		} else {
			$status = 'bad_key';
		}
	
	} else if (@$_GET['resend']) {
		$POD->currentUser()->welcomeEmail();	
		$status='key_resent';
	} else {
	// no key specified.  user may need a reminder mail, or may need to enter key manually.
		$status ='no_key';
	}

	$POD->header('Verify Your Account');
	$POD->currentUser()->set('verify_status',$status,false); // set temporary status field
	$POD->currentUser()->output('verify');
	$POD->footer();
?>
