<?php if ($doc->saved()) { ?>
	<script>
		current_content = <?php echo $doc->id; ?>
	</script>
<?php } ?>
<form method="post" action="<?php $doc->POD->podRoot(); ?>/admin/content/" enctype="multipart/form-data" id="content_form" class="edit_form valid">
<input type="hidden" name="action" value="update" />
<?php if ($doc->get('id')) { ?>
	<input type="hidden" name="id" value="<?php $doc->write('id'); ?>" />
<?php } ?>

<div id="options">

	<div class="option_set">
		<input type="submit" class="button" name="save" value="Save" />
			
		<p class="input">
			<label for="type">Type:</label>
			<span id="content_type">
				<?php $doc->write('type'); ?>
				<a href="#" onclick="return changeType();">Change</a>
			</span>
			<?php if ($doc->type=='spam'){?><input type="submit" name="notspam" value="Not Spam!" /><?php } ?>

			<input id="type" name="type" autocomplete="off" class="required text" value="<?php $doc->htmlspecialwrite('type'); ?>" style="display:none;"/>
		</p>
	
		
		<p class="input"><label for="privacy">Privacy:</label><select name="privacy" id="privacy">
			<option value="public" <?php if ($doc->get('privacy')=="public") { ?>selected<?php } ?>>Public</option>
			<option value="friends_only" <?php if ($doc->get('privacy')=="friends_only") { ?>selected<?php } ?>>Friends Only</option>
			<option value="group_only" <?php if ($doc->get('privacy')=="group_only") { ?>selected<?php } ?>>Group Only</option>		
		</select></p>
		
		<p class="input"><label for="status">Status:</label><input name="status" class="text" value="<?php $doc->htmlspecialwrite('status'); ?>" /></p>

		<p class="input"><label for="stub">Stub:</label><input name="stub" class="text" value="<?php $doc->htmlspecialwrite('stub'); ?>" /></p>

		<p class="input"><label for="date">Date:</label><input id="date" name="date" class="text" value="<?php $doc->htmlspecialwrite('date'); ?>" /></p>
		
		<p><a href="<?php echo $doc->permalink; ?>">View</a></p>

	</div>


	<?php if ($doc->saved()) { ?>

		<?php if ($doc->get('parentId')) { 
			$parent = $this->POD->getContent(array('id'=>$doc->get('parentId')));
			?>
			<div class="option_set">		
				<h3>Parent Content</h3>
				<P>Child of <a href="<?php $doc->POD->podRoot(); ?>/admin/content/?id=<?php $parent->write('id'); ?>"><?php $parent->write('headline'); ?></a>.</p>
			</div>
		<?php } ?>
		<?php if ($doc->get('groupId')) { 
			$group = $this->POD->getGroup(array('id'=>$doc->get('groupId')));
			?>
			<div class="option_set">
				<h3>Group</h3>
				<P>Belongs to the <a href="<?php $doc->POD->podRoot(); ?>/admin/groups/?id=<?php $group->write('id'); ?>"><?php $group->write('groupname'); ?></a> group.</p>
			</div>		
		<?php } ?>			
		<div class="option_set">
			<h3>Author</h3>

			<p class="input" id="creator_display">
				<label>Creator:</label>
				<?php if ($file = $doc->creator()->files()->contains('file_name','img')) { ?>
					<img src="<?php echo $file->src('50',true); ?>" align="left" hspace="5"/>
				<?php } ?>
				<a href="<?php $doc->POD->podRoot(); ?>/admin/people/?id=<?php echo $doc->creator('id'); ?>"><?php echo $doc->creator('nick'); ?></a><br />
				<a href="<?php $doc->POD->podRoot(); ?>/admin/content/search.php?userId=<?php $doc->creator()->write('id'); ?>" class="tool">More from this person...</a><br />
			</p>
			<?php if ($doc->author()->id!=$doc->creator()->id) { ?>
			<p class="input" id="author_display">
				<label>Author:</label>
				<?php if ($file = $doc->author()->files()->contains('file_name','img')) { ?>
					<img src="<?php echo $file->src('50',true); ?>" align="left" hspace="5" />
				<?php } ?>
				<a href="<?php $doc->POD->podRoot(); ?>/admin/people/?id=<?php echo $doc->author('id'); ?>"><?php echo $doc->author('nick'); ?></a><br />
				<a href="<?php $doc->POD->podRoot(); ?>/admin/content/search.php?userId=<?php $doc->author()->write('id'); ?>" class="tool">More from this person...</a><br />
			</p>
			<?php } ?>
			
			<a href="#" onclick="return changeAuthor();" id="changeAuthorLink">Change Author</a>
			<p class="input" id="author_edit" style="display:none;">
				<label>Change author to:</label>
				<input name="userId_autofill" id="userId_autofill" />
				<input name="userId" id="userId" type="hidden" value="<?php echo $doc->userId; ?>" />
			</p>

			
			<p class="input">
				<label>Last Modified:</label>
				<?php echo date('M j, Y \@ H:i',strtotime($doc->get('changeDate'))); ?>
			</p>
		</div>

		<div class="option_set">
			<a href="<?php $doc->POD->podRoot(); ?>/admin/content/?id=<?php $doc->write('id'); ?>&action=delete" onclick="return confirm('Really delete this document FOREVER?');" class="tool">Delete</a>
		</div>
	<?php }  ?>

		

</div>
<div class="panel panel_with_options" id="post_info_panel">
	<?php if ($doc->saved()) { ?>
		<h1>
			<ul class="attachments">
				<li><a href="#comments" class="tool"><?php echo $doc->comments()->totalCount(); ?> <?php echo $this->POD->pluralize($doc->comments()->totalCount(),'Comment','Comments'); ?></a></li>
				<li><a href="#children" class="tool"><?php echo $doc->children()->totalCount(); ?> <?php echo $this->POD->pluralize($doc->children()->totalCount(),'Child post','Child posts'); ?></a></li>
				<li><a href="<?php $doc->POD->podRoot(); ?>/admin/files/index.php?contentId=<?php $doc->write('id'); ?>" class="tool"><?php echo $doc->files()->totalCount(); ?> <?php echo $this->POD->pluralize($doc->files()->totalCount(),'File','Files'); ?></a></li>
				<li><a href="<?php $doc->POD->podRoot(); ?>/admin/flags/index.php?contentId=<?php $doc->write('id'); ?>" class="tool">Flags</a></li>
			</ul>
			Edit <?php echo ucfirst($doc->type); ?> 
		</h1>
	<?php } else { ?>
		<h1>Add <?php echo ucfirst($doc->type); ?></h1>
	<?php } ?>


		<?php if ($this->hidden==1) { ?>
		<div class="info">
			This content was deleted and should no longer be available on the public site.
		</div>
		<?php } ?>
		<p class="input">
			<label for="headline">Headline:</label>
			<textarea id="headline" name="headline" class="required text"  wrap="virtual"><?php $doc->htmlspecialwrite('headline'); ?></textarea>
		</p>

		<p class="input">
			<label for="body">Body:</label>
			<textarea id="body" name="body" class="text tinymce" wrap="virtual"><?php $doc->htmlspecialwrite('body'); ?></textarea>
		</p>

		<p class="input" id="content_img" <?php if (!$doc->saved() || !($file = $doc->files()->contains('file_name','img'))) {?>style="display: none;"<?php } ?>>
			<label for="link">Primary Image:</label> <?php if ($doc->saved()) { ?><a href="<?php $POD->podRoot(); ?>/admin/files?contentId=<?php $doc->write('id'); ?>">More Files</a><?php } ?>
			<?php if ($file) { ?>
				<br />			
				<a href="<?php $POD->podRoot(); ?>/admin/files?id=<?php echo $file->id; ?>"><img src="<?php echo $file->src('100',true); ?>" border="0"></a>
			<?php } else { ?>
				<input name="img" type="file" class="text" />	
			<?php } ?>
		</p>		
		
		<p class="input" id="content_link" <?php if (!$doc->saved() || !$doc->link) {?>style="display: none;"<?php } ?>>
			<label for="link">Link:</label> Include a link to an external site
			<input name="link" class="text url" value="<?php $doc->htmlspecialwrite('link'); ?>" />
		</p>


		<p class="input" id="content_tags" <?php if (!$doc->saved() || $doc->tags()->count()==0) {?>style="display: none;"<?php } ?>>
			<label for="tags">Tags:</label>
			<input id="tags" name="tags" class="text" value="<?php echo $doc->tagsAsString(); ?>" />
		</p>

		<?php if (!$doc->saved() || !$doc->files()->contains('file_name','img')) {?>
			<a href="#" onclick="return showOptional(this,'#content_img');" class="optional_field">+ Add Image</a>
		<?php } ?>
		<?php if (!$doc->saved() || !$doc->link) {?>
			<a href="#" onclick="return showOptional(this,'#content_link');" class="optional_field">+ Add Link</a>
		<?php } ?>
		<?php if (!$doc->saved() || $doc->tags()->count()==0) {?>
			<a href="#" onclick="return showOptional(this,'#content_tags');" class="optional_field">+ Add Tags</a>
		<?php } ?>

		<h2>Additional Information</h2>
		
		<?php $meta = $doc->getMeta();
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
	
	<?php if ($doc->saved()) {?>

	<!-- BEGIN COMMENTS -->
	<div id="comments_panel" class="panel panel_with_options">
		<h1><a name="comments"></a>Comments</h1>
		<?php $doc->comments()->output('comment','header','footer',null,'This content has no comments.'); ?>
	</div>
	<!-- END COMMENTS -->
	

	<!-- BEGIN CHILD POSTS -->
	<div id="child_documents_panel" class="panel panel_with_options">
			<h1><a name="children"></a>Subordinate Content</h1>
			<form id="addChild" onsubmit="return addChildSearch();" class="new_item">
				<label for="addChild_q">Add Child</label>
				<input name="q" class="text" id="addChild_q" />
				<input type="button" onclick="return addChildSearch();" value="Find"/>
			</form>
			
		<div id="addChildResults"></div>		
		
		<div id="child_documents">
			<?php if ($doc->children()->exists()) { 
				while ($child = $doc->children()->getNext()) { 
					$child->output('child');
				}
			} else { ?>
				<p class="column_padding">This post has no child posts.</p>
			<?php } ?>
		</div>
	</div>
	<!-- END CHILD POSTS -->
	
	<?php } ?>

</div>