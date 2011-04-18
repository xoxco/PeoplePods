<?

	// this pod actually contains 3 separate pods that relate to basic content creation and management
	// these modules are meant to be customized to create custom content types
	// see http://peoplepods.net/readme/new-content-type


	// including this file will set some configuration variables for this pod.
	require("content_type.php");


	// this pod creates the add/edit interface for the content
	$POD->registerPOD(
		"contenttype_{$content_type}_add",										// name
		"Create add/edit page for $content_type",					// description
		array("^{$edit_link}\$"=>$pod_dir .'/edit.php'),				// rewrite rules
		array("content_editpath_{$content_type}"=>$edit_pattern,
			 "content_editlink_{$content_type}"=>$edit_pattern.'?id={this.id}'
		)		// global variables
	);
	
	// this pod creates the content permalink pages
	$POD->registerPOD(
		"contenttype_{$content_type}_view",									// name
		"Create default permalink pages for $content_type",				// description
		array("^{$permalink}/(.*)"=> $pod_dir . '/view.php?stub=$1'),	// rewrite rules
		array("content_permalink_{$content_type}"=>$permalink_pattern)		// global variables
	);


	// this pod creates a list of most recent content
	$POD->registerPOD(
		"contenttype_{$content_type}_list",									// name
		"Create default list page for $content_type",				// description
		array("^{$permalink}$"=>$pod_dir . '/list.php'),				// rewrite rules
		array()														// global variables
	);




?>