<?php 

$POD->registerPOD(
	"core_api_dispatch",
	'Core API dispatcher based on Observer Pattern',
	array(
		"^dispatch/(.*)"=>"core_api_dispatch/dispatch.php?request=$1"
	),
	array()
);

?>