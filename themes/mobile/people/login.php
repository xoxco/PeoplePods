<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/people/login.php
* Used by the core_authentication pod to create the /login page
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/person-object
/**********************************************/
?>
	<div id="login_join_form">

		<h1>Sign in to <?php $POD->siteName(); ?></h1>
		
		<?php if ($POD->isEnabled('twitter_connect')) { ?>
			<a href="<?php $POD->siteRoot(); ?>/twitter/verify" id="twitter_signin" class="federated_login_link">Sign in with Twitter</a>
		<?php } ?>
		<?php if ($POD->isEnabled('fb_connect')) { ?>
			<a href="<?php $POD->siteRoot(); ?>/facebook/verify" id="facebook_signin" class="federated_login_link">Sign in with Facebook</a>
		<?php } ?>
		<?php if ($POD->isEnabled('openid_connect')) { ?>
			<a href="<?php $POD->siteRoot(); ?>/openid" id="openid_signin" class="federated_login_link">Sign in with OpenID</a>
		<?php } ?>
		
		<form method="post" id="login" action="<?php $POD->siteRoot(); ?>/login" class="valid">
			<input type="hidden" name="redirect" value="<?php echo htmlspecialchars($user->get('redirect')); ?>" />
			<p>
				<label for="email">Email:</label>
				<input class="required email text" name="email" id="email" />
			</p>
			
			<p>
				<label for="password">Password:</label>
				<input class="required text" name="password" type="password" id="password" />
			</p>
			
			<p>
                            <div data-role="fieldcontain">
                                <fieldset data-role="controlgroup">
                                    <input type="checkbox" name="remember_me" value="true" id="remember_me" class="custom" checked />
                                    <label for="remember_me">Remember Me:</label>
                                </fieldset>
                            </div>
			</p>
			
			<p>
				<label for="login">&nbsp;</label>
				<input type="submit"  value="Login" name="login" />
			</p>
			<?php if ($POD->libOptions('enable_core_authentication_creation')) { ?>
			<p class="right_align gray">Need an account? <a href="<?php $POD->siteRoot(); ?>/join">Join this site!</a></p>
			<?php } ?>
			<p class="right_align gray"><a href="<?php $POD->siteRoot(); ?>/password_reset">Forgot your password?</a></p>
			

		</form>
	</div>
