<? 
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* core_invite/index.php
* Send email invitations and generate invite codes.
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme
/**********************************************/
	include_once("../../PeoplePods.php");
	$POD = new PeoplePod(array('lockdown'=>'verified','authSecret'=>@$_COOKIE['pp_auth']));
	if (!$POD->libOptions('enable_core_invite')) { 
		header("Location: " . $POD->siteRoot(false));
		exit;
	}
	
	$max_allowed_invites = 5;
	
	
	if ($_POST) {  // send invites.
	
		for ($i = 1; $i <= $max_allowed_invites; $i++) {
			if (isset($_POST["email$i"]) && $_POST["email$i"] != '') {
				$POD->currentUser()->sendInvite($_POST["email$i"],$_POST['message'],@$_POST['group']);
				$POD->addMessage("Invites sent!");
			}
		}
	
	}
		
	$POD->header('Send Invites');	
	$POD->output('groups/invite');
	$POD->footer(); ?>
