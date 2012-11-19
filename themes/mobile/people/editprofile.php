<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/people/editprofile.php
* used by core_authentication
* defines the edit profile page at /editprofile
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/person-object
/**********************************************/
?>

	<div class="contentPadding">
		
		<h1>My Account</h1>
		
		<? if ($user->get('verificationKey') != '') { ?>
			<div class="info">
				Your e-mail address is still unverified.  <a href="<? $POD->siteRoot(); ?>/verify">Click here</a> to verify yourself!
			</div>
		<? } // if unverified ?>
		
		<form id="edit_profile" method="post" action="<? $POD->siteRoot(); ?>/editprofile"  class="valid" enctype="multipart/form-data">
		
			<p class="input"><label for="nick">My Username:</label>
			<input class="required text"  maxlength="20" name="nick" id="nick" value="<? $user->htmlspecialwrite('nick'); ?>"></p>	

			<p class="input"><label for="email">My Email:</label>
			<input class="required email text" name="email" id="email" value="<? $user->htmlspecialwrite('email'); ?>"></p>
	
			<p class="input"><label for="photo">My Picture:</label>
			<input name="img" type="file" id="img">
				<? if ($img = $user->files()->contains('file_name','img')) { ?>
					<div id="file<?= $img->id; ?>" class="file">
						<a href="<?= $img->original_file; ?>"><img src="<? $img->write('thumbnail'); ?>" border="0" /></a>
						<a href="#deleteFile" data-ajax=false data-file="<?= $img->id;?>">Delete</a>
					</div>
				<? } ?>
			</p>
	
			<!-- These are meta fields.  They don't exist in the real user table, but the values will show up as if they did! -->
			<p class="input"><label for="aboutme">About Me:</label>
			<textarea name="aboutme" class="text" id="aboutme" wrap="virtual"><? $user->htmlspecialwrite('aboutme'); ?></textarea></p>
				
			<p class="input"><label for="tagline">My Page Title:</label>
			<input class="text" name="tagline" id="tagline" value="<? $user->htmlspecialwrite('tagline'); ?>" /></p>

			<p class="input"><label for="age">Age:</label>
			<input class="text" name="age" id="age" length="5" maxlength="5" value="<? $user->htmlspecialwrite('age'); ?>" /></p>

			<p class="input"><label for="sex">Sex:</label>
			<input class="text" name="sex" id="sex" maxlength="20" value="<? $user->htmlspecialwrite('sex'); ?>" /></p>

			<p class="input"><label for="location">Location:</label>
			<input class="text" name="location" maxlength="100" id="location" value="<? $user->htmlspecialwrite('location'); ?>" /></p>
		
			<p class="input"><label for="homepage">My Homepage:</label>
			<input class="url text" name="homepage" id="homepage" value="<? $user->htmlspecialwrite('homepage'); ?>" /></p>

			<!-- end meta fields -->		
		
			<p class="input"><label>&nbsp;</label><input type="submit" class="button" value="Update my account" /></p>
		</form>
		

		<hr noshade />

		<form id="change_password" method="post" action="<? $POD->siteRoot(); ?>/editprofile" class="valid">

			<h3>Change My Password</h3>
		
			<p class="input"><label for="password">New Pass:</label><input name="password" id="password" type="password" class="text required" /></p>
		
			<p class="input"><label>&nbsp;</label><input class="button" type="submit" value="Set New Password" /></p>	
	
		</form>
	</div>
