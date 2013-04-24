<? 
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* core_groups/index.php
* Handles requests to /groups
* Creates new groups
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme
/**********************************************/

	include_once("../../PeoplePods.php");
	if ($_POST) { 
		$lockdown = 'verified';
	} else {
		$lockdown = ''; // anyone can see the groups homepage.
	}

	$POD = new PeoplePod(array('debug'=>0,'lockdown'=>$lockdown,'authSecret'=>@$_COOKIE['pp_auth']));
	if (!$POD->libOptions('enable_core_groups')) { 
		header("Location: " . $POD->siteRoot(false));
		exit;
	}
		
	
	$max = 10;
	$offset = 0;
	if (isset($_GET['offset'])) { $offset = $_GET['offset']; }
	
	if ($_POST) { 
	
		$group = $POD->getGroup();
		$group->set('groupname',$_POST['groupname']);
		$group->set('description',$_POST['description']);				
		$group->set('type',$_POST['type']);
		$group->save();
		
		if (!$group->success()) { 
			$POD->addMessage("Your group was not created! " . $group->error());
		} else {
			$POD->addMessage("Your new group, " . $group->permalink('groupname',true) . " is ready! ");
		}
	
	}
	if (isset($_GET['q'])) { 
		$header = "Group Search";
		$groups = $POD->getGroups(array('or'=>array('groupname:like'=>'%' . $_GET['q'] . '%','description:like'=>'%' . $_GET['q'] . '%')),'g.date DESC',$max,$offset);
	} else if ($POD->isAuthenticated()) {
		$header = "My Groups";
		$groups = $POD->getGroups(array('mem.userId'=>$POD->currentUser()->get('id')),'mem.date DESC',$max,$offset);	
		if ($groups->totalCount() == 0) { 
			$header = "Newish Groups";
			$groups = $POD->getGroups(array('type'=>'public'),'g.date DESC',$max,$offset);		
		}
	} else {
		$header = "Newish Groups";
		$groups = $POD->getGroups(array('type'=>'public'),'g.date DESC',$max,$offset);
	}
	if (!$groups->success()) { 
		$POD->addMessage($groups->error());
	}
	
	$POD->header('My Groups');
	
	$groups->output('short','2col_header','groups_pager',$header,'No groups have been created');

	$POD->footer(); ?>
