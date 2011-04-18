<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/groups/short.php
* Default short output template for group objects
* Used in lists of groups
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/group-object
/**********************************************/
?>
<li class="group">
	<header class="group_name">
		<h1><? $group->permalink(); ?></h1>
	</header>
	<article class="group_description">
			<? $group->writeFormatted('description'); ?>	
	</article>
	<aside class="group_member_count">
		<?= $POD->pluralize($group->members()->totalCount(),'@number member','@number members'); ?>
			<a href="<?= $group->permalink; ?>/join" class="joinGroup <?= $group->isMember($POD->currentUser()); ?>" data-default="Join" data-group="<?= $group->id; ?>" <? if ($POD->isAuthenticated()) { ?>data-person="<?= $POD->currentUser()->id; ?>"<? } ?> data-invitee="You're invited. Join now." data-applicant="You've applied for membership."  data-member="Quit Group" data-owner="You started this group!" data-manager="You're a group manager.">Join</a>
	</aside>
	<div class="clearer"></div>
</li>