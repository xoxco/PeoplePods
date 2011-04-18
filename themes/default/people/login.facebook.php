
	<div id="connect_form">

			
			<h1>Connect to Facebook</h1>
		
			<? if ($user->get('facebook_token')) { ?>
				<div class="connect_details">
				
					<p>
						<strong>Facebook Name:</strong> <fb:profile-pic uid=loggedinuser facebook-logo=true></fb:profile-pic> <fb:name uid=loggedinuser useyou=false></fb:name>
						&nbsp;&nbsp;<a href="/logout?nobounce=1"  class="littleButton">Logout</a>
						&nbsp;&nbsp;<a href="/facebook?rfb=1"  class="littleButton">Remove</a>
					</p>
				</div>
			<? } else { ?>
			
				<p>When you connect, you'll be able to login with your Facebook account, and automatically post your activity to your Facebook wall.</p>
			
				<p><a href="/facebook/verify" class="bigButton">Login to Facebook</a></p>

			<? } ?>
			<? if ($POD->isAuthenticated()) { ?>
				<p class="right_align"><a href="/" class="littleButton">Continue &#187;</a></p>
			<? } else { ?>
				<p><a href="/join" class="littleButton">&larr; Return to Login</a></p>
			<? } ?>
				

				
	</div>