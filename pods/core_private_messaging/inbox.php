<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* core_private_messaging/inbox.php
* Handles requests to /inbox
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/messaging
/**********************************************/

	include_once("../../PeoplePods.php");
	$POD = new PeoplePod(array('debug'=>2,'lockdown'=>'verified','authSecret'=>@$_COOKIE['pp_auth']));
	if (!$POD->libOptions('enable_core_private_messaging')) { 
		header("Location: " . $POD->siteRoot(false));
		exit;
	}


	$offset = 0;
	if (isset($_GET['offset'])) { 
		$offset = $_GET['offset'];
	}

	$inbox = $POD->getInbox(20,$offset);

	$POD->header("Conversations");
	$inbox->output('thread_short','header','pager','Conversations','You don\'t have any messages. :(');	
	$POD->footer();
	
?>