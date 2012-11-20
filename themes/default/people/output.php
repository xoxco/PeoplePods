<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/people/output.php
* Default output template for a person object. 
* Defines what a user profile looks like
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/person-object
/**********************************************/
?>
	<div class="column_4">
	
		<div id="profile_info">		
			<h1><?php $user->write('nick'); ?></h1>
			<?php if ($img = $user->files()->contains('file_name','img')) { ?>
				<img src="<?php $img->write('resized'); ?>"  />
			<?php } ?>
		</div>
		
		<div id="profile_actions">
				<?php if ($user->POD->isAuthenticated()) { 
					if ($user->POD->currentUser()->get('id') != $user->get('id')) {  ?>

						<a href="#toggleFlag" data-flag="friends" data-person="<?php echo $user->id; ?>" data-active="Stop Following" data-inactive="Follow" class="person_output_follow_button <?php if ($user->hasFlag('friends',$POD->currentUser())){?>active<?php } ?>">Follow</a>
						<?php if ($user->POD->libOptions('enable_core_private_messaging')) { ?>
							<a href="<?php $user->POD->siteRoot(); ?><?php echo $user->POD->libOptions('messagePath') ?>/<?php $user->write('stub'); ?>" class="person_output_send_message_button">Send Message</a>
						<?php } ?>

					<?php } else { ?>
						<a href="<?php $user->POD->siteRoot(); ?>/editprofile" title="Edit My Profile" class="person_output_edit_profile_button">Edit My Profile</a>
					<?php } ?>
				<?php } else { ?>
					<div id="addFriend<?php $user->write('id'); ?>"><a href="<?php $user->POD->siteRoot(); ?>/join" class="person_output_follow_button person_output_follow_button_start">Join up to follow <?php $user->write('nick'); ?></a></div>
				<?php } ?>
		</div>
		
		<div id="profile_about">	
			<?php if ($user->get('aboutme')) { ?>
				<?php echo $user->formatText('aboutme'); ?>
			<?php } ?>
			<?php if ($user->get('homepage')) { ?>
				<p><b><?php $user->write('nick'); ?>'s "Real" Website:</b> <a href="<?php $user->write('homepage'); ?>"><?php $user->write('homepage'); ?></a></p>
			<?php } ?>

			<?php if ($user->get('age')) { ?>
				<p><b>Age:</b> <?php $user->write('age'); ?></p>
			<?php } ?>
			<?php if ($user->get('sex')) { ?>
				<p><b>Sex:</b> <?php $user->write('sex'); ?></p>
			<?php } ?>
			<?php if ($user->get('location')) { ?>
				<p><b>Location:</b> <?php $user->write('location'); ?></p>
			<?php } ?>
			<?php if ($user->favorites()->totalCount() > 0) { ?>
				<p><a href="<?php $user->POD->siteRoot(); ?>/lists/favorites/<?php $user->write('stub'); ?>"><?php $user->write('nick'); ?>'s Favorites</a></p>
			<?php } ?>
		</div>
			
		<div id="profile_friends">
			<h3>Following <?php echo $user->friends()->totalCount(); echo $POD->pluralize($user->friends()->totalCount(),' Person',' People'); ?></h3>
			<?php $user->friends()->output('short'); ?>
		</div>
	</div>
	

	<div class="column_8 last" id="profile_content">
		<?php 	
			$offset = 0;
			if (isset($_GET['offset'])) {
				$offset = $_GET['offset'];
			}
			$docs = $user->POD->getContents(array('userId'=>$user->get('id')),null,20,$offset); 
			if ($user->get('tagline')) { 
				$tagline = $user->get('tagline');
			} else {
				$tagline = $user->get('nick') . "'s Posts";
			}
			$docs->output('short','header','pager',$tagline,$user->get('nick') . " hasn't posted anything yet.");
		?>	
	</div>	