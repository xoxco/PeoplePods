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
	<div class="">
	
		<div id="profile_info">		
			<h1><? $user->write('nick'); ?></h1>
			<? if ($img = $user->files()->contains('file_name','img')) { ?>
				<img src="<? $img->write('resized'); ?>" border="0" />
			<? } ?>
		</div>
            
		<div id="profile_actions">
				<? if ($user->POD->isAuthenticated()) { 
					if ($user->POD->currentUser()->get('id') != $user->get('id')) {  ?>

						<a data-role="button"data-ajax=false href="#toggleFlag" data-flag="friends" data-person="<?= $user->id; ?>" data-active="Stop Following" data-inactive="Follow" class="person_output_follow_button <? if ($user->hasFlag('friends',$POD->currentUser())){?>active<? } ?>">Follow</a>

						<? if ($user->POD->libOptions('enable_core_private_messaging')) { ?>
							<a href="<? $user->POD->siteRoot(); ?><? echo $user->POD->libOptions('messagePath') ?>/<? $user->write('stub'); ?>" data-role="button" rel="external" class="person_output_send_message_button">Send Message</a>
						<? } ?>

					<? }  ?>
				<? } else { ?>
					<div id="addFriend<? $user->write('id'); ?>"><a href="<? $user->POD->siteRoot(); ?>/join" data-role="button" rel="external"class="person_output_follow_button person_output_follow_button_start">Join up to follow <? $user->write('nick'); ?></a></div>
				<? } ?>
		</div>
		
		<div id="profile_about">	
			<? if ($user->get('aboutme')) { ?>
				<? echo $user->formatText('aboutme'); ?>
			<? } ?>
			<? if ($user->get('homepage')) { ?>
				<p><b><? $user->write('nick'); ?>'s "Real" Website:</b> <a href="<? $user->write('homepage'); ?>" rel="external"><? $user->write('homepage'); ?></a></p>
			<? } ?>

			<? if ($user->get('age')) { ?>
				<p><b>Age:</b> <? $user->write('age'); ?></p>
			<? } ?>
			<? if ($user->get('sex')) { ?>
				<p><b>Sex:</b> <? $user->write('sex'); ?></p>
			<? } ?>
			<? if ($user->get('location')) { ?>
				<p><b>Location:</b> <? $user->write('location'); ?></p>
			<? } ?>
			<? if ($user->favorites()->totalCount() > 0) { ?>
				<p><a href="<? $user->POD->siteRoot(); ?>/lists/favorites/<? $user->write('stub'); ?>"><? $user->write('nick'); ?>'s Favorites</a></p>
			<? } ?>
		</div>
			
		<div id="profile_friends">
			<h3>Following <? echo $user->friends()->totalCount(); echo $POD->pluralize($user->friends()->totalCount(),' Person',' People'); ?></h3>
			<? $user->friends()->output('short'); ?>
		</div>
	</div>
	

	<div class="last" id="profile_content">
		<? 	
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