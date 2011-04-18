<? if ($group->saved()) { ?>
	<script>
		current_group = <?= $group->id; ?>;
	</script>
<? } ?>

<form method="post" action="<? $group->POD->podRoot(); ?>/admin/groups/" enctype="multipart/form-data" id="update_form" class="valid">
	<div id="options">
		<div class="option_set">
			<input type="submit" class="button" name="save" value="Save"/>		

			<p class="input"><label for="type">Type:</label><select name="type"><option value="public" <? if ($group->get('type')=='public'){?>selected<? } ?>>Public</option><option value="private" <? if ($group->get('type')=='private'){?>selected<? } ?>>Private</option></select></p>
			<p class="input"><label for="stub">Stub:</label><input name="stub" class="text" value="<? $group->write('stub'); ?>" /><br />
				<span class="field_explain">Used to generate permalink</span>
			</p>
			<p class="input"><label for="date">Date:</label><input id="date" name="date" class="text" value="<? $group->write('date'); ?>" /></p>
		</div>
		<? if ($group->saved()) { ?>
		<div class="option_set">
			<b>View: <a href="<? $group->write('permalink'); ?>"><? $group->write('groupname'); ?></a></b>
					
			<P>Created by: <a href="<? $group->POD->podRoot(); ?>/admin/people/?id=<? $group->owner()->write('id'); ?>"><? $group->owner()->write('nick'); ?></a> on <? $group->write('date'); ?>.</p>

		</div>
		<div class="option_set">
			<a href="<? $group->POD->podRoot(); ?>/admin/groups/?id=<? $group->write('id'); ?>&action=delete" class="tool" onclick="return confirm('Are you sure you want to delete this group?');">Delete</a>
		</div>
		<? } ?>
	</div>

	<div class="panel panel_with_options">
	
		<h1><? if ($group->saved()) { ?>
			<ul class="attachments">
			<li><a href="#members" ><? echo $group->members()->totalCount(); ?>  <? echo $group->POD->pluralize($group->members()->totalCount(),'member','members'); ?></a></li>
			<li><a href="#content"><? echo $group->content()->totalCount(); ?> <? echo $group->POD->pluralize($group->content()->totalCount(),'post','posts'); ?></a></li>
			<li><a href="<? $group->POD->podRoot(); ?>/admin/files/index.php?groupId=<? $group->write('id'); ?>" class="tool"><? echo $group->files()->totalCount(); ?> <? echo $this->POD->pluralize($group->files()->totalCount(),'File','Files'); ?></a></li>

			</ul>
			Edit <?= $group->groupname; ?> 
			
			<? } else { ?>
				Add Group
			<? } ?>
		</h1>
			
		<input type="hidden" name="action" value="update" />
		<? if ($group->get('id')) { ?>
			<input type="hidden" name="id" value="<? $group->write('id'); ?>" />
		<? } ?>

		<p class="input">
			<label for="headline">Name:</label>
			<input type="text" id="groupname" name="groupname" class="required text" value="<? $group->htmlspecialwrite('groupname'); ?>" />
		</p>
		<p class="input">
			<label for="body">Description:</label>
			<textarea id="description" name="description" class="text required" wrap="virtual"><? $group->htmlspecialwrite('description'); ?></textarea>
		</p>

		<p class="input" id="group_img" <? if (!$group->saved() || !($file = $group->files()->contains('file_name','img'))) {?>style="display: none;"<? } ?>>
			<label for="link">Primary Image:</label> <? if ($group->saved()) { ?><a href="<? $POD->podRoot(); ?>/admin/files?contentId=<? $group->write('id'); ?>">More Files</a><? } ?>
			<? if ($file) { ?>
				<br />			
				<a href="<? $POD->podRoot(); ?>/admin/files?id=<?= $file->id; ?>"><img src="<?= $file->src('100',true); ?>" border="0"></a>
			<? } else { ?>
				<input name="img" type="file" class="text" />	
			<? } ?>
		</p>		

		<p class="input" id="group_tags" <? if (!$group->saved() || $group->tags()->count()==0) {?>style="display: none;"<? } ?>>
			<label for="tags">Tags:</label>
			<input id="tags" name="tags" class="text" value="<? echo $group->tagsAsString(); ?>" />
		</p>


		<? if (!$group->saved() || !$group->files()->contains('file_name','img')) {?>
			<a href="#" onclick="return showOptional(this,'#group_img');" class="optional_field">+ Add Image</a>
		<? } ?>

				
		<? if (!$group->saved() || $group->tags()->count()==0) {?>
			<a href="#" onclick="return showOptional(this,'#group_tags');" class="optional_field">+ Add Tags</a>
		<? } ?>

		<h2>Additional Information</h2>
		
		<? $meta = $group->getMeta();
		if ($meta) { 
			foreach ($meta as $field=>$value) { ?>
				<p class="input">
					<label for="meta_<? echo $field; ?>"><? echo $field; ?>:</label>
					<? if (strlen($value) > 50) { ?>
						<textarea class="text" name="meta_<? echo $field; ?>"><? echo htmlspecialchars($value); ?></textarea>
					<? } else { ?>
						<input class="text" type="text" name="meta_<? echo $field; ?>" id="meta_<? echo $field; ?>" value="<? echo htmlspecialchars($value); ?>"/>
					<? } ?>
				</p>				
			<? }
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

	<? if($group->saved()) { ?>

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
			<? if ($group->allmembers()->count() > 0) { 
				while ($member = $group->allmembers()->getNext()) { 
					$member->set('membership',$group->isMember($member),false);
					$member->output('group');
				}
			} else { ?>
				<p class="column_padding">This group has no members.</p>
			<? } ?>
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
			<? if ($group->content()->exists()) { 
				while ($child = $group->content()->getNext()) { 
					$child->output('group');
				}
			} else { ?>
				<p class="column_padding">This group has no posts.</p>
			<? } ?>
		</div>
	</div>
	<!-- END CHILD POSTS -->
	
	<? } ?>