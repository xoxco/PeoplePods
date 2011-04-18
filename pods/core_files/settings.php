<?php 

	$POD->registerPOD(
		"core_files", // name of pod
		"Make files download as their original names", // description of pod for settings menu
		array( // rewrite rules
			'^files/(.*)/(.*)'=>'core_files/index.php?id=$1&size=$2'
		),
		array( // extra variables
			'default_files_path'=>'files'
		)
	);

?>