<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/sidebars/create_group.php
* Sidebar box to create a group
* Used in core_groups/index.php
*
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/person-object
/**********************************************/
?>

			<div id="create_group_sidebar" class="sidebar padded">
				<a href="#" onclick="$('#create_group').show(); $(this).hide();; return false;">Create a Group</a>
	
				<form method="post" id="create_group" class="valid" style="display: none;">
					<p>
						<label for="groupname">What do you want to call your group?</label>
						<input type="text" class="text required" name="groupname" id="groupname" />
					</p>		
					<p>
						<label for="description">What is the purpose of this group?</label>
						<textarea type="text" class="text required" name="description" id="description"></textarea>
					</p>		
					<p>
						<label>Group Type</label><br />
						<input type="radio" name="type" value="public" checked /> <b>Public</b> - Anyone can join<br />
					   <input type="radio" name="type" value="private" /> <b>Private</b> - Invite only
					</p>
					<p>
						<input type="submit" value="Start this group &#187;" />
					</p>
				</form>	
				
			</div>
