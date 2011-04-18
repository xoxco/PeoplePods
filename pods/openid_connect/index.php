<?

	include_once("../../PeoplePods.php");
	$POD = new PeoplePod(array('debug'=>2,'authSecret'=>@$_COOKIE['pp_auth']));

	session_start();

	define('Auth_OpenID_RAND_SOURCE',null);

    require_once "Auth/OpenID/Consumer.php";
    require_once "Auth/OpenID/FileStore.php";
    require_once "Auth/OpenID/SReg.php";

	// got an open id submitted, send it off to be verified.
	if (isset($_POST['openid'])) { 
	
		$openid = $_POST['openid'];
		$consumer = getConsumer($POD);

	    // Begin the OpenID authentication process.
	    $auth_request = $consumer->begin($openid);
	
	    // No auth request means we can't begin OpenID.
	    if (!$auth_request) {
	        $POD->addMessage("Authentication error; not a valid OpenID.");
	    } else {	
	    		
		    $sreg_request = Auth_OpenID_SRegRequest::build(
		                                     // Required
		                                     array('nickname'),
		                                     // Optional
		                                     array('fullname', 'email'));
		
		    if ($sreg_request) {
		        $auth_request->addExtension($sreg_request);
		    }

		    if ($auth_request->shouldSendRedirect()) {

	        	$redirect_url = $auth_request->redirectURL($POD->siteRoot(false).'/openid',
	    	                                               $POD->siteRoot(false) . '/openid?mode=verify');
		
			    // If the redirect URL can't be built, display an error
		        // message.
		        if (Auth_OpenID::isFailure($redirect_url)) {
		            $POD->addMessage("Could not redirect to server: " . $redirect_url->message);
		        } else {
		            // Send redirect.
		            header("Location: ".$redirect_url);
		            exit;
		        }
		   } else {


		      $form_id = 'openid_message';
		        $form_html = $auth_request->htmlMarkup($POD->siteRoot(false), $POD->siteRoot(false) . '/openid?mode=verify',
		                                               false, array('id' => $form_id));
		
		        // Display an error if the form markup couldn't be generated;
		        // otherwise, render the HTML.
		        if (Auth_OpenID::isFailure($form_html)) {
		            $POD->addMessage("Could not redirect to server: " . $form_html->message);
		        } else {
		            print $form_html;
		            exit;
		        }		   
		   
		   
		   }
	 	}	
	}
	
	if ($_GET['mode']=="verify") { 
	// did I just succeed in verifying an openid?
	    $consumer = getConsumer($POD);
	
	    // Complete the authentication process using the server's
	    // response.
	    $return_to = $POD->siteRoot(false) . '/openid?mode=verify';
	    
	    
	    $response = $consumer->complete($return_to);

	    // Check the response status.
	    if ($response->status == Auth_OpenID_CANCEL) {
	        // This means the authentication was cancelled.
			header("Location: " . $POD->siteRoot(false) .'/openid');
	    } else if ($response->status == Auth_OpenID_FAILURE) {
	        // Authentication failed; display the error message.
	       	header("Location: " . $POD->siteRoot(false) .'/openid?msg=badopenid');
	    } else if ($response->status == Auth_OpenID_SUCCESS) {
	        // This means the authentication succeeded; extract the
	        // identity URL and Simple Registration data (if it was
	        // returned).
	        $openid = $response->getDisplayIdentifier();
	        $esc_identity = htmlspecialchars($openid);
			$people = $POD->getPeople(array('openid'=>$esc_identity));
			
			if (!$POD->isAuthenticated()) { 

	
				if ($people->count()==1) { 
					// successful login
					$POD->changeActor(array('id'=>$people->getNext()->get('id')));
					$days = 15;
					setcookie('pp_auth',$POD->currentUser()->get('authSecret'),time()+(86400 * $days),"/");
					header("Location: " . $POD->siteRoot(false));
					exit;
				} else if ($people->count()==0) { 
			        $sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
			        $sreg = $sreg_resp->contents();
			        $open_id_info['openid'] = $openid;
			        $open_id_info['email'] = @$sreg['email'];
			        $open_id_info['fullname'] = @$sreg['fullname'];
			        $open_id_info['nickname'] = @$sreg['nickname'];
			        $open_id_info['zip'] = @$sreg['postcode'];
				} 
			} else  {
	

				if ($people->count()==0) { 
						// a valid id was passed in by a user who was already authenticated, and there is nobody else with this id, so that means we
						// were adding it to an account
						$POD->currentUser()->addMeta('openid',$esc_identity);
						$POD->addMessage('Your OpenID was added!');
				
				} else { 
				
					$POD->addMessage("Another account is already connected to the OpenID account you chose.");
					$open_id_info = null;
				}
			}
	
	    }


	}
	
	if ($_GET['group']) { 
		setcookie('pp_group',$_GET['group'],time()+60*60*24*30,"/");		
	}
	$POD->header('Connect with OpenID');
	if ($POD->isAuthenticated()) { 
	

		if ($_GET['rod']) { 
			$POD->currentUser()->addMeta('openid',null);
			$msg ="Your OpenID has been removed.";
		}

		$user = $POD->currentUser();
		
			
	}  else {
	
		
		$user = $POD->getPerson();
		
		// is this person perhaps joining in via open id?
		if ($open_id_info) { 
		

			$user->set('email',$open_id_info['email']);
			$user->set('nick',$open_id_info['fullname']);
			$user->set('zipcode',$open_id_info['zip']);
			$user->set('openid',$open_id_info['openid']);

			$user->set('group',@$_COOKIE['pp_group']);
			$user->set('invite',@$_COOKIE['pp_invite_code']);

			
			$user->output('join');
			$user->footer();
			exit;
			
		} 
		
		
		
	}	
	
	$user->output('login.openid');
	$POD->footer();

	
	function &getStore($POD) {
	    /**
	     * This is where the example will store its OpenID information.
	     * You should change this path if you want the example store to be
	     * created elsewhere.  After you're done playing with the example
	     * script, you'll have to remove this directory manually.
	     */
	
	    $path = $POD->libOptions('cacheDir');
	    $store_path = $path . "/openid";
	
	    if (!file_exists($store_path) &&
	        !mkdir($store_path)) {
	        print "Could not create the FileStore directory '$store_path'. ".
	            " Please check the effective permissions.";
	        exit(0);
	    }
	
	    return new Auth_OpenID_FileStore($store_path);
	}
	
	function &getConsumer($POD) {
	    /**
	     * Create a consumer object using the store object created
	     * earlier.
	     */
	    $store = getStore($POD);
	    $consumer =& new Auth_OpenID_Consumer($store);
	    return $consumer;
	}



