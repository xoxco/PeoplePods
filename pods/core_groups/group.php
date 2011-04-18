<? 
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* core_groups/group.php
* Handles requests to /groups/mygroup
* Handles requests to member manager
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme
/**********************************************/


	include_once("../../PeoplePods.php");
	if ($_POST || isset($_GET['command'])) { 
		$lockdown = 'verified';
	} else {
		$lockdown = null;
	}

	$POD = new PeoplePod(array('debug'=>0,'lockdown'=>$lockdown,'authSecret'=>@$_COOKIE['pp_auth']));

	if (!$POD->libOptions('enable_core_groups')) { 
		header("Location: " . $POD->siteRoot(false));
		exit;
	}
	
	$group = $POD->getGroup(array('stub'=>$_GET['stub']));
	
	if (!$group->success()) {
		header("Status: 404 Not Found");
		echo "404 Not Found";
		exit;
	}
	if ($group->get('type')=="private") { 
		if (!$POD->isAuthenticated() || !$group->isMember($POD->currentUser())) { 
			header("Status: 404 Not Found");
			echo "404 Not Found";
			exit;
		}	
	}	
	if ($POD->isAuthenticated()) { 
		$POD->currentUser()->expireAlertsAbout($group);
	}
	
	$template = "output";
	
	if (isset($_GET['command'])) {
		// do something here.
		// join
		// quit
		// delete
		// remove document
		if ($_GET['command'] == "members") {
			$template = "member_manager"; 
		} 
		
		if ($_GET['command'] == "delete") { 
		
			if ($_POST['confirm']==md5($POD->currentUser()->get('memberSince'))) { 
				$group->delete();
				if ($group->success()) {
					header("Location: " . $POD->siteRoot(false) . "/groups");
					exit;
				} else {
					$POD->addMessage("Group delete failed! " . $group->error());
				}
			}		
		}		
		if ($_GET['command'] == "edit") { 
			
			// if there is a _POST, this is an edit save
			// if not, this is an edit get
			
			if ($_POST) { 		
				$group->set('groupname',$_POST['groupname']);
				$group->set('description',$_POST['description']);				
				$group->set('type',$_POST['type']);
				$group->save();
				
				if (!$group->success()) { 
					$POD->addMessage("Save Failed! " . $group->error());
					$template = "edit_group";
				} else {
					$POD->addMessage("Group Saved!");
				}
			} else {
				$template = "edit_group";
			}
		}
		if ($_GET['command'] == "remove") { 
			$doc = $POD->getContent(array('id'=>$_GET['docId']));
			if ($doc->success()) { 
				$group->removeContent($doc);
				if ($group->success()) { 
					$POD->addMessage("Post removed from group!");
				} else {
					$POD->addMessage("Error: " . $group->error());
				}
			} else {
				$POD->addMessage("Error! " . $doc->error());
			}
		}
		
		if ($_GET['command'] == "join") { 
		
			if ($group->isMember($POD->currentUser())=="invitee") { 
				$group->changeMemberType($POD->currentUser(),"member");
			} else {
				$group->addMember($POD->currentUser());

			}
			if ($group->success()) { 
				$POD->addMessage("You joined this group!");
			} else {
				$POD->addMessage("ERROR! " . $group->error());
			}
		}

		if ($_GET['command'] == "quit") { 
		
			if ($group->isMember($POD->currentUser())) { 
				$group->removeMember($POD->currentUser());
			}
			if ($group->success()) { 
				$POD->addMessage("You quit this group!");
			} else {
				$POD->addMessage("ERROR! " . $group->error());
			}
		}
	} 
	
	$POD->header($group->get('groupname'));
		if (isset($_GET['offset'])) {
			$group->content()->getOtherPage($_GET['offset']);
		}
		$group->output($template);

	$POD->footer();	
	?>

