<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* core_authentication/join.php
* Handles requests to /join
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme
/**********************************************/

	include_once("../../lib/Core.php"); 

	$POD = new PeoplePod(array('authSecret'=>@$_COOKIE['pp_auth']));
	if (!$POD->libOptions('enable_core_authentication_creation')) { 
		header("Location: " . $POD->siteRoot(false));
		exit;
	}

	$redir = false;
	
	// if we have all the necessary fields, let's create a new user!
	if (@$_POST['email']&& @$_POST['name']) {
		
		if (!$_POST['password'] && !($_POST['meta_openid']||$_POST['meta_fbuid']||$_POST['meta_twitter_token'])) {
			$POD->addMessage("You must specify a password!");			
		} else if (!$_POST['password'] && ($_POST['meta_openid']||$_POST['meta_fbuid']||$_POST['meta_twitter_token'])) {
			$password = generatePassword();
		} else {
			$password = $_POST['password'];
		}

		$NEWUSER = $POD->getPerson(array('nick'=>$_POST['name'],'email'=>$_POST['email'],'password'=>$password,'invite_code'=>$_POST['code']));
		$NEWUSER->save();		
		if ($NEWUSER->success()) {
				setcookie('pp_auth',$NEWUSER->get('authSecret'),time()+60*60*24*30,"/");
				$POD = new PeoplePod(array('authSecret'=>$NEWUSER->get('authSecret')));
				$redir = true;

				// now we'll add any meta fields that have been passed in.
				// we do this by looking for anything with a field name starting with meta_
				// so if you want to add a meta field called foo to your content
				// you'll pass in the value via meta_foo
				foreach ($_POST as $key=>$value) { 
					if (preg_match("/^meta_(.*)/",$key,$match)) { 
					
						$key = $match[1];
						// add the field.
						// the third parameter is no_html, set it to true to strip html, or false to allow html
						$NEWUSER->addMeta($key,$value,true);
					
					}
				}




		} else {
			$POD->addMessage($NEWUSER->error());		
		}	
	}
	
	
	// create a temporary empty user to output the form
	$p = $POD->getPerson();
	
	$redirect = null;

	if (@$_POST['redirect'] || @$_GET['redirect']) {
		$redirect = ($_POST['redirect']) ? $_POST['redirect'] : $_GET['redirect'];	
		$p->set('redirect',$redirect);
	}

	
	$invite_code = null;
	
	if (@$_POST['code'] || @$_GET['code']) { 
		$invite_code = ($_POST['code']) ? $_POST['code'] : $_GET['code'];
		
		if ($invite_deets = $POD->isValidInvite($invite_code)) { 
			$p->set('code',$invite_code,false);
			$p->set('invited_by',$POD->getPerson(array('id'=>$invite_deets['userId'])),false);
			if ($invite_deets['groupId']) { 
				$p->set('invited_to_group',$POD->getGroup(array('id'=>$invite_deets['groupId'])),false);			
			}
		} else {
			$POD->addMessage("Sorry, but the invite code you used has expired.");
		}
	}

	if ($redir) {
		// if we logged in correctly, we redirect to the homepage of the site, or to any url passed in as a parameter	
		if ($redirect) { 
			print header("Location: " . $redirect);
		} else {
			print header("Location: " . $POD->siteRoot(false));
		}
	
	}
	
	$POD->header("Create an account");
	$p->output('join');
	$POD->footer();


	function generatePassword($length=9, $strength=8) {
		$vowels = 'aeuy';
		$consonants = 'bdghjmnpqrstvz';
		if ($strength & 1) {
			$consonants .= 'BDGHJLMNPQRSTVWXZ';
		}
		if ($strength & 2) {
			$vowels .= "AEUY";
		}
		if ($strength & 4) {
			$consonants .= '23456789';
		}
		if ($strength & 8) {
			$consonants .= '@#$%';
		}
	 
		$password = '';
		$alt = time() % 2;
		for ($i = 0; $i < $length; $i++) {
			if ($alt == 1) {
				$password .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			} else {
				$password .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}
		return $password;
	}
 

?>