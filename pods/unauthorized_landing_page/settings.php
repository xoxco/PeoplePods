<?php

	// this pod creates static pages
	$POD->registerPOD( 'unauthorized_landing_page','Set page for those that have not logged in.',array('^unauthorized$'=>'/themes/village/unauthorized_landing_page/index.php'),array());

?>