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

	include_once("../../PeoplePods.php");
	
	//set up central object with info to query regarding the person and their login
	$POD = new PeoplePod(array('debug'=>0,'authSecret'=>@$_COOKIE['pp_auth'])); //todo this line check to see if auth is current, must change to opposite

	//if they have already logged in
	if( $POD->isAuthenticated() ){
		//send them to their respective dashboard //todo make smarter - needs to route between healer dashboards, patient, and family/friend dashboards.
		header( 'Location: dashboard' );	
	}


	//if not the above locations, then output the view
	$POD->output( 'index',  '/village/unauthorized_landing_page/' );
?>