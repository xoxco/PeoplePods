<?    
    /*
    * @Ben Brown: Please change the below
    * Written by Paul-Armand Verhaegen 
    * Based on the twitter_connect pod from PeoplePods
    * License same as PeoplePods license 
    */

    /* 
    * Can be used directly (meaning by properly formed links) or from other pods (be forming links)
    * e.g. connect to a service: http://my_server/common_oauth/twitter
    * e.g. remove account credentials stored for a service http://github_pp/common_oauth/twitter/remove
    * e.g. directly connect without going to the form http://my_server/common_oauth/twitter/verify
    */
    include_once("../../PeoplePods.php");

    // application specific variables stored here -> so go change them if you want another service (and deleted the rest?)
    include_once('app_variables.php');

    /* Can't do this without a verified user */
    $POD = new PeoplePod(array('debug'=>0,'authSecret'=>@$_COOKIE['pp_auth'],'lockdown'=>'verified')); 
    
    //**************** SERVICE NOT SET *******************************
    if ($bool_service_set != 1) {
	$user = $POD->currentUser();
	$user->set('err_msg', 'Service not set.', false); 	// Temporary error msg (not saved to user, see argument "false")
	$POD->header('Service name not set'); 
	$user->output('login.oauth', dirname(__FILE__).'/templates');
	$POD->footer();
	die();
    } //else { $POD->currentUser()->set('tmp_msg');} 	// Give the notice or error msg 
    //**************** END ******** SERVICE NOT SET *******************************

    //**************** APPLICATION LEVEL OAUTH KEYS --> KEEP THESE VERY VERY SECRET **********************
    $key = $POD->libOptions($app_api_key_name);  	// The general key
    $secret =  $POD->libOptions($app_api_secret_name);  // The general secret
    //**************** END******** APPLICATION LEVEL OAUTH KEYS --> KEEP THESE VERY VERY SECRET **********************

    //**************** METHOD REMOVE *******************************
    if (isset($_GET['mode']) && $_GET['mode']=='remove') {
	$POD->currentUser()->addMeta($app_api_user_secret_name, null);
	$POD->currentUser()->addMeta($app_api_user_token_name, null);
	$msg = "Your " . $app_name . " account has been removed.";
    }
    //**************** METHOD REMOVE *******************************

    session_start(); // start session to store the $app status and codes
    $access_token_info = array();

    // We need to verify an account
    if (isset($_GET['mode']) && $_GET['mode']=='verify') {
	// If the user was sent back from oauth, but has no oauth_token, then reset the state
	if (!isset($_SESSION[$app .'_state']) || ($_SESSION[$app .'_state']==1 && !isset($_GET['oauth_token']))) { 
	    $_SESSION[$app .'_state']=0;
	}
	try {
	    $oauth = new OAuth($key,$secret,OAUTH_SIG_METHOD_HMACSHA1,OAUTH_AUTH_TYPE_URI);
	    $oauth->enableDebug();  // This will generate debug output in your error_log
	    // State 0 - Generate request token and redirect user to $app to authorize
	    if(!isset($_SESSION[$app .'_state']) || $_SESSION[$app .'_state']==0) {
		$request_token_info = $oauth->getRequestToken($request.'?oauth_callback='.urlencode($oauth_callback_url));
		$_SESSION[$app.'_token'] = $request_token_info['oauth_token'];
		$_SESSION[$app.'_secret'] = $request_token_info['oauth_token_secret'];
		$_SESSION[$app.'_state'] = 1;
		header('Location: '.$auth.'?oauth_token='.$_SESSION[$app.'_token']);
		exit; 			
	    }
	    // State 1 - Handle callback from $app and get and store an access token
	    else if (isset($_SESSION[$app .'_state']) && $_SESSION[$app.'_state']==1) {
		$oauth->setToken($_GET['oauth_token'],$_SESSION[$app.'_secret']);
		$access_token_info = $oauth->getAccessToken($access);
		$oauth->setToken($access_token_info['oauth_token'],$access_token_info['oauth_token_secret']);

		// If we're authenticated within PP, then store the $app credentials for this profile
		if ($POD->isAuthenticated()) { 
		    // Check whether account is already in use by someone else
		    $test = $POD->getPeople(array($app_api_user_token_name=>$access_token_info['oauth_token']));
		    if ($test->count()==0) { 
			$POD->currentUser()->addMeta($app_api_user_token_name, $access_token_info['oauth_token']);
			$POD->currentUser()->addMeta($app_api_user_secret_name, $access_token_info['oauth_token_secret']);
			$msg = 'You have successfully connected your ' . $app_name . ' account.';
		    } 
		    else {
		      $msg = "Another account is already connected to the $app account you chose.";
		    }
		} 
		else {				
		    // is there a person with this $app info already in the db?  if so, log her in!
		    $user = $POD->getPeople(array($app_api_user_token_name=>$access_token_info['oauth_token']));
		    if ($user->count()==1) {
			$user = $user->getNext();
			// if so, and the user is logged out, log him in!
			$days = 15;
			setcookie('pp_auth',$user->get('authSecret'),time()+(86400 * $days),"/");
			header("Location: " . $POD->siteRoot(false));
			exit;
		    }	
		}
	    }
	} 
	catch(OAuthException $e) {
	    //echo 'Caught exception: ',  $e->getMessage(), "\n";die();
	    $msg = "Your " . $app_name . " login failed. Try again!";
	}
    }

    if (isset($_GET['group'])) { 
	setcookie('pp_group',$_GET['group'],time()+60*60*24*30,"/");		
    }
    
    if (!$POD->isAuthenticated()) { 
	$user = $POD->getPerson();
	if (isset($access_token_info['oauth_token'])) { 
	    // set some vars
	    $user->set($app_api_user_token_name, $access_token_info['oauth_token']);
	    $user->set($app_api_user_secret_name, $access_token_info['oauth_token_secret']);
	    $user->set('group',@$_COOKIE['pp_group']);
	    $user->set('invite',@$_COOKIE['pp_invite_code']);
	    $user->set('redirect',$POD->siteRoot(false));

	    $user->output('join');
	    $POD->footer();
	    exit;
	}
    } else {
	$user = $POD->currentUser();	
    }
    
    //**************** SET TEMPORARY DATA TO USER TO DISPLAY IN LOGIN FORM *******************************
    $POD->header('Connect using oauth'); 
    $connected = $user->get($app_api_user_secret_name)? true : false ;	 // is the user connected?
    $user->set('connected', $connected, false);
    $user->set('tmp_oauth_app', $app, false); 	// Give the name of the service to connect to
    if (isset($msg)) {$user->set('tmp_msg', $msg, false);} 	// Give the notice or error msg 
    //**************** END************ SET TEMPORARY DATA TO USER TO DISPLAY IN LOGIN FORM *******************************

    $user->output('login.oauth', dirname(__FILE__).'/templates');
    $POD->footer();