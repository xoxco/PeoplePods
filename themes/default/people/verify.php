<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/people/verify.php
* Used by the core_authentication pod to create the /verify page
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/person-object
/**********************************************/
?>
<div id="login_join_form">
	<h1>Verify Your Account</h1>
	
	<? if ($user->get('verify_status')=='key_resent') { ?>
	
		<div class="info">
			Your verification key has been resent!  If you don't see it within a minute or so, check your spam folder!
		</div>
	
	<? } // if resent ?>
	
	<? if ($POD->currentUser()->get('verificationKey')=='') { ?>
	
		<div class="info">
			<h3>Your account has been verified!</h3>
			<p><a href="<? $POD->siteRoot(); ?>">Continue &#187;</a></p>
		</div>
	
	<? } else  { ?>
	
		<form method="get" id="verify" action="<? $POD->siteRoot(); ?>/verify" class="valid">
		
			<p>Enter the code that we sent to <? $POD->currentUser()->write('email') ?></p>
			
			<p>
				<label>Code:</label>
				<input class="required text" name="key">
			</p>
			
			<p>
				<label>&nbsp;</label>
				<input type="submit" name="validate" value="Verify Me" class="button" />
			</p>

		</form>		
		<p>If you can't find your key, <a href="<? $POD->siteRoot(); ?>/verify?resend=1">click here to have it resent.</a></p>
	
	<? } ?>
</div>
</div>
