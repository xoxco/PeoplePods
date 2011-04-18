<? 
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* core_authentication/password.php
* Handles requests to /password_reset
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

		
	if (@$_POST['password'] && @$_POST['resetCode']) { 
	
		$user = $POD->getPerson(array('passwordResetCode'=>$_POST['resetCode']));
		if ($user->success()) {
			$user->set('password',$_POST['password']);
			$user->set('passwordResetCode','');
			$user->save();		
			$days = 1;
			setcookie('pp_auth',$user->get('authSecret'),time()+(86400 * $days),"/");
			header("Location: /");
			exit;
		} else {
			// nothing.  the user submitted a password reset attempt without a valid reset code, which means they are probably lying!
		}
	}

	$POD->header('Reset Password');

	// get a blank person for the output.
	$p = $POD->getPerson();
	


	if (@$_POST['email']) { 
		

			$user = $POD->getPerson(array('email'=>$_POST['email']));
			if ($user->success()) {
				$user->set('passwordResetCode',md5($user->get('nick') . getmypid()));
				$user->save();
				$user->sendPasswordReset();
				
				$p->set('msg','Check your email!  A message has been sent to the address you used to sign up that contains a link that will allow you to reset your password.',false);				

			} else {

				$p->set('msg','The email address you specified could not be found in the database.',false);
				
			}
		
		
	} else if (@$_GET['resetCode']) { 
	
		$p->set('resetCode',$_GET['resetCode'],false);
		
	} 
	
	$p->output('password_reset');

	$POD->footer();	
	
?>	
	