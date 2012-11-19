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
/**********************************************/

$needs_password= true;
?>

	<div id="login_join_form">			
		<form method="post" id="join" action="<? $POD->siteRoot() ?>/join" class="valid">

			<input type="hidden" name="redirect" value="<? echo htmlspecialchars($user->get('redirect')); ?>" />
			<input type="hidden" name="code" value="<? echo htmlspecialchars($user->get('code')); ?>" />			
			<h1>Welcome to <? $POD->siteName(); ?>!</h1>

			<? if ($POD->isEnabled('twitter_connect')) { ?>
				<a href="<? $POD->siteRoot(); ?>/twitter/verify" id="twitter_signin" class="federated_login_link">Sign in with Twitter</a>
			<? } ?>
			<? if ($POD->isEnabled('fb_connect')) { ?>
				<a href="<? $POD->siteRoot(); ?>/facebook/verify" id="facebook_signin" class="federated_login_link">Sign in with Facebook</a>
			<? } ?>
			<? if ($POD->isEnabled('openid_connect')) { ?>
				<a href="<? $POD->siteRoot(); ?>/openid" id="openid_signin" class="federated_login_link">Sign in with OpenID</a>
			<? } ?>
	
			<? if ($user->get('invited_by')) { ?>
				<div class="info">
					<P>You were invited to join this site by <? $user->get('invited_by')->permalink(); ?>.
				
					<? if ($user->get('invited_to_group')) { ?>
						<p><? $user->get('invited_by')->write('nick'); ?> wants you to join the group <? $user->get('invited_to_group')->permalink(); ?>.</p>	
					<? } ?>
				</div>
			<? } ?>

			<p>
				<label for="name">Your Name:</label>
				<input class="required text" name="name" value="<? $user->htmlspecialwrite('nick'); ?>" maxlength="20"/>
			</p>
			
			<p>
				<label for="email">Your Email:</label>
				<input class="required email text" value="<? $user->htmlspecialwrite('email'); ?>" name="email" />
			</p>
			
			
			<? if ($user->get('fbuid')) { $needs_password = false; ?>
				<p><label>Facebook:</label> Connected!</p>
				<input type="hidden" name="meta_fbuid" value="<?= $user->write('fbuid'); ?>" />
			<? } ?>
			<? if ($user->get('twitter_name')) { $needs_password = false; ?>
				<p><label>Twitter:</label> <a href="http://twitter.com/<? $user->write('twitter_name'); ?>"><? $user->write('twitter_name'); ?></a> Connected!</p>
				<input type="hidden" name="meta_twitter_token" value="<?= $user->write('twitter_token'); ?>" />
				<input type="hidden" name="meta_twitter_secret" value="<?= $user->write('twitter_secret'); ?>" />
				<input type="hidden" name="meta_twitter_name" value="<?= $user->write('twitter_name'); ?>" />
				<input type="hidden" name="meta_twitter_id" value="<?= $user->write('twitter_id'); ?>" />

			<? } ?>

			<? if ($user->get('openid')) { $needs_password = false;?>
				<p>
					<label for="password">OpenID:</label>&nbsp;<?= $user->write('openid'); ?>
					<input type="hidden" name="meta_openid" value="<?= $user->write('openid'); ?>"/>
				</p>	
			<? } ?>
	
			
			<? if ($needs_password){?>
			
			<p>
				<label for="password">Choose a Password:</label>
				<input class="required text" name="password" type="password" />
			</p>
			<? } ?>
	
			<p>
				<label for="create">&nbsp;</label><input type="submit"  class="button" value="Create my account" name="create" />
			</p>
		
			<p class="form_text">By clicking "Create" you agree to our <a href="<? $POD->siteRoot(); ?>/tos">Terms of Service</a></p>
		</form>
		<p>Already have an account?  <a href="<? $POD->siteRoot(); ?>/login" rel="external" data-role="button">Login here</a></p>

	</div>