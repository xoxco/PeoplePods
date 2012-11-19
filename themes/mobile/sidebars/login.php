<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/sidebars/login.php
* Simple login sidebar
*
* Use this in other templates:
* $POD->output('sidebars/login');
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/person-object
/**********************************************/
?>
	<div class="sidebar padded" id="sidebar_login_form">
		
		<form method="post" id="login" action="<? $POD->siteRoot(); ?>/login" class="valid">
			<p>
				<label for="email">Email:</label>
				<input class="required email text" name="email" id="email" />
			</p>
			
			<p>
				<label for="password">Password:</label>
				<input class="required text" name="password" type="password" id="password" />
			</p>
			
			<p>
				<label for="remember_me">Remember:</label>
				<input type="checkbox" name="remember_me" value="true" checked />
				<input type="submit"  value="Login" name="login" />
			</p>
			
			<p>Need an account? <a href="<? $POD->siteRoot(); ?>/join">Join this site!</a></p>

		</form>
	</div>
