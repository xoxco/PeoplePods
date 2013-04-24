<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/groups/invite.php
* Used to present group invite form.
*
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/group-object
/**********************************************/
?>
	<form method="post" action="<? $POD->siteRoot(); ?>/invite" class="valid" id="invite">
		
	
	<div class="column_6">
	
				<h1>Send Invites</h1>

			<p class="normal_input"><label for="email1">Email:</label><input name="email1" id="email1" class="text required validate-email" /></p>
			<p class="normal_input"><label for="email2">Email:</label><input name="email2" id="email2" class="text  validate-email"  /></p>
			<p class="normal_input"><label for="email3">Email:</label><input name="email3" id="email3" class="text  validate-email"  /></p>
			<p class="normal_input"><label for="email4">Email:</label><input name="email4" id="email4" class="text  validate-email"  /></p>
			<p class="normal_input"><label for="email5">Email:</label><input name="email5" id="email5" class="text  validate-email"  /></p>

		
	</div>
	<div class="column_6">
		<?	if (isset($_GET['group'])) { 
					$group = $POD->getGroup(array('id'=>$_GET['group']));
					if ($group->success() && $group->isMember($POD->currentUser())) {	?>
						<div class="info">
							Your email will include an invitation to join <? $group->permalink(); ?>.
						</div>
						<input type="hidden" name="group" value="<? $group->write('id'); ?>" />
					<?	} // if group loaded
				} // if group id passed			
			?>

		<label for="message">Include a personal message</label>
		<p class="normal_input"><textarea name="message" id="message" class="text required"></textarea></p>
		
		<p class="input"><input type="submit" value="Send Invites &#187;" /></p>
	</div>

	</form>	