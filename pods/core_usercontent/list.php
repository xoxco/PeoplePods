<?php

/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* core_usercontent/list.php
* Handles the blog style reverse chronological list this type of content
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/new-content-type
/**********************************************/

	include_once("content_type.php"); // this defines some variables for use within this pod
	include_once("../../PeoplePods.php");
	$POD = new PeoplePod(array('authSecret'=>@$_COOKIE['pp_auth'],'debug'=>0));
	if (!$POD->libOptions("enable_contenttype_{$content_type}_list")) { 
		header("Location: " . $POD->siteRoot(false));
		exit;
	}

	$offset = 0;
	if (isset($_GET['offset'])) {
		$offset = $_GET['offset'];
	}
	$docs = $POD->getContents(array('type'=>$content_type,'status:!='=>'friends_only'),null,30,$offset);
	
	$POD->header('What\'s New?'); ?>

	<div class="column_8">
		<? $docs->output('short','header','pager','What\'s New?','Nothing has been posted on this site yet. Wow, it must be brand new!'); ?>
	</div>	
	<div class="column_4 structure_only">
		
		<? $POD->output('sidebars/search'); ?>

		<? $POD->output('sidebars/ad_unit'); ?>

		<? $POD->output('sidebars/tag_cloud'); ?>

		<? $POD->output('sidebars/recent_visitors'); ?>
		
	</div>	

<?	$POD->footer(); ?>
