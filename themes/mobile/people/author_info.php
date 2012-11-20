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
	<?php $user->output('avatar'); ?>
	<section class="attributed_content">
		<b>By <?php $user->permalink(); ?></b>
		<?php if ($user->get('location')) {
			$user->write('location');
			echo "<br />";
		} ?>
		<?php if ($user->POD->isAuthenticated() && $user->POD->currentUser()->get('id') != $user->get('id')) { ?>
			<a href="#toggleFlag" data-flag="friends" data-person="<?php echo $user->id; ?>" data-active="Stop Following" data-inactive="Follow" class="person_output_follow_button <?php if ($user->hasFlag('friends',$POD->currentUser())){?>active<?php } ?>">Follow</a>
		<?php } ?>
	</section>
	<div class="clearer"></div>
</section>