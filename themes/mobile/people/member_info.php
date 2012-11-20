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
<section id="member_info" class="padded">
	<?php $user->output('avatar'); ?>
	<section class="attributed_content">
		<b><?php $user->permalink(); ?></b>
		<?php if ($user->get('location')) {
			$user->write('location');
			echo "<br />";
		} ?>
		<?php if ($user->POD->isAuthenticated() && $user->POD->currentUser()->get('id') == $user->get('id')) { ?>
			<?php if ($user->get('verificationKey')) { ?>
				<a href="<?php $user->POD->siteRoot(); ?>/verify" class="highlight" rel="external">Verify Your Account!</a>
			<?php } ?>
			<a href="<?php $user->POD->siteRoot(); ?>/editprofile" rel="external">Edit Profile</a>
		<?php } ?>
	</section>
	<div class="clearer"></div>
</section>