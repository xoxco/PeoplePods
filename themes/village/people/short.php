<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/people/short.php
* Default tempalte for short output of person object
* Used to create lists of people
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/person-object
/**********************************************/
?>
<li class="person_short">

	<a href="<?php $user->write('permalink'); ?>"><img src="<?php echo $user->avatar(); ?>"  alt="<?php $user->htmlspecialwrite('nick'); ?>" align="absmiddle" /></a>

	<?php $user->permalink(); ?>
	<?php if ($POD->isAuthenticated()) { ?>
		<a href="#toggleFlag" data-flag="friends" data-person="<?php echo $user->id; ?>" data-active="Stop Following" data-inactive="Follow" class="person_short_follow_button <?php if ($user->hasFlag('friends',$POD->currentUser())){?>active<?php } ?>">Follow</a>
	<?php } ?>

</li>
