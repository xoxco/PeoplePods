<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/people/member_info.php
* Creates a little member info box
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/person-object
/**********************************************/
?>
<section id="author_info" class="padded">
	<? $user->output('avatar'); ?>
	<section class="attributed_content">
		<b>By <? $user->permalink(); ?></b>
		<? if ($user->get('location')) {
			$user->write('location');
			echo "<br />";
		} ?>
		<? if ($user->POD->isAuthenticated() && $user->POD->currentUser()->get('id') != $user->get('id')) { ?>
			<a href="#toggleFlag" data-flag="friends" data-person="<?= $user->id; ?>" data-active="Stop Following" data-inactive="Follow" class="person_output_follow_button <? if ($user->hasFlag('friends',$POD->currentUser())){?>active<? } ?>">Follow</a>
		<? } ?>
	</section>
	<div class="clearer"></div>
</section>