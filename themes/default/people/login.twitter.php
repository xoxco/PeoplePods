	<div id="connect_form">

			
			<h1>Connect to Twitter</h1>
			
			<?php if ($user->get('twitter_name')) { ?>
				<div class="connect_details">
						<p><strong>Twitter Name:</strong> <a href="http://twitter.com/<?php $user->write('twitter_name'); ?>"><?php $user->write('twitter_name'); ?></a>&nbsp;&nbsp;<a href="/twitter?remove=1" class="littleButton">Remove</a></p>
				</div>
			<?php } else { ?>
			
			<p>When you connect, you'll be able to login with your Twitter account, and automatically post your activity to your Twitter stream.</p>
			
			<a href="/twitter/verify" class="bigButton">Login to Twitter</a>
			
			<?php } ?>

			<?php if ($POD->isAuthenticated()) { ?>
				<p class="right_align"><a href="/" class="littleButton">Continue &#187;</a></p>
			<?php } else { ?>
				<p><a href="/join" class="littleButton">&larr; Return to Login</a></p>
			<?php } ?>
				
	</div>