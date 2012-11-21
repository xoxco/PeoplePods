<?php
/***********************************************
 * This file is part of PeoplePods
 * (c) xoxco, inc
 * http://PeoplePods.net http://xoxco.com
 *
 * /index.php
 * This file just redirects to the admin or install tools
 /**********************************************/
require_once ("PeoplePods.php");


//todo trying out the excellent moor library to help out (simplify and flexify) the routing issues of this project
//also has the benefit of being able to slowly tailor it to the current PeoplePods implementation
//the approach is to hardwire requests to this directory through http, while allowing the previous implementation
//using a predominantly .htaccess-based page controller still function in the direcotry above; for now.
//to start, I hope to match the current PeoplePods implementation use of .htaccess
//then replace it completely with this more flexible and robust option in time
require_once ("lib/moor/Moor.php");

//immediately set a handler for pages not routed by either .htaccess or in Moor
Moor::setNotFoundCallback('default404');

//go ahead and declare it, this is a fairly long file. No need to get lost
function default404(){
	include( "/pods/page_unknown/view.php" );
}

//immediately create a $POD object to test against
$POD = new PeoplePod( array("authSecret" => @$_COOKIE["pp_auth"], "debug" => 2 ));
//test
if ($POD -> success()) {
	//send to dashboard using .htaccess route
	header( "Location: dashboard");
} else {
	//header( "install");//todo it might just be worth removing the install directory after first installation
	header( "Location: unauthorized");
}


//set up cursory routes for testing

//current routing table through .htaccess is as follows:
/*
 * # BEGIN PeoplePods RULES
 #####################################
 # turn the RewriteEngine on so that these fancy rewrite rules work
 RewriteEngine On

 RewriteRule ^edit$	/PeoplePods/pods/core_usercontent/edit.php	[QSA,L] # contenttype_document_add
 RewriteRule ^show$	/PeoplePods/pods/core_usercontent/list.php	[QSA,L] # contenttype_document_list
 RewriteRule ^show/(.*)	/PeoplePods/pods/core_usercontent/view.php?stub=$1	[QSA,L] # contenttype_document_view
 RewriteRule ^api/2/(.*)	/PeoplePods/pods/core_api_simple/index_version2.php?method=$1	[QSA,L] # core_api_simple
 RewriteRule ^api$	/PeoplePods/pods/core_api_simple/index_version1.php	[QSA,L] # core_api_simple
 RewriteRule ^join	/PeoplePods/pods//core_authentication/join.php	[QSA,L] # core_authentication_creation
 RewriteRule ^verify	/PeoplePods/pods/core_authentication/verify.php	[QSA,L] # core_authentication_creation
 RewriteRule ^login	/PeoplePods/pods/core_authentication/login.php	[QSA,L] # core_authentication_login
 RewriteRule ^logout	/PeoplePods/pods/core_authentication/logout.php	[QSA,L] # core_authentication_login
 RewriteRule ^password_reset/(.*)	/PeoplePods/pods/core_authentication/password.php?resetCode=$1	[QSA,L] # core_authentication_login
 RewriteRule ^password_reset$	/PeoplePods/pods/core_authentication/password.php	[QSA,L] # core_authentication_login
 RewriteRule ^$	/PeoplePods/pods/dashboard/index.php	[QSA,L] # core_dashboard
 RewriteRule ^replies	/PeoplePods/pods/dashboard/index.php?replies=1	[QSA,L] # core_dashboard
 RewriteRule ^feeds/(.*)	/PeoplePods/pods/core_feeds/feed.php?args=$1	[QSA,L] # core_feeds
 RewriteRule ^lists/(.*)	/PeoplePods/pods/core_feeds/list.php?args=$1	[QSA,L] # core_feeds
 RewriteRule ^lists$	/PeoplePods/pods/core_feeds/list.php	[QSA,L] # core_feeds
 RewriteRule ^feeds$	/PeoplePods/pods/core_feeds/feed.php	[QSA,L] # core_feeds
 RewriteRule ^files/(.*)/(.*)	/PeoplePods/pods/core_files/index.php?id=$1&size=$2	[QSA,L] # core_files
 RewriteRule ^friends$	/PeoplePods/pods/core_friends/index.php	[QSA,L] # core_friends
 RewriteRule ^friends/(.*)	/PeoplePods/pods/core_friends/index.php?mode=$1	[QSA,L] # core_friends
 RewriteRule ^groups$	/PeoplePods/pods/core_groups/index.php	[QSA,L] # core_groups
 RewriteRule ^groups/(.*)/(.*)	/PeoplePods/pods/core_groups/group.php?stub=$1&command=$2	[QSA,L] # core_groups
 RewriteRule ^groups/(.*)	/PeoplePods/pods/core_groups/group.php?stub=$1	[QSA,L] # core_groups
 RewriteRule ^invite	/PeoplePods/pods/core_invite/index.php	[QSA,L] # core_invite
 RewriteRule ^pages/(.*)	/PeoplePods/pods/core_pages/view.php?stub=$1	[QSA,L] # core_pages
 RewriteRule ^inbox$	/PeoplePods/pods/core_private_messaging/inbox.php	[QSA,L] # core_private_messaging
 RewriteRule ^inbox/conversationwith/(.*)	/PeoplePods/pods/core_private_messaging/thread.php?username=$1	[QSA,L] # core_private_messaging
 RewriteRule ^people/(.*)	/PeoplePods/pods/core_profiles/profile.php?username=$1	[QSA,L] # core_profiles
 RewriteRule ^editprofile	/PeoplePods/pods/core_profiles/editprofile.php	[QSA,L] # core_profiles
 RewriteRule ^search	/PeoplePods/pods/core_search/search.php	[QSA,L] # core_search
 RewriteRule ^openid$	/PeoplePods/pods/openid_connect/index.php	[QSA,L] # openid_connect
 RewriteRule ^openid/(.*)	/PeoplePods/pods/openid_connect/index.php?mode=$1	[QSA,L] # openid_connect

 #####################################
 # END PeoplePods RULES
 * */

Moor::route("/", "dashboard") -> //needs to go to /PeoplePods/pods/dashboard/index.php	[QSA,L] # core_dashboard
	route("/dashboard", "dashboard") -> //needs to go to /PeoplePods/pods/dashboard/index.php	[QSA,L] # core_dashboard
	route("/unauthorized", "unauthorized") -> //needs to go to /PeoplePods/pods/unauthorized_landing_page/index.php
	route("/authentication", "authentication") -> //needs to go to
	route("/login", "login") -> //needs to go to /PeoplePods/pods/core_authentication/login.php	[QSA,L] # core_authentication_login
	route("/logout", "logout") -> //needs to go to /PeoplePods/pods/core_authentication/logout.php	[QSA,L] # core_authentication_login
	route("/password_reset/:resetCode", "passReset") -> //needs to go to /PeoplePods/pods/core_authentication/password.php?resetCode=$1	[QSA,L] # core_authentication_login
	route("/password_reset", "passReset") -> //needs to go to /PeoplePods/pods/core_authentication/password.php	[QSA,L] # core_authentication_login
	route("/replies", "dashboard") -> //needs to go to /PeoplePods/pods/dashboard/index.php?replies=1	[QSA,L] # core_dashboard
	route("/join", "join") -> //needs to go to /PeoplePods/pods/core_authentication/join.php	[QSA,L] # core_authentication_creation
	route("/verify", "verify") -> //needs to go to /PeoplePods/pods/core_authentication/verify.php	[QSA,L] # core_authentication_creation
	route("/edit", "edit") -> //needs to go to /PeoplePods/pods/core_usercontent/edit.php	[QSA,L] # contenttype_document_add
	route("/show", "content") -> //needs to go to /PeoplePods/pods/core_usercontent/list.php	[QSA,L] # contenttype_document_list
	route("/show/:stub", "content") -> //needs to go to /PeoplePods/pods/core_usercontent/view.php?stub=$1	[QSA,L] # contenttype_document_view
	route("/files/:id/:size", "files") -> //needs to go to /PeoplePods/pods/core_files/index.php?id=$1&size=$2	[QSA,L] # core_files
	route("/files", "files") -> 
	route("/friends", "friends") ->
	//the following few routes are handled by the same function, there is no differece between them. 
	route("/feeds/:args", "listFeeds") -> //needs to go to /PeoplePods/pods/core_feeds/feed.php?args=$1	[QSA,L] # core_feeds
	route("/feeds", "listFeeds") -> //needs to go to /PeoplePods/pods/core_feeds/feed.php	[QSA,L] # core_feeds
	route("/lists/:args", "listFeeds") -> //needs to go to /PeoplePods/pods/core_feeds/list.php?args=$1	[QSA,L] # core_feeds
	route("/lists", "listFeeds") -> //needs to go to /PeoplePods/pods/core_feeds/list.php	[QSA,L] # core_feeds
	
	route("/groups", "groups") -> 
	route("/groups/:stub/:command", "groups") -> //needs to go to /PeoplePods/pods/core_groups/group.php?stub=$1&command=$2	[QSA,L] # core_groups
	route("/groups/:stub", "groups") -> //needs to go to /PeoplePods/pods/core_groups/group.php?stub=$1	[QSA,L] # core_groups
	route("/groups", "groups") -> //needs to go to /PeoplePods/pods/core_groups/index.php	[QSA,L] # core_groups
	route("/invite", "invite") -> //needs to go to /PeoplePods/pods/core_invite/index.php	[QSA,L] # core_invite
	route("/pages", "pages") -> 
	route("/pages/:stub", "view") -> //needs to go to /PeoplePods/pods/core_pages/view.php?stub=$1	[QSA,L] # core_pages
	route("/patients", "patients") -> 
	route("/pm", "private_messaging") -> 
	route("/inbox/conversationwith/:username", "conversation") -> //needs to go to /PeoplePods/pods/core_private_messaging/thread.php?username=$1	[QSA,L] # core_private_messaging
	route("/inbox", "inbox") -> //needs to go to /PeoplePods/pods/core_private_messaging/inbox.php	[QSA,L] # core_private_messaging
	route("/people/:username", "profile") -> //needs to go to /PeoplePods/pods/core_profiles/profile.php?username=$1	[QSA,L] # core_profiles
	route("/editprofile", "editProfile") -> //needs to go to /PeoplePods/pods/core_profiles/editprofile.php	[QSA,L] # core_profiles
	route("/search", "search") -> //needs to go to /PeoplePods/pods/core_search/search.php	[QSA,L] # core_search
	route("/content", "user_content") -> 
	route("/dashboard/:healer", "healer_dashboard") -> 
	route("/fb", "fb_connect") -> 
	route("/gravatars", "gravatars") -> 
	route("/landing_page", "landing_page") -> 
	route("/openid/:mode", "openId") -> //needs to go to /PeoplePods/pods/openid_connect/index.php?mode=$1	[QSA,L] # openid_connect
	route("/openid", "openId") -> //needs to go to /PeoplePods/pods/openid_connect/index.php	[QSA,L] # openid_connect
	route("/placekitten", "placekitten") -> 
	route("/twitter", "twitter") -> 
	route("/friends/:mode", "friends") -> //needs to go to /PeoplePods/pods/core_friends/index.php?mode=$1	[QSA,L] # core_friends
	route("/friends", "friends") -> //needs to go to /PeoplePods/pods/core_friends/index.php	[QSA,L] # core_friends
	route("/admin/:id/", "admin") -> 
	route("/api", "api") -> //needs to go to /PeoplePods/pods/core_api_simple/index_version1.php	[QSA,L] # core_api_simple
	route("/api/:whichOne/:method", "api") -> //needs to go to /PeoplePods/pods/core_api_simple/index_version2.php?method=$1	[QSA,L] # core_api_simple
	route( "/tos", "terms_of_service" )-> //cursory terms of service link
	route("/install", "install") -> //todo needs to go to /PeoplePods/install/, but only the first run though...
run();

function dashboard() {
	include( "/pods/dashboard/index.php");
}# core_dashboard //needs to go to /PeoplePods/pods/dashboard/index.php	[QSA,L] # core_dashboard

function unauthorized() {
	include( "/pods/unauthorizes_landing_page/index.php");
}//needs to go to /PeoplePods/pods/unauthorizes_landing_page/index.php


function authentication() {
	include( "/pods/core_authentication/login.php");
}//authentication" )-> //needs to go to Location: /PeoplePods/pods/core_authentication/login.php //just an alternate route


function login() {
	include( "/pods/core_authentication/login.php");
}//login" )-> //needs to go to /PeoplePods/pods/core_authentication/login.php	[QSA,L] # core_authentication_login

function logout() {
	include( "/pods/core_authentication/logout.php");
}//logout" )-> //needs to go to /PeoplePods/pods/core_authentication/logout.php	[QSA,L] # core_authentication_login

function passReset( $resetCode ) {
	if( isset( $resetCode ) ){
		include( "/pods/core_authentication/password.php?resetCode=$resetCode");
	}else{
		include( "/pods/core_authentication/password.php");	
	}
}//passReset" )-> //needs to go to /PeoplePods/pods/core_authentication/password.php?resetCode=$1	[QSA,L] # core_authentication_login

function dashboard() {
	include( "/pods/dashboard/index.php?replies=1");
}//dashboard" )-> //needs to go to /PeoplePods/pods/dashboard/index.php?replies=1	[QSA,L] # core_dashboard

function join() {
	include( "/pods/core_authentication/join.php");
}//join" )-> //needs to go to /PeoplePods/pods/core_authentication/join.php	[QSA,L] # core_authentication_creation

function verify() {
	include( "/pods/core_authentication/verify.php");
}//verify" )-> //needs to go to /PeoplePods/pods/core_authentication/verify.php	[QSA,L] # core_authentication_creation

function edit() {
	include( "/pods/core_usercontent/edit.php");
}//edit" )-> //needs to go to /PeoplePods/pods/core_usercontent/edit.php	[QSA,L] # contenttype_document_add

function content() {
	include( "/pods/core_usercontent/list.php");
}//content" )-> //needs to go to /PeoplePods/pods/core_usercontent/list.php	[QSA,L] # contenttype_document_list

function content() {
	include( "/pods/core_usercontent/view.php?stub=$stub");
}//content" )-> //needs to go to /PeoplePods/pods/core_usercontent/view.php?stub=$stub	[QSA,L] # contenttype_document_view

function feeds() {
	include( "/pods/core_feeds/feed.php?args=$args");
}//feeds" )-> //needs to go to /PeoplePods/pods/core_feeds/feed.php?args=$args	[QSA,L] # core_feeds

function feeds() {
	include( "/pods/core_feeds/feed.php");
}//feeds" )-> //needs to go to /PeoplePods/pods/core_feeds/feed.php	[QSA,L] # core_feeds

//original path in .htaccess
function listFeeds( $args ) {
	if( isset( $args ) ){
		include( "/pods/core_feeds/list.php?args=$args");
	}else{
		include( "/pods/core_feeds/list.php");
	}
}//list" )-> //needs to go to /PeoplePods/pods/core_feeds/list.php	[QSA,L] # core_feeds

function files( $id, $size ) {
	if( isset( $id ) && isset( $size ) ){
		include( "/pods/core_files/index.php?id=$id&size=$size"); //todo recheck this path from the original .htaccess rule, specifically, see if both $id and $size are required.
	}else{
		include( "/pods/core_files/index.php");
	}
}//files" )-> //needs to go to /PeoplePods/pods/core_files/index.php?id=$id&size=$size	[QSA,L] # core_files



function groups( $stub = null, $command = null ) {
	if( isset( $stub ) && isset( $command ) ){
		include( "/pods/core_groups/group.php?stub=$stub&command=$command");
	}else if( $stub ) {
		include( "/pods/core_groups/group.php?stub=$stub");
	}else{
		include( "/pods/core_groups/index.php");
	}
}//groups" )-> //needs to go to /PeoplePods/pods/core_groups/group.php?stub=$stub&command=$command	[QSA,L] # core_groups # optional params

function invite() {
	include( "/pods/core_invite/index.php");
}//invite" )-> //needs to go to /PeoplePods/pods/core_invite/index.php	[QSA,L] # core_invite

function pages() {
	include( "/pods/core_pages/view.php?stub=$stub");
}//pages" )->

function view() {
	include( "/pods/core_pages/view.php?stub=$stub");
}//view" )-> //needs to go to /PeoplePods/pods/core_pages/view.php?stub=$stub	[QSA,L] # core_pages

function patients() {
	include( "/pods/dashboard/index.php");
}//patients" )->

function conversation( $username ) {
	include( "/pods/core_private_messaging/thread.php?username=$username");
}//conversation" )-> //needs to go to /PeoplePods/pods/core_private_messaging/thread.php?username=$username	[QSA,L] # core_private_messaging

function inbox() {
	include( "/pods/core_private_messaging/inbox.php");
}//inbox" )-> //needs to go to /PeoplePods/pods/core_private_messaging/inbox.php	[QSA,L] # core_private_messaging

function profile( $username ) {
	include( "/pods/core_profiles/profile.php?username=$username");
}//profile" )-> //needs to go to /PeoplePods/pods/core_profiles/profile.php?username=$username	[QSA,L] # core_profiles

function editProfile() {
	include( "/pods/core_profiles/editprofile.php");
}//editProfile" )-> //needs to go to /PeoplePods/pods/core_profiles/editprofile.php	[QSA,L] # core_profiles

function search() {
	include( "/pods/core_search/search.php");
}//search" )-> //needs to go to /PeoplePods/pods/core_search/search.php	[QSA,L] # core_search

function user_content() {
	include( "/pods/core_usercontent/view.php");
}//user_content" )->

function healer_dashboard() {
	include("/pods/healer_dashboard");
}//doctor_dashboard" )->

function fb_connect() {
	include( "/pods/dashboard/index.php");
}//fb_connect" )->

function gravatars() {
	include( "/pods/dashboard/index.php");
}//gravatars" )->

function landing_page() {
	include( "/pods/dashboard/index.php");
}//landing_page" )->

function openId( $mode ) {
	if( isset( $mode ) ){
		include( "/pods/openid_connect/index.php?mode=$mode");
	}else{
		include( "/pods/openid_connect/index.php");
	}
}//openId" )-> //needs to go to /PeoplePods/pods/openid_connect/index.php	[QSA,L] # openid_connect

function placekitten() {
	include( "/pods/dashboard/index.php");
}//placekitten" )->

function twitter() {
	include( "/pods/dashboard/index.php");
}//twitter" )->

function friends( $mode ) {
	if( isset( $mode ) ){}
		include( "/pods/core_friends/index.php?mode=$mode");
	}else{
		include( "/pods/core_friends/index.php" );
	}
}//friends" )-> //needs to go to /PeoplePods/pods/core_friends/index.php?mode=$mode	[QSA,L] # core_friends

function admin() {
	include( "/admin/index.php");
}//admin" )->

function api1() {//security needed here
	include( "/pods/core_api_simple/index_version1.php");
}//api" )-> //needs to go to /PeoplePods/pods/core_api_simple/index_version1.php	[QSA,L] # core_api_simple

function api2( $method ) {//security needed here
	include( "/pods/core_api_simple/index_version2.php?method=$method");
}//api" )-> //needs to go to /PeoplePods/pods/core_api_simple/index_version2.php?method=$method	[QSA,L] # core_api_simple


function terms_of_service(){
	include( "/pods/terms/view.php" );
}

function install() {//todo check if database is present before routing to install
	if( !$POD->success() ){ include( "/PeoplePods/install/index.php"); }
	else header( "Location: unauthorized" );
	
}//install" )-> //todo needs to go to /PeoplePods/install/, but only the first run though...


//todo Taking a bit of a break, but further work will be to implement all of the paths in the .htaccess, and write the routing handlers.
//Plugin creators will have to edit 2 files instead of one, but hey, that is the price of being able to generate the links to their plugins in the templates
//currently there are 100s of hardcoded links to different places in the code, they all have to be made a bit more flexible
?>

