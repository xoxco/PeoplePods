<?

	include_once("../../PeoplePods.php");
	$POD = new PeoplePod(array('debug'=>0,'authSecret'=>@$_COOKIE['pp_auth']));



	$request = 'http://twitter.com/oauth/request_token';
	$access = 'http://twitter.com/oauth/access_token';
	$auth = 'http://twitter.com/oauth/authorize';

	$key = $POD->libOptions('twitter_api');
	$secret =  $POD->libOptions('twitter_secret');
	
	if (!($key && $secret)) { 
		$POD->header('Configuration Required');
		echo '<div class="info">Configuration required!</div>';
		echo '<p>To enable Twitter login, please set the Twitter API key and secret via the PeoplePods command center.</p>';
		echo '<P>To obtain the necessary details from Twitter, <a href="https://dev.twitter.com/apps">register your app</a>.</p>';
		$POD->footer();
		exit;	
	}
	
	
	// we need to make sure sessions are on so we can store the twitter codes
	session_start();
	if (isset($_GET['remove'])) { 
	
		$POD->currentUser()->addMeta('twitter_name',null);
		$POD->currentUser()->addMeta('twitter_secret',null);
		$POD->currentUser()->addMeta('twitter_token',null);
		$POD->currentUser()->addMeta('twitter_id',null);
		$POD->addMessage("Your Twitter account has been removed.");
	}
	
	$access_token_info = array();
			
	if (@$_GET['mode']=='verify') {

		if ($_SESSION['twitter_state']==1 && !isset($_GET['oauth_token'])) { $_SESSION['twitter_state']=0; }

		try {
	
			$oauth = new OAuth($key,$secret,OAUTH_SIG_METHOD_HMACSHA1,OAUTH_AUTH_TYPE_URI);
			$oauth->enableDebug();  // This will generate debug output in your error_log
			
			if($_SESSION['twitter_state']==0) {
			
				// State 0 - Generate request token and redirect user to Twitter to authorize
				$request_token_info = $oauth->getRequestToken('https://twitter.com/oauth/request_token');
				
				$_SESSION['twitter_token'] = $request_token_info['oauth_token'];
				$_SESSION['twitter_secret'] = $request_token_info['oauth_token_secret'];
				$_SESSION['twitter_state'] = 1;
				header('Location: https://twitter.com/oauth/authorize?oauth_token='.$_SESSION['twitter_token']);
				exit; 
			
			} else if ($_SESSION['twitter_state']==1) {
			
				// State 1 - Handle callback from Twitter and get and store an access token
				$oauth->setToken($_GET['oauth_token'],$_SESSION['twitter_secret']);
				$access_token_info = $oauth->getAccessToken('https://twitter.com/oauth/access_token');
				
				$oauth->setToken($access_token_info['oauth_token'],$access_token_info['oauth_token_secret']);
				$oauth->fetch('https://twitter.com/account/verify_credentials.json'); 
				$json = json_decode($oauth->getLastResponse());
			
			    if ($POD->isAuthenticated()) { 
			    
					$test = $POD->getPeople(array('twitter_token'=>$access_token_info['oauth_token']));
					if ($test->count()==0) { 
						$POD->currentUser()->addMeta('twitter_token',$access_token_info['oauth_token']);
						$POD->currentUser()->addMeta('twitter_secret',$access_token_info['oauth_token_secret']);
						$POD->currentUser()->addMeta('twitter_name',(string)$json->screen_name);
						$POD->currentUser()->addMeta('twitter_id',(string)$json->id);
						$POD->addMessage("You have successfully connected your Twitter account.");
					} else {
						$POD->addMessage("Another account is already connected to the Twitter account you chose.");
					}
				} else {
				
				
					// is there a person with this twitter info already in the db?  if so, log her in!
					$user = $POD->getPeople(array('twitter_token'=>$access_token_info['oauth_token']));
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

	} catch(OAuthException $E) {
		$POD->addMessage("Your Twitter login failed. Try again!");
	}

	}
	
	
	if (isset($_GET['group'])) { 
		setcookie('pp_group',$_GET['group'],time()+60*60*24*30,"/");		
	}
	$POD->header('Connect with Twitter'); 
	if (!$POD->isAuthenticated()) { 

		$user = $POD->getPerson();

		if (isset($access_token_info['oauth_token'])) { 

			// set some vars
			$user->set('twitter_token',$access_token_info['oauth_token']);
			$user->set('twitter_secret',$access_token_info['oauth_token_secret']);
			$user->set('twitter_name',(string)$json->screen_name);
			$user->set('nick',(string)$json->name);
			$user->set('twitter_id',(string)$json->id);

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
	
	
	$user->output('login.twitter');
	
	$POD->footer();
		
 ?>