<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/groups/output.php
* Default output page for a group object
*
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/group-object
/**********************************************/
?>
<?
	// get current user's membership
	$membership = $group->isMember($POD->currentUser());
?>
	<div class="column_8">

		<ul class="group_actions">
		<? if ($membership == "invitee") { ?>
			<li><a href="<? $group->write('permalink'); ?>/join">Accept invite to join this group</a></li>
		<? } else if ($membership =="owner" || $membership == "manager") { ?>
		
			<li><a href="<? $group->write('permalink'); ?>/edit">Edit this group</a></li>
			<li><a href="<? $group->write('permalink'); ?>/members">Manage Members</a></li>
			<li><a href="<? $group->POD->siteRoot(); ?>/invite?group=<? $group->write('id'); ?>">Invite Someone</a></li>

		<? } else if ($membership =="" && $group->get('type')=="public") { ?>
			<li><a href="<? $group->write('permalink'); ?>/join">Join this group</a></li>			
		<? } else {  ?>
			<li><a href="<? $group->write('permalink'); ?>/quit">Quit this group</a></li>
			<li><a href="<? $group->write('permalink'); ?>/members">View Members</a></li>
			<li><a href="<? $group->POD->siteRoot(); ?>/invite?group=<? $group->write('id'); ?>">Invite Someone</a></li>
		<? } ?>
		</ul>

		<h1><? $group->permalink(); ?></h1>

		<? if ($membership == "owner" || $membership == "member") { 
		
			// display the content addition form
			$new = $POD->getContent();
			$new->set('groupId',$group->get('id'));
			$new->output('editform');
		
		 } else { // if is member ?>
		
			<h1>Groups &#187; <? $group->write('groupname'); ?></h1>
		
			<p>This is a public group started by <? $group->owner()->permalink(); ?> with <a href="<? $group->write('permalink'); ?>/members"><? echo $group->members()->totalCount(); ?> <? echo $group->POD->pluralize($group->members()->totalCount(),'member','members'); ?></a>.</p>
			
			<p>You are not a member.  <a href="<? $group->write('permalink'); ?>/join">Join this group &#187;</a></p>
		
		<? } ?>

		<? 
			$group->content()->output('short','header','pager','Latest Stuff in ' . $group->get('groupname'),'No posts in this group yet!'); 
		?>	

	</div>

	<div class="column_4 structure_only">
		<div id="group_description">
			<b>About this group:</b>
			<? $group->writeFormatted('description'); ?>
		</div>

		<? if ($group->POD->isAuthenticated()) { ?>
			<ul id="navigator">
				<li class="dashboard_navigator"><a href="<? $group->POD->siteRoot(); ?>">New Stuff</a></li>
				<li class="dashboard_navigator dashboard_navigator_last"><a href="<? $group->POD->siteRoot(); ?>/replies">Activity</a></li>

				<? 
					$groups = $group->POD->getGroups(array('mem.type:!='=>'invitee','mem.userId'=>$group->POD->currentUser()->get('id')));
					while ($g = $groups->getNext()) { 
						if ($g->get('id') == $group->get('id')) { $g->set('active','active',false); } 
						$g->output('group_navigator'); 
					}
				?>
				<li class="group_navigator"><a href="<? $group->POD->siteRoot(); ?>/groups">More Groups...</li>					
			</ul>	
		<? } ?>	
	</div>