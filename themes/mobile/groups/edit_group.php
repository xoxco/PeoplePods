<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/groups/edit_group.php
* Defines the group edit page
*
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/group-object
/**********************************************/
?>

<?
	$membership = $group->isMember($group->POD->currentUser());
?>
<? if ($membership == "owner" || $membership == "manager") { ?>
	<script type="text/javascript">
	
		function setGroupToPrivate() {
			if (confirm('Are you sure you want to make this group private?  This cannot be undone!')) { 
				$('#group_type').val('private');
				$('#edit_group').submit();
				return false;
			}
		}
	
		function deleteGroupConfirm() {
			return confirm('Are you sure you want to delete this group? This cannot be undone!');
		}
	</script>
	<div class="column_8">
		<h1><? $group->permalink(); ?> &#187; Edit</h1>
		<form method="post" action="<? $group->write('permalink'); ?>/edit" id="edit_group" class="valid">
			<input type="hidden" name="id" value="<? $group->write('id'); ?>" />
			<input type="hidden" name="type" id="group_type" value="<? $group->write('type'); ?>" />			
			<p class="input"><label for="groupname">Group Name:</label><input name="groupname" class="text required" id="groupname" value="<? echo htmlspecialchars($group->get('groupname')); ?>" /></p>
			<p class="input"><label for="description">Description:</label><textarea name="description" class="text required" id="description"><? echo htmlspecialchars($group->get('description')); ?></textarea></p>

			<p class="form_text"><input type="submit" value="Update &#187;" /></p>

			<? if ($group->get('type')=="public") { ?>
				<p class="form_text">This group is public.  Anyone can see posts, and anyone can join.</p>
				
				<p class="form_text">This group can be changed to a private group, however this change cannot be undone.  <input type="submit" onclick="return setGroupToPrivate();" value="Change this group to a private group."></p>
			<? } else { ?>
				<p class="form_text">This group is private.  Only members can see posts, and new members must be invited.</p>
			<? } ?>
		</form>		
		<form method="post" action="<? $group->write('permalink'); ?>/delete" id="delete_group" onsubmit="return deleteGroupConfirm();">
		
				<p class="form_text">You can delete this group. <? if ($group->get('type')=="private") { ?>Posts from this group
				will be visible only to their author.<? } ?><input type="submit" value="Delete group" /></p>
				<input type="hidden" name="id" value="<? $group->write('id'); ?>" />
				<input type="hidden" name="confirm" value="<? echo htmlspecialchars(md5($group->POD->currentUser()->get('memberSince'))); ?>" />
		</form>		
	</div>
<? } // if is member ?>
	
