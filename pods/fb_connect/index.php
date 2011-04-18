<?

	include_once("../../PeoplePods.php");
	$POD = new PeoplePod(array('debug'=>0,'authSecret'=>@$_COOKIE['pp_auth']));

	$key = $POD->libOptions('fb_connect_api');
	$secret =  $POD->libOptions('fb_connect_secret');
	
	// we need to make sure sessions are on so we can store the twitter codes
	session_start();
	if (isset($_GET['rfb'])) { 
		$POD->currentUser()->removeMeta('facebook_token');
		$POD->currentUser()->removeMeta('fbuid');
		$POD->addMessage("Your Facebook account has been removed.");
	}

	$display = 'page';	
	if (strpos($_SERVER['HTTP_USER_AGENT'],"iPhone") || strpos($_SERVER['HTTP_USER_AGENT'],"Android")) { 
	//	$display='touch';
	}


	
	if (!($key && $secret)) { 
		$POD->header('Configuration Required');
		echo '<div class="info">Configuration required!</div>';
		echo '<p>To enable Facebook login, please set the Facebook API key and secret via the PeoplePods command center.</p>';
		echo '<P>To obtain the necessary details from Facebook, <a href="http://www.facebook.com/developers/">register your app</a>.</p>';
		$POD->footer();
		exit;	
	}
	

	$user = null;
	
	if (isset($_GET['code'])) {
		$code = $_GET['code'];
		$oauth_token = file_get_contents("https://graph.facebook.com/oauth/access_token?client_id=" . $key . "&redirect_uri=" . $POD->siteRoot(false) . "/facebook&client_secret=" . urlencode($secret) . "&code=" . urlencode($code));

		list($junk,$oauth_token) = explode("=",$oauth_token);
		// if authenticated, add to user
		// if not, new user time!
		
	    if ($POD->isAuthenticated() && !$POD->currentUser()->facebook_token) { 
	   	$user = $POD->currentUser();
	    
			$test = $POD->getPeople(array('facebook_token'=>$oauth_token));
			if ($test->count()==0) { 
				$user->addMeta('facebook_token',$oauth_token);
				$json = $user->getFacebookInfo();

				$user->set('fbuid',$json->id);

				$POD->addMessage("You have successfully connected your Facebook account.");
			} else {
				$POD->addMessage("Another account is already connected to the Facebook account you chose.");
			}
		} else {
			// is there a person with this facebook info already in the db?  if so, log her in!
			$user = $POD->getPeople(array('facebook_token'=>$oauth_token));
			if ($user->count()==1) { 
				$user = $user->getNext();
				// if so, and the user is logged out, log him in!
				$days = 15;
				setcookie('pp_auth',$user->get('authSecret'),time()+(86400 * $days),"/");
				header("Location: " . $POD->siteRoot(false));				
				exit;
			} else {
				$user = $POD->getPerson();
				$user->set('facebook_token',$oauth_token);
		
				$json = $user->getFacebookInfo();
				
				$user->set('nick',$json->name);
				$user->set('fbuid',$json->id);
				$user->set('email',$json->email);
								
				$user->set('group',@$_COOKIE['pp_group']);
				$user->set('invite',@$_COOKIE['pp_invite_code']);
				$user->set('redirect',$POD->siteRoot(false),false);

			}
						
		}

		
	}
	
	if ($POD->isAuthenticated() && !$user) { 
		$user = $POD->currentUser();
	}
	
	
	// if there is no user, and/or this person doesn't have FB yet, send them to FB!
	if (!$user->facebook_token && @$_GET['mode']=='verify') { 
		header("Location: https://graph.facebook.com/oauth/authorize?client_id=" . $key . "&redirect_uri=" . $POD->siteRoot(false) . '/facebook&display=' . $display . '&scope=publish_stream,email,offline_access');
		exit;
	}

	$POD->header('Facebook Connect');
	if ($user && !$user->saved() && $user->facebook_token) {
		
		$user->output('join');
		
	} else {
		if (!$user) {  $user = $POD->getPerson(); } 
		$user->output('login.facebook');
	}
	$POD->footer();
		
 ?>