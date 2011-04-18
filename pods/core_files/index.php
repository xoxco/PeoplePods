<? 
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* core_files/index.php
* Handles simple requests to /download
* Allows files to be downloaded with their original file name
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme
/**********************************************/

	

	include_once("../../PeoplePods.php");
	error_reporting(0);
	$POD = new PeoplePod(array('debug'=>0)); // no parameters, we want the most basic pod we can get to serve images

	if (!$POD->libOptions('enable_core_files')) { 
		header("Location: " . $POD->siteRoot(false));
		exit;
	}
	
	
	$f = $POD->getFile(array('id'=>$_GET['id']));
	if (!$f->success()) {
		header("Status: 404 Not Found");
		echo "404 Not Found";		
		exit;
	}

	$f->download($_GET['size']);
	if (!$f->success()) {
		header("Status: 404 Not Found");
		echo "Download Failed";		
	}
	
?>