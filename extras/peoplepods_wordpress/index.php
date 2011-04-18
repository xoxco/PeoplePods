<?php
/*
Plugin Name: PeoplePods
Plugin URI: http://xoxco.com/o/peoplepods
Description: Makes PeoplePods functionality available to all Wordpress templates.
Version: 1.0
Author: Ben Brown, XOXCO
Author URI: http://xoxco.com
*/

// Customize this line to reflect your actual PeoplePods install dir.
$peoplePodsInstallPath = "/Volumes/Pixel/Users/benbrown/Sites/pp/trunk";
include_once("$peoplePodsInstallPath/PeoplePods.php");
$POD = new PeoplePod(array('authSecret'=>@$_COOKIE['pp_auth']));


?>