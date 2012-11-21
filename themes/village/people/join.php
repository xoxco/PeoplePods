<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/people/join.php
* Used by the core_authentication pod to create the /join page
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/person-object
 * tore out this code as village will not need it
 * 			<?php if ($POD->isEnabled('twitter_connect')) { ?>
				<a href="<?php $POD->siteRoot(); ?>/twitter/verify" id="twitter_signin" class="federated_login_link">Sign in with Twitter</a>
			<?php } ?>
			<?php if ($POD->isEnabled('fb_connect')) { ?>
				<a href="<?php $POD->siteRoot(); ?>/facebook/verify" id="facebook_signin" class="federated_login_link">Sign in with Facebook</a>
			<?php } ?>
			<?php if ($POD->isEnabled('openid_connect')) { ?>
				<a href="<?php $POD->siteRoot(); ?>/openid" id="openid_signin" class="federated_login_link">Sign in with OpenID</a>
			<?php } ?>
 * 			<?php if ($user->get('fbuid')) { $needs_password = false; ?>
				<p><label>Facebook:</label> Connected!</p>
				<input type="hidden" name="meta_fbuid" value="<?php echo $user->write('fbuid'); ?>" />
			<?php } ?>
			<?php if ($user->get('twitter_name')) { $needs_password = false; ?>
				<p><label>Twitter:</label> <a href="http://twitter.com/<?php $user->write('twitter_name'); ?>"><?php $user->write('twitter_name'); ?></a> Connected!</p>
				<input type="hidden" name="meta_twitter_token" value="<?php echo $user->write('twitter_token'); ?>" />
				<input type="hidden" name="meta_twitter_secret" value="<?php echo $user->write('twitter_secret'); ?>" />
				<input type="hidden" name="meta_twitter_name" value="<?php echo $user->write('twitter_name'); ?>" />
				<input type="hidden" name="meta_twitter_id" value="<?php echo $user->write('twitter_id'); ?>" />

			<?php } ?>

			<?php if ($user->get('openid')) { $needs_password = false;?>
				<p>
					<label for="password">OpenID:</label>&nbsp;<?php echo $user->write('openid'); ?>
					<input type="hidden" name="meta_openid" value="<?php echo $user->write('openid'); ?>"/>
				</p>	
			<?php } ?>
/**********************************************/

?>

	<div id="login_join_form">			
		<form method="post" id="join" action="<?php $POD->siteRoot() ?>/join" class="valid">

			<input type="hidden" name="redirect" value="<?php echo htmlspecialchars($user->get('redirect')); ?>" />
			<input type="hidden" name="code" value="<?php echo htmlspecialchars($user->get('code')); ?>" />			
			<h1>Welcome to <?php $POD->siteName(); ?>!</h1>
	
			<?php if ($user->get('invited_by')) { ?>
				<div class="info">
					<p>You were invited to join this site by <?php $user->get('invited_by')->permalink(); ?>.</p>
				
					<?php if ($user->get('invited_to_group')) { ?>
						<p><?php $user->get('invited_by')->write('nick'); ?> wants you to join the group <?php $user->get('invited_to_group')->permalink(); ?>.</p>	
					<?php } ?>
				</div>
			<?php } ?>

			<p>
				<label for="name">Your Name:</label>
				<input class="required text" id="name" name="name" value="<?php $user->htmlspecialwrite('nick'); ?>" maxlength="20"/>
			</p>
			
			<p>
				<label for="email">Your Email:</label>
				<input class="required email text" id="email" value="<?php $user->htmlspecialwrite('email'); ?>" name="email" />
			</p>
			<p>
				<label for="meta_phone">Your Phone number:</label>
				<input class="required phone text" id="email" value="only enter numbers, please" name="meta_phone" />
			</p>
			<p>
				<label for="meta_role">Your role:</label>
				<select name="meta_role" type="radio">
					<option value="healer">healer (doctors, social workers, or others charged with the responsible care of patients and legal licensing to do so)</option>
					<option value="patient">patient</option>
					<option value="familyOrFriend">family or freind of patient</option>
				</select>
			</p>
			
			<p>
				<label for="password">Choose a Password:</label>
				<input class="required text" id="password" name="password" type="password" />
			</p>
			
	
			<p>
				<label for="create">&nbsp;</label><input type="submit"  id="create" class="button" value="Create my account" name="create" />
			</p>
		
			<p class="form_text">By clicking "Create" you agree to our <a href="<?php $POD->siteRoot(); ?>/tos">Terms of Service</a></p>
		</form>
		<p class="right_align gray">Already have an account?  <a href="<?php $POD->siteRoot(); ?>/login">Login here</a></p>

	</div>