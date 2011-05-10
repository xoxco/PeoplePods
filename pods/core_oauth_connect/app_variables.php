<?// This data can also be set on an application level in the options.php file

    //**************** SPECIFIC TO DIFFERENT OAUTH PODS*************************
    $bool_service_set = false;
    if (isset($_GET['service'])) {
	switch (strtolower($_GET['service'])) {  
	    case "linkedin":
		$bool_service_set = true;
		// Application details
		$app_name = 'Linkedin';
		$site = 'https://api.linkedin.com';
		$request = $site . '/uas/oauth/requestToken';
		$access = $site . '/uas/oauth/accessToken';
		$auth = $site . '/uas/oauth/authorize';
		break;

	    case "twitter":
		$bool_service_set = true;
		// Application details
		$app_name = 'Twitter';
		$site = 'http://twitter.com';
		$request = $site . '/oauth/request_token';
		$access = $site . '/oauth/access_token';
		$auth = $site . '/oauth/authorize';	
		break;

	    default:
	}
	//**************** END ** SPECIFIC TO DIFFERENT OAUTH PODS*************************

	//**************** DERIVATIVE VARIABLES*************************
	$prefix = 'user_';

	$app = strtolower($app_name);					// e.g. "linkedin"
	$app_api_key_name = $app . '_api';				// name of global application api key e.g. "linkedin_api"
	$app_api_secret_name = $app . '_secret';			// name of global application api secret e.g. "linkedin_secret"

	$app_api_user_secret_name = $prefix . $app . '_secret';		// name of user specific api secret e.g. "user_linkedin_secret"
	$app_api_user_token_name = $prefix . $app . '_token';		// name of user specific api secret e.g. "user_linkedin_token"

	$oauth_callback_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];  // callback to myself
	//**************** END** DERIVATIVE VARIABLES*************************

    }