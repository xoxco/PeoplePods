<?php if ($group->saved()) { ?>
	<script>
		current_group = <?php echo $group->id; ?>;
	</script>
<?php } ?>

<form method="post" action="<?php $group->POD->podRoot(); ?>/admin/groups/" enctype="multipart/form-data" id="update_form" class="valid">
	<div id="options">
		<div class="option_set">
			<input type="submit" class="button" name="save" value="Save"/>		

			<p class="input"><label for="type">Type:</label><select name="type"><option value="public" <?php if ($group->get('type')=='public'){?>selected<?php } ?>>Public</option><option value="private" <?php if ($group->get('type')=='private'){?>selected<?php } ?>>Private</option></select></p>
			<p class="input"><label for="stub">Stub:</label><input name="stub" class="text" value="<?php $group->write('stub'); ?>" /><br />
				<span class="field_explain">Used to generate permalink</span>
			</p>
			<p class="input"><label for="date">Date:</label><input id="date" name="date" class="text" value="<?php $group->write('date'); ?>" /></p>
		</div>
		<?php if ($group->saved()) { ?>
		<div class="option_set">
			<b>View: <a href="<?php $group->write('permalink'); ?>"><?php $group->write('groupname'); ?></a></b>
					
			<P>Created by: <a href="<?php $group->POD->podRoot(); ?>/admin/people/?id=<?php $group->owner()->write('id'); ?>"><?php $group->owner()->write('nick'); ?></a> on <?php $group->write('date'); ?>.</p>

		</div>
		<div class="option_set">
			<a href="<?php $group->POD->podRoot(); ?>/admin/groups/?id=<?php $group->write('id'); ?>&action=delete" class="tool" onclick="return confirm('Are you sure you want to delete this group?');">Delete</a>
		</div>
		<?php } ?>
	</div>

	<div class="panel panel_with_options">
	
		<h1><?php if ($group->saved()) { ?>
			<ul class="attachments">
			<li><a href="#members" ><?php echo $group->members()->totalCount(); ?>  <?php echo $group->POD->pluralize($group->members()->totalCount(),'member','members'); ?></a></li>
			<li><a href="#content"><?php echo $group->content()->totalCount(); ?> <?php echo $group->POD->pluralize($group->content()->totalCount(),'post','posts'); ?></a></li>
			<li><a href="<?php $group->POD->podRoot(); ?>/admin/files/index.php?groupId=<?php $group->write('id'); ?>" class="tool"><?php echo $group->files()->totalCount(); ?> <?php echo $this->POD->pluralize($group->files()->totalCount(),'File','Files'); ?></a></li>

			</ul>
			Edit <?php echo $group->groupname; ?> 
			
			<?php } else { ?>
				Add Group
			<?php } ?>
		</h1>
			
		<input type="hidden" name="action" value="update" />
		<?php if ($group->get('id')) { ?>
			<input type="hidden" name="id" value="<?php $group->write('id'); ?>" />
		<?php } ?>

		<p class="input">
			<label for="headline">Name:</label>
			<input type="text" id="groupname" name="groupname" class="required text" value="<?php $group->htmlspecialwrite('groupname'); ?>" />
		</p>
		<p class="input">
			<label for="body">Description:</label>
			<textarea id="description" name="description" class="text required" wrap="virtual"><?php $group->htmlspecialwrite('description'); ?></textarea>
		</p>

		<p class="input" id="group_img" <?php if (!$group->saved() || !($file = $group->files()->contains('file_name','img'))) {?>style="display: none;"<?php } ?>>
			<label for="link">Primary Image:</label> <?php if ($group->saved()) { ?><a href="<?php $POD->podRoot(); ?>/admin/files?contentId=<?php $group->write('id'); ?>">More Files</a><?php } ?>
			<?php if ($file) { ?>
				<br />			
				<a href="<?php $POD->podRoot(); ?>/admin/files?id=<?php echo $file->id; ?>"><img src="<?php echo $file->src('100',true); ?>" border="0"></a>
			<?php } else { ?>
				<input name="img" type="file" class="text" />	
			<?php } ?>
		</p>		

		<p class="input" id="group_tags" <?php if (!$group->saved() || $group->tags()->count()==0) {?>style="display: none;"<?php } ?>>
			<label for="tags">Tags:</label>
			<input id="tags" name="tags" class="text" value="<?php echo $group->tagsAsString(); ?>" />
		</p>


		<?php if (!$group->saved() || !$group->files()->contains('file_name','img')) {?>
			<a href="#" onclick="return showOptional(this,'#group_img');" class="optional_field">+ Add Image</a>
		<?php } ?>

				
		<?php if (!$group->saved() || $group->tags()->count()==0) {?>
			<a href="#" onclick="return showOptional(this,'#group_tags');" class="optional_field">+ Add Tags</a>
		<?php } ?>

		<h2>Additional Information</h2>
		
		<?php $meta = $group->getMeta();
		if ($meta) { 
			foreach ($meta as $field=>$value) { ?>
				<p class="input">
					<label for="meta_<?php echo $field; ?>"><?php echo $field; ?>:</label>
					<?php if (strlen($value) > 50) { ?>
						<textarea class="text" name="meta_<?php echo $field; ?>"><?php echo htmlspecialchars($value); ?></textarea>
					<?php } else { ?>
						<input class="text" type="text" name="meta_<?php echo $field; ?>" id="meta_<?php echo $field; ?>" value="<?php echo htmlspecialchars($value); ?>"/>
					<?php } ?>
				</p>				
			<?php }
		} ?>
		
		<div id="new_meta_fields">
		</div>
		<div id="new_meta" style="display:none;">
				<label for="new_meta_name">Name the new field:</label>
				<input id="new_meta_name" type="text" class="text meta_lookup" /><input type="submit" onclick="return addMetaField();" value="Add to Form">
		</div>
		
		<a href="#" onclick="return showOptional(this,'#new_meta');" class="optional_field" id="add_field_link">+ Add Field</a>
	
	</div>

</form>

	<?php if($group->saved()) { ?>

	<!-- BEGIN MEMBERS -->
	<div id="group_members_panel" class="panel panel_with_options">
		<h1><a name="members"></a>Members This Group</h1>
		<form id="addMember" onsubmit="return addMemberSearch();" class="new_item">
			<label for="addMember_q">Add Member</label>
			<input name="q" class="text" id="addMember_q" />
			<input type="button" onclick="return addMemberSearch();" value="Find"/>
		</form>
			
		<div id="addMemberResults"></div>		
		
		<div id="members">
			<?php if ($group->allmembers()->count() > 0) { 
				while ($member = $group->allmembers()->getNext()) { 
					$member->set('membership',$group->isMember($member),false);
					$member->output('group');
				}
			} else { ?>
				<p class="column_padding">This group has no members.</p>
			<?php } ?>
		</div>
	</div>
	<!-- END MEMBERS -->



	<!-- BEGIN CHILD POSTS -->
	<div id="child_documents_panel" class="panel panel_with_options">
		<h1><a name="content"></a>Content In This Group</h1>
		<form id="addChild" onsubmit="return addChildSearch();" class="new_item">
				<label for="addChild_q">Add Post</label>
				<input name="q" class="text" id="addChild_q" />
				<input type="button" onclick="return addChildSearch();" value="Find"/>
		</form>
			
		<div id="addChildResults"></div>		
		
		<div id="child_documents">
			<?php if ($group->content()->exists()) { 
				while ($child = $group->content()->getNext()) { 
					$child->output('group');
				}
			} else { ?>
				<p class="column_padding">This group has no posts.</p>
			<?php } ?>
		</div>
	</div>
	<!-- END CHILD POSTS -->
	
	<?php } ?>