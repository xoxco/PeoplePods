<?php

/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* dashboard/index.php
* Displays a customized "What's new" type dashboard for members
* as defined in my_theme/people/dashboard.php

* Displays a welcome page for non-members
* as defined in my_theme/people/welcome.php

* Documentation for this pod can be found here:
* http://peoplepods.net/readme
/**********************************************/


	include_once("../../PeoplePods.php");
	if ($_POST) { 
		$lockdown = 'verified';
	} else {
		$lockdown = null;
	}
	$POD = new PeoplePod(array('debug'=>0,'lockdown'=>$lockdown,'authSecret'=>@$_COOKIE['pp_auth']));
	if (!$POD->libOptions('enable_core_dashboard')) { 
		header("Location: " . $POD->siteRoot(false));
		exit;
	}
	
	if (@$_GET['msg']) {
		$POD->addMessage(htmlspecialchars(strip_tags($_GET['msg'])));
	}

	$POD->header();	
	if ($POD->isAuthenticated()) { 
		if (!isset($_GET['replies'])) { 
			$POD->currentUser()->output('dashboard');
		} else {
			$POD->currentUser()->output('dashboard_replies');
		}
	} else {
		$POD->getPerson()->output('welcome');
	}
	$POD->footer(); ?>