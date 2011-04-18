<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/groups/member_manager.php
* Defines the group member manager page
*
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/group-object
/**********************************************/
?>

<?
	$membership = $group->isMember($group->POD->currentUser());
?>

	<header>
			<h1><? $group->permalink(); ?> &#187; Members</h1>

			<? echo $group->members()->totalCount(); ?> members | <a href="<? $group->POD->siteRoot(); ?>/invite?group=<? $group->write('id'); ?>">Invite Someone</a>
	</header>
	<div class="column_6">
			<B>Member</B>
	</div>
	<div class="column_2">
			<b>Type</b>
	</div>
	<div class="clearer"></div>
	<? 
	$group->members()->sortBy('type');
	while ($person = $group->members()->getNext()) { ?>
	
		<div id="person<? $person->write('id'); ?>" class="group_list_member">
			<? $person->output('avatar'); ?>
			<div class="column_5">
					<? $person->permalink(); ?>
					<? if ($person->get('tagline')) { ?><Br />
					<span class="tagline"><? $person->write('tagline');  }?></span>
			</div>
			<div class="column_3">
					<? $member_type = $group->isMember($person); ?>
					<? if ($membership == "owner" || $membership == "manager") { ?>
						<a href="#changeMemberType" data-group="<?= $group->id; ?>" data-person="<?= $person->id; ?>">
							<? echo $member_type; ?>
						</a>
					<? } else { ?>
						<? 	echo $member_type; ?>
					<? } ?>
			</div>
			<div class="column_3 last right_align">
					<? if ($membership == "owner" || $membership == "manager") { ?>						
						<a href="#removeMember"  data-group="<?= $group->id; ?>" data-person="<?= $person->id; ?>">Remove</a>
					<? } ?>
			</div>
			<div class="clearer"></div>
		</div>
	<? } ?>
