<?php 
    $path = dirname(__FILE__);

    $POD->registerPOD(
	'common_oauth',
	'common oauth connector',
	array(
	    "^common_oauth$"=>"core_oauth_connect/index.php",
	    "^common_oauth/(.*)/(.*)?(.*)$"=>'core_oauth_connect/index.php?service=$1&mode=$2&$3',
	    "^common_oauth/(.*)$"=>'core_oauth_connect/index.php?service=$1'
	),
	array(),
	$path . '/methods.php',
	'common_oauth_settings'
    );