<?php

	// this pod creates static pages
	include_once("../unauthorized_landing_page/content_type.php"); // this defines some variables for use within this pod

	$POD->registerPOD(
		"unauthorized_landing_page", // name
		"Use static page for entry of unauthorized users", // description
		array("^{$permalink}/(.*)"=> $pod_dir . '/view.php?stub=$1'),	// rewrite rules
		array(
			"content_permalink_{$content_type}"=>"/$permalink/{this.stub}",
			"content_editlink_{$content_type}"=>"/peoplepods/admin/content/?id={this.id}",
			"content_editpath_{$content_type}"=>"/peoplepods/admin/content/",
		)		// global variables
	);

?>