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
		
		<?php if ($user->get('verificationKey') != '') { ?>
			<div class="info">
				Your e-mail address is still unverified.  <a href="<?php $POD->siteRoot(); ?>/verify">Click here</a> to verify yourself!
			</div>
		<?php } // if unverified ?>
		
		<form id="edit_profile" method="post" action="<?php $POD->siteRoot(); ?>/editprofile"  class="valid" enctype="multipart/form-data">
          <label for="nick">My Username:</label>
          <input maxlength="20" name="nick" id="nick" value="<?php $user->htmlspecialwrite('nick'); ?>">
          <label for="email">My Email:</label>
          <input name="email" id="email" value="<?php $user->htmlspecialwrite('email'); ?>">
          <label for="photo">My Picture:</label>
          <input name="img" type="file" id="img">
          <?php if ($img = $user->files()->contains('file_name','img')) { ?>
<div id="file<?php echo $img->id; ?>" class="file">
				  <a href="<?php echo $img->original_file; ?>"><img src="<?php $img->write('thumbnail'); ?>" /></a>
				  <a href="#deleteFile" data-file="<?php echo $img->id;?>">Delete</a>
			  </div>
				<?php } ?>
			</p>
	
			<!-- These are meta fields.  They don't exist in the real user table, but the values will show up as if they did! -->
			<label for="aboutme">About Me:</label>
          <textarea name="aboutme" id="aboutme" wrap="virtual"><?php $user->htmlspecialwrite('aboutme'); ?>
            </textarea>
            <label for="tagline">My Page Title:</label>
            <input name="tagline" id="tagline" value="<?php $user->htmlspecialwrite('tagline'); ?>" />
            <label for="age">Age:</label>
            <input name="age" id="age" length="5" maxlength="5" value="<?php $user->htmlspecialwrite('age'); ?>" />
            <label for="sex">Sex:</label>
            <input name="sex" id="sex" maxlength="20" value="<?php $user->htmlspecialwrite('sex'); ?>" />
            <label for="location">Location:</label>
            <input name="location" maxlength="100" id="location" value="<?php $user->htmlspecialwrite('location'); ?>" />
            <label for="homepage">My Homepage:</label>
            <input name="homepage" id="homepage" value="<?php $user->htmlspecialwrite('homepage'); ?>" />
            <hr />
            
			<h3>My role in the network:</h3>
            <h4><?php $user->htmlspecialwrite( 'role' ); ?></h4>
            
            <hr />
          <label>My Phone number:</label>
            <input class="url text" name="meta_phone" value="<?php $user->htmlspecialwrite('phone'); ?>" />
          <label>My chosen safeword:</label>
            <input name="safeword" value="<?php $user->htmlspecialwrite('safeword'); ?>" />
            <?php if( $user->role == 'healer' ){ ?>
	      <label>My licensing allows me to:</label>
            <input name="license_type"  value="<?php $user->htmlspecialwrite('license_type'); ?>" />
          <label>My license identification number is:</label>
            <input name="license" value="<?php $user->htmlspecialwrite('license'); ?>" />
          <label>My state of licensing is:</label>
            <input name="state_issuer_of_license" value="<?php $user->htmlspecialwrite('state_issuer_of_license'); ?>" />
            <?php } ?>
		  <!-- end meta fields -->		
		
		  <label>&nbsp;</label>
		  <button type="submit" class="btn"  >Update my account</button>
		</form>
		

		<hr noshade />

		<form id="change_password" method="post" action="<?php $POD->siteRoot(); ?>/editprofile" class="valid">

			<h3>Change My Password</h3>
		
			<label for="password">New Pass:</label><input name="password" id="password" type="password" class="text required" />
		
			<label>&nbsp;</label>
			<input class="button" type="submit" value="Set New Password" />
		</form>
	</div>
