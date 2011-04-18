<? 
	include_once("../../PeoplePods.php");	
	$POD = new PeoplePod(array('lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));

	if ($_POST) {
	

		
		$POD->setLibOptions('friendEmail',@$_POST['friendEmail']);
		$POD->setLibOptions('friendAlert',@$_POST['friendAlert']);
		$POD->setLibOptions('friendActivity',@$_POST['friendActivity']);
		
		$POD->setLibOptions('friendActivity',@$_POST['friendActivity']);
		$POD->setLibOptions('friendActivity',@$_POST['friendActivity']);
		

		$POD->setLibOptions('contentCommentAlert',@$_POST['contentCommentAlert']);
		$POD->setLibOptions('contentCommentActivity',@$_POST['contentCommentActivity']);

		$POD->setLibOptions('profileCommentAlert',@$_POST['profileCommentAlert']);
		$POD->setLibOptions('profileCommentActivity',@$_POST['profileCommentActivity']);

		$POD->setLibOptions('alertEmail',@$_POST['alertEmail']);

		$POD->setLibOptions('contactEmail',@$_POST['contactEmail']);


		$POD->saveLibOptions();
		if ($POD->success()) { 
				$message = "Config updated.";
		} else {
			$message = $POD->error();
		}

	}


	$POD->changeTheme('admin');
	$POD->header();
	$current_tab="notifications";

?>
<? include_once("option_nav.php"); ?>

	<? if (isset($message)) { ?>
		<div class="info">
		
			<? echo $message ?>
			
		</div>
	
	<? } ?>		
<div class="panel">

	<h1>Automatic Notifications</h1>
	
	<p>When certain events occur, PeoplePods can send a variety of automatic notifications.</p>

	<form method="post" class="valid">
	<input type="hidden" value="save" name="save" />
	<table cellspacing="0" cellpadding="0" class="stack_output">
		<tr>
			<th align="left">
				Event
			</th>
			<th align="left">
				Description
			</th>
			<th align="left">
				Notification Type
			</th>
			<th align="right">
				<input type="checkbox" onchange="selectAll(this);" />
			</th>
		</tr>
		
		<!-- ---------------------------------------------------------------------------------------------------------- -->
		
		<tr>
			<td valign="top" align="left">
				<strong>Add Friend</strong>
			</td>
			<td valign="top" align="left">
				Member adds another member as a friend	
			</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td valign="top" align="left">
				&nbsp;
			</td>
			<td valign="top" align="left">
				&nbsp;
			</td>
			<td valign="top" align="left">
				Send Email
			</td>
			<td valign="top" align="right">
				<input type="checkbox" class="enabler"  name="friendEmail" value="friendEmail" <? if ($POD->libOptions('friendEmail')) { ?>checked<? } ?> />
			</td>
		</tr>
		<tr>
			<td valign="top" align="left">
				&nbsp;
			</td>
			<td valign="top" align="left">
				&nbsp;
			</td>
			<td valign="top" align="left">
				Send Alert
			</td>
			<td valign="top" align="right">
				<input type="checkbox" class="enabler"  name="friendAlert" value="friendAlert" <? if ($POD->libOptions('friendAlert')) { ?>checked<? } ?> />
			</td>
		</tr>
		<tr>
			<td valign="top" align="left">
				&nbsp;
			</td>
			<td valign="top" align="left">
				&nbsp;
			</td>
			<td valign="top" align="left">
				Publish Activity
			</td>
			<td valign="top" align="right">
				<input type="checkbox" class="enabler"  name="friendActivity" value="friendActivity" <? if ($POD->libOptions('friendActivity')) { ?>checked<? } ?> />
			</td>
		</tr>

		<!-- ---------------------------------------------------------------------------------------------------------- -->
		
		<tr>
			<td valign="top" align="left">
				<strong>Post comment on content</strong>
			</td>
			<td valign="top" align="left">
				Member adds a comment to a piece of content that is owned by another user
			</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td valign="top" align="left">
				&nbsp;
			</td>
			<td valign="top" align="left">
				&nbsp;
			</td>
			<td valign="top" align="left">
				Send Alert
			</td>
			<td valign="top" align="right">
				<input type="checkbox" class="enabler"  name="contentCommentAlert" value="contentCommentAlert" <? if ($POD->libOptions('contentCommentAlert')) { ?>checked<? } ?> />
			</td>
		</tr>
		<tr>
			<td valign="top" align="left">
				&nbsp;
			</td>
			<td valign="top" align="left">
				&nbsp;
			</td>
			<td valign="top" align="left">
				Publish Activity
			</td>
			<td valign="top" align="right">
				<input type="checkbox" class="enabler"  name="contentCommentActivity" value="contentCommentActivity" <? if ($POD->libOptions('contentCommentActivity')) { ?>checked<? } ?> />
			</td>
		</tr>

		<!-- ---------------------------------------------------------------------------------------------------------- -->
		
		<tr>
			<td valign="top" align="left">
				<strong>Post comment on a profile</strong>
			</td>
			<td valign="top" align="left">
				Member adds a comment to the profile of another user
			</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td valign="top" align="left">
				&nbsp;
			</td>
			<td valign="top" align="left">
				&nbsp;
			</td>
			<td valign="top" align="left">
				Send Alert
			</td>
			<td valign="top" align="right">
				<input type="checkbox" class="enabler"  name="profileCommentAlert" value="profileCommentAlert" <? if ($POD->libOptions('profileCommentAlert')) { ?>checked<? } ?> />
			</td>
		</tr>
		<tr>
			<td valign="top" align="left">
				&nbsp;
			</td>
			<td valign="top" align="left">
				&nbsp;
			</td>
			<td valign="top" align="left">
				Publish Activity
			</td>
			<td valign="top" align="right">
				<input type="checkbox" class="enabler"  name="profileCommentActivity" value="profileCommentActivity" <? if ($POD->libOptions('profileCommentActivity')) { ?>checked<? } ?> />
			</td>
		</tr>

		<!-- ---------------------------------------------------------------------------------------------------------- -->
		
		<tr>
			<td valign="top" align="left">
				<strong>Send Alert</strong>
			</td>
			<td valign="top" align="left">
				One of the above alerts, or any custom alert is sent to a user
			</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>

		<tr>
			<td valign="top" align="left">
				&nbsp;
			</td>
			<td valign="top" align="left">
				&nbsp;
			</td>
			<td valign="top" align="left">
				Send Email
			</td>
			<td valign="top" align="right">
				<input type="checkbox" class="enabler"  name="alertEmail" value="alertEmail" <? if ($POD->libOptions('alertEmail')) { ?>checked<? } ?> />
			</td>
		</tr>
		
		<!-- ---------------------------------------------------------------------------------------------------------- -->
		
		<tr>
			<td valign="top" align="left">
				<strong>Private Message</strong>
			</td>
			<td valign="top" align="left">
				Member sends a private message
			</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td valign="top" align="left">
				&nbsp;
			</td>
			<td valign="top" align="left">
				&nbsp;
			</td>
			<td valign="top" align="left">
				Send Email
			</td>
			<td valign="top" align="right">
				<input type="checkbox" class="enabler"  name="contactEmail" value="contactEmail" <? if ($POD->libOptions('contactEmail')) { ?>checked<? } ?> />			</td>
		</tr>
		</table>
		<p><input type="submit" value="Update Options" class="button" /></p>
	</form>
</div>
<? $POD->footer(); ?>