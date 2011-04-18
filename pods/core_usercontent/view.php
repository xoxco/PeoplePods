<?php

/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* core_usercontent/view.php
* Handles permalinks, comments and voting for this type of content
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/new-content-type
/**********************************************/


	include_once("content_type.php"); // this defines some variables for use within this pod
	include_once("../../PeoplePods.php");
	if ($_POST) {
		$lockdown = 'verified';
	} else {
		$lockdown = null;
	}
	$POD = new PeoplePod(array('debug'=>0,'lockdown'=>$lockdown,'authSecret'=>@$_COOKIE['pp_auth']));
	if (!$POD->libOptions("enable_contenttype_{$content_type}_view")) { 
		header("Location: " . $POD->siteRoot(false));
		exit;
	}

	if ($_GET['stub']) {		
		$doc = $POD->getContent(array('stub'=>$_GET['stub']));
	} else if ($_GET['id']) {
		$doc = $POD->getContent(array('id'=>$_GET['id']));	
	} else if ($_POST['id']) {
		$doc = $POD->getContent(array('id'=>$_POST['id']));	
	}
	
	if ($POD->isAuthenticated()) { 
		$POD->currentUser()->expireAlertsAbout($doc);
	}


	if (!$doc->success()) {
		header("Status: 404 Not Found");
		echo "404 Not Found";
		exit;
	}

	if (isset($_POST['comment'])) {  // this is a request to post a comment

		$comment = $doc->addComment($_POST['comment']);
		if (!$comment || !$comment->success()) {
			$POD->addMessage("Couldn't add comment! " . $doc->error());
		} else {
			header("Location: " . $doc->get('permalink') . "#" . $comment->get('id'));
			exit;
		}
	}
			
	if (isset($_GET['vote'])) { // this is a request to vote
	
		if ($POD->isAuthenticated()) {
			if (!$POD->currentUser()->getVote($doc)) {
				$doc->vote($_GET['vote']);
			 }
		} 
	
	}

	if (@$_GET['msg']) { 
		$POD->addMessage(strip_tags($_GET['msg']));
	}

	$POD->header($doc->get('headline')  );
	$doc->output($output_template);
	$POD->footer();
?>