<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* /index.php
* This file just redirects to the admin or install tools
/**********************************************/
include_once("PeoplePods.php");

//todo trying out the excellent moor library to help out (simplify and flexify) the routing issues of this project
//also has the benefit of being able to slowly tailor it to the current peoplepods implementation
//to start, I hope to match the current peoplepods implementation use of .htaccess
//as a basic router and each pod as an individual controller
//then replace it completely with this more flexible and robust option in time
require_once( 'lib/moor/Moor.php' );

//set up cursory routes for testing

//current routing table through .htaccess is as follows:
/*
 * # BEGIN PEOPLEPODS RULES
#####################################
# turn the RewriteEngine on so that these fancy rewrite rules work
RewriteEngine On

RewriteRule ^edit$	/sn/pp2/peoplepods/pods/core_usercontent/edit.php	[QSA,L] # contenttype_document_add
RewriteRule ^show$	/sn/pp2/peoplepods/pods/core_usercontent/list.php	[QSA,L] # contenttype_document_list
RewriteRule ^show/(.*)	/sn/pp2/peoplepods/pods/core_usercontent/view.php?stub=$1	[QSA,L] # contenttype_document_view
RewriteRule ^api/2/(.*)	/sn/pp2/peoplepods/pods/core_api_simple/index_version2.php?method=$1	[QSA,L] # core_api_simple
RewriteRule ^api$	/sn/pp2/peoplepods/pods/core_api_simple/index_version1.php	[QSA,L] # core_api_simple
RewriteRule ^join	/sn/pp2/peoplepods/pods//core_authentication/join.php	[QSA,L] # core_authentication_creation
RewriteRule ^verify	/sn/pp2/peoplepods/pods/core_authentication/verify.php	[QSA,L] # core_authentication_creation
RewriteRule ^login	/sn/pp2/peoplepods/pods/core_authentication/login.php	[QSA,L] # core_authentication_login
RewriteRule ^logout	/sn/pp2/peoplepods/pods/core_authentication/logout.php	[QSA,L] # core_authentication_login
RewriteRule ^password_reset/(.*)	/sn/pp2/peoplepods/pods/core_authentication/password.php?resetCode=$1	[QSA,L] # core_authentication_login
RewriteRule ^password_reset$	/sn/pp2/peoplepods/pods/core_authentication/password.php	[QSA,L] # core_authentication_login
RewriteRule ^$	/sn/pp2/peoplepods/pods/dashboard/index.php	[QSA,L] # core_dashboard
RewriteRule ^replies	/sn/pp2/peoplepods/pods/dashboard/index.php?replies=1	[QSA,L] # core_dashboard
RewriteRule ^feeds/(.*)	/sn/pp2/peoplepods/pods/core_feeds/feed.php?args=$1	[QSA,L] # core_feeds
RewriteRule ^lists/(.*)	/sn/pp2/peoplepods/pods/core_feeds/list.php?args=$1	[QSA,L] # core_feeds
RewriteRule ^lists$	/sn/pp2/peoplepods/pods/core_feeds/list.php	[QSA,L] # core_feeds
RewriteRule ^feeds$	/sn/pp2/peoplepods/pods/core_feeds/feed.php	[QSA,L] # core_feeds
RewriteRule ^files/(.*)/(.*)	/sn/pp2/peoplepods/pods/core_files/index.php?id=$1&size=$2	[QSA,L] # core_files
RewriteRule ^friends$	/sn/pp2/peoplepods/pods/core_friends/index.php	[QSA,L] # core_friends
RewriteRule ^friends/(.*)	/sn/pp2/peoplepods/pods/core_friends/index.php?mode=$1	[QSA,L] # core_friends
RewriteRule ^groups$	/sn/pp2/peoplepods/pods/core_groups/index.php	[QSA,L] # core_groups
RewriteRule ^groups/(.*)/(.*)	/sn/pp2/peoplepods/pods/core_groups/group.php?stub=$1&command=$2	[QSA,L] # core_groups
RewriteRule ^groups/(.*)	/sn/pp2/peoplepods/pods/core_groups/group.php?stub=$1	[QSA,L] # core_groups
RewriteRule ^invite	/sn/pp2/peoplepods/pods/core_invite/index.php	[QSA,L] # core_invite
RewriteRule ^pages/(.*)	/sn/pp2/peoplepods/pods/core_pages/view.php?stub=$1	[QSA,L] # core_pages
RewriteRule ^inbox$	/sn/pp2/peoplepods/pods/core_private_messaging/inbox.php	[QSA,L] # core_private_messaging
RewriteRule ^inbox/conversationwith/(.*)	/sn/pp2/peoplepods/pods/core_private_messaging/thread.php?username=$1	[QSA,L] # core_private_messaging
RewriteRule ^people/(.*)	/sn/pp2/peoplepods/pods/core_profiles/profile.php?username=$1	[QSA,L] # core_profiles
RewriteRule ^editprofile	/sn/pp2/peoplepods/pods/core_profiles/editprofile.php	[QSA,L] # core_profiles
RewriteRule ^search	/sn/pp2/peoplepods/pods/core_search/search.php	[QSA,L] # core_search
RewriteRule ^openid$	/sn/pp2/peoplepods/pods/openid_connect/index.php	[QSA,L] # openid_connect
RewriteRule ^openid/(.*)	/sn/pp2/peoplepods/pods/openid_connect/index.php?mode=$1	[QSA,L] # openid_connect

#####################################
# END PEOPLEPODS RULES
 * */

Moor::
	route( '/', 							'dashboard' )-> //needs to go to /sn/pp2/peoplepods/pods/dashboard/index.php	[QSA,L] # core_dashboard
	route( '/dashboard',					'dashboard' )-> //needs to go to /sn/pp2/peoplepods/pods/dashboard/index.php	[QSA,L] # core_dashboard								
	route( '/unauthorized',					'unauthorized' )->
	route( '/authentication', 				'authentication' )-> //needs to go to 
	route( '/login',  						'login' )-> //needs to go to /sn/pp2/peoplepods/pods/core_authentication/login.php	[QSA,L] # core_authentication_login
	route( '/logout',						'logout' )-> //needs to go to /sn/pp2/peoplepods/pods/core_authentication/logout.php	[QSA,L] # core_authentication_login
	route( '/password_reset/:resetCode', 	'passReset' )-> //needs to go to /sn/pp2/peoplepods/pods/core_authentication/password.php?resetCode=$1	[QSA,L] # core_authentication_login
	route( '/password_reset',  				'passReset' )-> //needs to go to /sn/pp2/peoplepods/pods/core_authentication/password.php	[QSA,L] # core_authentication_login
	route( '/replies',  					'dashboard' )-> //needs to go to /sn/pp2/peoplepods/pods/dashboard/index.php?replies=1	[QSA,L] # core_dashboard
	route( '/join',  						'join' )-> //needs to go to /sn/pp2/peoplepods/pods/core_authentication/join.php	[QSA,L] # core_authentication_creation
	route( '/verify',  						'verify' )-> //needs to go to /sn/pp2/peoplepods/pods/core_authentication/verify.php	[QSA,L] # core_authentication_creation
	route( '/edit',  						'edit' )-> //needs to go to /sn/pp2/peoplepods/pods/core_usercontent/edit.php	[QSA,L] # contenttype_document_add
	route( '/show',  						'content' )-> //needs to go to /sn/pp2/peoplepods/pods/core_usercontent/list.php	[QSA,L] # contenttype_document_list
	route( '/show/:stub', 					'content' )-> //needs to go to /sn/pp2/peoplepods/pods/core_usercontent/view.php?stub=$1	[QSA,L] # contenttype_document_view
	route( '/feeds/:args', 					'feeds' )-> //needs to go to /sn/pp2/peoplepods/pods/core_feeds/feed.php?args=$1	[QSA,L] # core_feeds
	route( '/feeds',  						'feeds' )-> //needs to go to /sn/pp2/peoplepods/pods/core_feeds/feed.php	[QSA,L] # core_feeds
	route( '/files/:id/:size', 				'files' )-> //needs to go to /sn/pp2/peoplepods/pods/core_files/index.php?id=$1&size=$2	[QSA,L] # core_files
	route( '/files',						'files' )->
	route( '/friends',						'friends' )->
	route( '/lists/:args',  				'list' )-> //needs to go to /sn/pp2/peoplepods/pods/core_feeds/list.php?args=$1	[QSA,L] # core_feeds
	route( '/lists', 						'list' )-> //needs to go to /sn/pp2/peoplepods/pods/core_feeds/list.php	[QSA,L] # core_feeds
	route( '/groups',						'groups' )->
	route( '/groups/:stub/:command',		'groups' )-> //needs to go to /sn/pp2/peoplepods/pods/core_groups/group.php?stub=$1&command=$2	[QSA,L] # core_groups
	route( '/groups/:stub', 				'groups' )-> //needs to go to /sn/pp2/peoplepods/pods/core_groups/group.php?stub=$1	[QSA,L] # core_groups
	route( '/groups',  						'groups' )-> //needs to go to /sn/pp2/peoplepods/pods/core_groups/index.php	[QSA,L] # core_groups
	route( '/invite', 						'invite' )-> //needs to go to /sn/pp2/peoplepods/pods/core_invite/index.php	[QSA,L] # core_invite
	route( '/pages',						'pages' )->
	route( '/pages/:stub', 					'view' )-> //needs to go to /sn/pp2/peoplepods/pods/core_pages/view.php?stub=$1	[QSA,L] # core_pages
	route( '/patients',						'patients' )->
	route( '/pm',							'private_messaging' )->
	route( '/inbox/conversationwith/:username', 'conversation' )-> //needs to go to /sn/pp2/peoplepods/pods/core_private_messaging/thread.php?username=$1	[QSA,L] # core_private_messaging
	route( '/inbox', 						'inbox' )-> //needs to go to /sn/pp2/peoplepods/pods/core_private_messaging/inbox.php	[QSA,L] # core_private_messaging
	route( '/people/:username', 			'profile' )-> //needs to go to /sn/pp2/peoplepods/pods/core_profiles/profile.php?username=$1	[QSA,L] # core_profiles
	route( '/editprofile', 					'editProfile' )-> //needs to go to /sn/pp2/peoplepods/pods/core_profiles/editprofile.php	[QSA,L] # core_profiles
	route( '/search', 						'search' )-> //needs to go to /sn/pp2/peoplepods/pods/core_search/search.php	[QSA,L] # core_search
	route( '/content',						'user_content' )->
	route( '/dashboard/:doctor',			'doctor_dashboard' )->
	route( '/fb',							'fb_connect' )->
	route( '/gravatars',					'gravatars' )->
	route( '/landing_page',					'landing_page' )->
	route( '/openid/:mode', 				'openId' )-> //needs to go to /sn/pp2/peoplepods/pods/openid_connect/index.php?mode=$1	[QSA,L] # openid_connect
	route( '/openid', 						'openId' )-> //needs to go to /sn/pp2/peoplepods/pods/openid_connect/index.php	[QSA,L] # openid_connect
	route( '/placekitten',					'placekitten' )->
	route( '/twitter',						'twitter' )->
	route( '/friends/:mode', 				'friends' )-> //needs to go to /sn/pp2/peoplepods/pods/core_friends/index.php?mode=$1	[QSA,L] # core_friends
	route( '/friends', 						'friends' )-> //needs to go to /sn/pp2/peoplepods/pods/core_friends/index.php	[QSA,L] # core_friends
	route( '/admin/:id/',					'admin' )->
	route( '/api', 							'api' )-> //needs to go to /sn/pp2/peoplepods/pods/core_api_simple/index_version1.php	[QSA,L] # core_api_simple
	route( '/api/:whichOne/:method', 		'api' )-> //needs to go to /sn/pp2/peoplepods/pods/core_api_simple/index_version2.php?method=$1	[QSA,L] # core_api_simple
	route( '/install', 						'install' )-> //todo needs to go to /sn/pp2/peoplepods/install/, but only the first run though...
	run();


$POD = new PeoplePod(array('authSecret'=>@$_COOKIE['pp_auth']));
if ($POD->success()) {
	header("Location: dashboard");
} else {
	//header("Location: install");//@todo investigate the particular heading/routing that this function uses, but this path and likely the whole directory, should be removed from final
	header( "Location: unauthorized" );
}

//todo Taking a bit of a break, but further work will be to implement all of the paths in the .htaccess, and write the routing handlers.
//Plugin creators will have to edit 2 files instead of one, but hey, that is the price of being able to generate the links to their plugins in the templates
//currently there are 100s of hardcoded links to different places in the code, they all have to be made a bit more flexible

?>	


