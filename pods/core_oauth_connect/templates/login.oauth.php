<? 
    if (strlen($user->get('err_msg')) > 0) { ?>
    <div class="info">
	<? echo $user->write('err_msg'); ?>		
    </div>
    <div id="connect_form">
      <h1> The URL is not well formed, or the service is not implemented.</h1>
      </p> An example of a correctly formed URL: /common_oauth/Twitter</p>
    </div>
<? } 
else
{ ?>
    <div id="connect_form">
	<? if ($user->get('connected')) { ?>
	    <h1>Currently connected to <? $user->write('tmp_oauth_app'); ?></h1>
	    <div class="connect_details">
		<p>
		    <a href="/common_oauth/<?$user->write('tmp_oauth_app')?>/remove"  class="littleButton">Remove this connection.</a>
		</p>
	    </div>
	<? } else { ?>
	    <h1>Connect to <? $user->write('tmp_oauth_app') ?> </h1>
	    <p>Connect to your <? $user->get('tmp_oauth_app') ?> account (using oAuth).</p>
	    <a href="/common_oauth/<?$user->write('tmp_oauth_app')?>/verify" class="bigButton">Login to <? $user->write('tmp_oauth_app') ?></a>
	<? } ?>
	<? if ($POD->isAuthenticated()) { ?>
	    <p class="right_align"><a href="/" class="littleButton">Continue &#187;</a></p>
	<? } else { ?>
	    <p><a href="/join" class="littleButton">&larr; Return to Login</a></p>
	<? } ?>			
    </div>
<? } ?>			