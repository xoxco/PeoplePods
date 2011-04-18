<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* core_profiles/editprofile.php
* Handles requests to /editprofile
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/messaging
/**********************************************/

	include_once("../../PeoplePods.php");
	$POD = new PeoplePod(array('debug'=>0,'lockdown'=>'login','authSecret'=>@$_COOKIE['pp_auth']));
	if (!$POD->libOptions('enable_core_profiles')) { 
		header("Location: " . $POD->libOptions('serverRoot'));
		exit;
	}



	if (@$_POST['email']!='') {
			$POD->currentUser()->set('nick',$_POST['nick']);
			$POD->currentUser()->set('email',$_POST['email']);
			$POD->currentUser()->save();
			if (!$POD->currentUser()->success()) {
				$POD->addMessage($POD->currentUser()->error());
			} else {
				$POD->addMessage("Your settings have been updated.");
				foreach ($_FILES as $filename=>$file) { 
					$POD->currentUser()->addFile($filename,$file);
					if (!$POD->currentUser()->success()) { 
						$POD->addMessage('An error occured while attaching your file: ' . $POD->currentUser()->error());
					}
				}
				$POD->currentUser()->files()->fill();

				// now we'll add any meta fields that have been passed in.
				// we do this by looking for anything with a field name starting with meta_
				// so if you want to add a meta field called foo to your content
				// you'll pass in the value via meta_foo
				foreach ($_POST as $key=>$value) { 
					if (preg_match("/^meta_(.*)/",$key,$match)) { 
					
						$key = $match[1];
						// add the field.
						// the third parameter is no_html, set it to true to strip html, or false to allow html
						$POD->currentUser()->addMeta($key,$value,true);
					
					}
				}


				if ($_POST['age']) {
					$POD->currentUser()->addMeta('age',strip_tags($_POST['age']));
				} else {
					$POD->currentUser()->addMeta('age',null);
				}
					
				if ($_POST['sex']) {
					$POD->currentUser()->addMeta('sex',strip_tags($_POST['sex']));
				} else {
					$POD->currentUser()->addMeta('sex',null);
				}
					
				if ($_POST['location']) {
					$POD->currentUser()->addMeta('location',strip_tags($_POST['location']));
				} else {
					$POD->currentUser()->addMeta('location',null);
				}				
	
				if ($_POST['tagline']) {
					$POD->currentUser()->addMeta('tagline',strip_tags($_POST['tagline']));
				} else {
					$POD->currentUser()->addMeta('tagline',null);
				}
				
				if ($_POST['homepage']) {
					$POD->currentUser()->addMeta('homepage',strip_tags($_POST['homepage']));
				} else {
					$POD->currentUser()->addMeta('homepage',null);
				}	
				
				if ($_POST['aboutme']) {		
					$POD->currentUser()->addMeta('aboutme',$_POST['aboutme']);
				} else {
					$POD->currentUser()->addMeta('aboutme',null);
				}
				

			}
									
			$days = 15;			
			setcookie('pp_auth',$POD->currentUser()->get('authSecret'),time()+(86400 * $days),"/");
	}
		
	if (@$_POST['password']!='') {
		
			$POD->currentUser()->set('password',$_POST['password']);
			$POD->currentUser()->save();
			
			if (!$POD->currentUser()->success()) {
				$POD->addMessage($POD->currentUser()->error());
			} else {
				$POD->addMessage("Your password has been changed.");
			}

			$days = 15;			
			setcookie('pp_auth',$POD->currentUser()->get('authSecret'),time()+(86400 * $days),"/");
		
	}


	$POD->header('Edit Profile');	
	$POD->currentUser()->output('editprofile');
	$POD->footer(); ?>
