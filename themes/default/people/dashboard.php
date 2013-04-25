<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/people/dashboard.php
* Used by the dashboard module to create homepage of the site for members
* Displays a list of content the current user has created,
* and content from the user's friends and groups
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/person-object
/**********************************************/
?>
<?	


	$offset = 0;	
	if (isset($_GET['offset'])) {
		$offset = $_GET['offset'];
	}

	// load up a list of groups that this user is a member of
	$groups = $POD->getGroups(array('mem.type:!='=>'invitee','mem.userId'=>$user->get('id')));
	
	// load documents from friends or from groups.
	$fids = $user->friends(9999)->extract('id');
	$gids = $groups->extract('id');
	array_push($fids,$user->get('id'));
	$OR_PARAMS = array();
	if ($fids) { 
		$OR_PARAMS['userId'] = $fids;
	}
	if ($gids) {
		$OR_PARAMS['groupId'] = $gids;
	}
	$docs = $POD->getContents(array('or'=>$OR_PARAMS),null,10,$offset);
	if (!$docs->success()) { 
		$msg =  $docs->error();
	}
?>
	<div class="column_8">
		<?
			if ($user->get('verificationKey')) { ?>
				<div id="welcome_message">
					
					<p><strong>Welcome to <? $POD->siteName(); ?>!</strong>.  We are so glad you joined us.</p>
					<p>
						However, before you're allowed to post anything or leave comments, we need to <a href="<? $POD->siteRoot(); ?>/verify">verify your email address</a>.
						This lets us make sure that you aren't a spambot.
						You should already have the verification email in your inbox!
					</p>
					<p><a href="<? $POD->siteRoot(); ?>/verify">Verify My Account</a></p>
				
				</div>		
			<? } else {
			
				// output a blank edit form
				if ($POD->libOptions("enable_contenttype_document_add")) { 
					$POD->getContent()->output('editform'); 
				}
				
			}
		?>	
		
		<? $user->getAlerts()->output('output',null,null,null); ?>
				
		<? if (isset($msg)) { ?>	
			<div class="info">
				<? echo $msg; ?>
			</div>
		<? } ?>
		
		<!-- this is where new posts from friends and groups show up -->
		<? 
				$docs->output('short','header','pager','My New Stuff','There\'s nothing new yet!'); 
		?>

		
		
	</div>
	<div class="column_4 structure_only">
		<? 
			$user->output('member_info');
			
		?>			


			<ul id="navigator" class="padded">
				<li class="dashboard_navigator dashboard_active"><a href="<? $POD->siteRoot(); ?>">New Stuff</a></li>
				<? if ($POD->libOptions('enable_core_friends')) { 
					$user->output('member_friends');
				} ?>
				<li class="dashboard_navigator dashboard_navigator_last"><a href="<? $POD->siteRoot(); ?>/replies">Activity</a></li>
				<? if ($POD->libOptions('enable_core_groups')) { 					
					$groups->output('group_navigator',null,null); 
					?>
					<li class="group_navigator"><a href="<? $POD->siteRoot(); ?>/groups">More Groups...</a></li>					
					<?						
				} ?>
			</ul>					
			
			<? $POD->output('sidebars/activity_stream'); ?>
			
			<? $POD->output('sidebars/ad_unit'); ?>
			<? $POD->output('sidebars/tag_cloud'); ?>
			
			<? $POD->output('sidebars/recent_visitors'); ?>

	</div>
	<div class="clearer"></div>