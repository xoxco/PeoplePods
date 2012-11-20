<?
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* /index.php
* This file just redirects to the admin or install tools
/**********************************************/

	include_once("PeoplePods.php");
	$POD = new PeoplePod(array('authSecret'=>@$_COOKIE['pp_auth']));
	if ($POD->success()) {
		header("Location: admin");
	} else {
		//header("Location: install");//@todo investigate the particular heading/routing that this function uses, but this path and likely the whole directory, should be removed from final
		header( "Location: unauthorized_landing_page" );
	} 

?>	
	
	
	