<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* core_private_messaging/thread.php
* Displays a single thread, accepts new messages
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/messaging
/**********************************************/

	include_once("../../PeoplePods.php");
	$POD = new PeoplePod(array('debug'=>0,'lockdown'=>'verified','authSecret'=>@$_COOKIE['pp_auth']));
	if (!$POD->libOptions('enable_core_private_messaging')) { 
		header("Location: " . $POD->siteRoot(false));
		exit;
	}

	$inbox = $POD->getInbox();

	$username = $_GET['username'];
	$user = $POD->getPerson(array('stub'=>$username));
	if (!$user->success()) { 
		header("Status: 404 Not Found");
		echo "404 Not Found";
		exit;
	}
		

	$thread = $inbox->newThread($user->get('id'));

	if (isset($_POST['message'])) {
		$thread->reply(strip_tags($_POST['message']));
	}

	
	if (isset($_GET['clear'])) {
		$thread->clear();
		if (!$thread->success()) {
			$POD->addMessage("Conversation could not be cleared! " . $thread->error());
		} else {
			$POD->addMessage("Conversation cleared.");
		}
	}


	$POD->header('Conversation with ' . $user->get('nick'));
	$thread->output();
	$thread->markAsRead();
	$POD->footer();
?>
