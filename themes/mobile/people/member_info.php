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
	<? $user->output('avatar'); ?>
	<section class="attributed_content">
		<b><? $user->permalink(); ?></b>
		<? if ($user->get('location')) {
			$user->write('location');
			echo "<br />";
		} ?>
		<? if ($user->POD->isAuthenticated() && $user->POD->currentUser()->get('id') == $user->get('id')) { ?>
			<? if ($user->get('verificationKey')) { ?>
				<a href="<? $user->POD->siteRoot(); ?>/verify" class="highlight" rel="external">Verify Your Account!</a>
			<? } ?>
			<a href="<? $user->POD->siteRoot(); ?>/editprofile" rel="external">Edit Profile</a>
		<? } ?>
	</section>
	<div class="clearer"></div>
</section>