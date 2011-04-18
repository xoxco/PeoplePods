<?php 

$POD->registerPOD(
	"core_api_simple",
	'RESTful API for basic user actions',
	array(
		"^api/2/(.*)"=>"core_api_simple/index_version2.php?method=$1",
		"^api$"=>"core_api_simple/index_version1.php",
	),
	array()
);

?>