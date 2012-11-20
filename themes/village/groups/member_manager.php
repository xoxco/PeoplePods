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

<?php
	$membership = $group->isMember($group->POD->currentUser());
?>

	<header>
			<h1><?php $group->permalink(); ?> &#187; Members</h1>

			<?php echo $group->members()->totalCount(); ?> members | <a href="<?php $group->POD->siteRoot(); ?>/invite?group=<?php $group->write('id'); ?>">Invite Someone</a>
	</header>
	<div class="column_6">
			<B>Member</B>
	</div>
	<div class="column_2">
			<b>Type</b>
	</div>
	<div class="clearer"></div>
	<?php 
	$group->members()->sortBy('type');
	while ($person = $group->members()->getNext()) { ?>
	
		<div id="person<?php $person->write('id'); ?>" class="group_list_member">
			<?php $person->output('avatar'); ?>
			<div class="column_5">
					<?php $person->permalink(); ?>
					<?php if ($person->get('tagline')) { ?><Br />
					<span class="tagline"><?php $person->write('tagline');  }?></span>
			</div>
			<div class="column_3">
					<?php $member_type = $group->isMember($person); ?>
					<?php if ($membership == "owner" || $membership == "manager") { ?>
						<a href="#changeMemberType" data-group="<?php echo $group->id; ?>" data-person="<?php echo $person->id; ?>">
							<?php echo $member_type; ?>
						</a>
					<?php } else { ?>
						<?php 	echo $member_type; ?>
					<?php } ?>
			</div>
			<div class="column_3 last right_align">
					<?php if ($membership == "owner" || $membership == "manager") { ?>						
						<a href="#removeMember"  data-group="<?php echo $group->id; ?>" data-person="<?php echo $person->id; ?>">Remove</a>
					<?php } ?>
			</div>
			<div class="clearer"></div>
		</div>
	<?php } ?>
