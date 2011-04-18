<? 
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* core_authentication/login.php
* Handles requests to /login
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme
/**********************************************/

	include_once("../../lib/Core.php");
	
	$POD = new PeoplePod(array('authSecret'=>@$_COOKIE['pp_auth']));
	if (!$POD->libOptions('enable_core_authentication_login')) { 
		header("Location: " . $POD->siteRoot(false));
		exit;
	}

	$redirect_after_login = false;
		
	if ($_POST) { 
		// if we have a form being submitted, handle the login
	 	if (@$_POST['email'] && @$_POST['password']) {
	 		$POD = new PeoplePod(array('authSecret'=>md5($_POST['email'].$_POST['password'])));
	 		if (!$POD->success())  {
				$POD->addMessage($POD->error());
	 		}		
			if (!$POD->isAuthenticated()) {
				$POD->addMessage("Oops!  We could not log you in using that email address and password.");
			} else {

					$days = 15;
					if ($_POST['remember_me']) { 
						$days = 100;
					}
			
					setcookie('pp_auth',$POD->currentUser()->get('authSecret'),time()+(86400 * $days),"/");
					$redirect_after_login = true;
			}
		}

	}
	
	
	if ($redirect_after_login) {
		// if we logged in correctly, we redirect to the homepage of the site, or to any url passed in as a parameter	
		if ($_POST['redirect']) { 
		 	header("Location: " . $_POST['redirect']);
		} else {
			header("Location: " . $POD->siteRoot(false));
		}
	
	} else {
		$POD->header("Login");
		$p = $POD->getPerson(); // create an empty person record 
		$p->set('redirect',@$_GET['redirect']);
		$p->output('login');
		
		$POD->footer();
	} 
	
?>