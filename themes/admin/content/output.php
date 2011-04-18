<? if ($doc->saved()) { ?>
	<script>
		current_content = <?= $doc->id; ?>
	</script>
<? } ?>
<form method="post" action="<? $doc->POD->podRoot(); ?>/admin/content/" enctype="multipart/form-data" id="content_form" class="edit_form valid">
<input type="hidden" name="action" value="update" />
<? if ($doc->get('id')) { ?>
	<input type="hidden" name="id" value="<? $doc->write('id'); ?>" />
<? } ?>

<div id="options">

	<div class="option_set">
		<input type="submit" class="button" name="save" value="Save" />
			
		<p class="input">
			<label for="type">Type:</label>
			<span id="content_type">
				<? $doc->write('type'); ?>
				<a href="#" onclick="return changeType();">Change</a>
			</span>
			<? if ($doc->type=='spam'){?><input type="submit" name="notspam" value="Not Spam!" /><? } ?>

			<input id="type" name="type" autocomplete="off" class="required text" value="<? $doc->htmlspecialwrite('type'); ?>" style="display:none;"/>
		</p>
	
		
		<p class="input"><label for="privacy">Privacy:</label><select name="privacy" id="privacy">
			<option value="public" <? if ($doc->get('privacy')=="public") { ?>selected<? } ?>>Public</option>
			<option value="friends_only" <? if ($doc->get('privacy')=="friends_only") { ?>selected<? } ?>>Friends Only</option>
			<option value="group_only" <? if ($doc->get('privacy')=="group_only") { ?>selected<? } ?>>Group Only</option>		
		</select></p>
		
		<p class="input"><label for="status">Status:</label><input name="status" class="text" value="<? $doc->htmlspecialwrite('status'); ?>" /></p>

		<p class="input"><label for="stub">Stub:</label><input name="stub" class="text" value="<? $doc->htmlspecialwrite('stub'); ?>" /></p>

		<p class="input"><label for="date">Date:</label><input id="date" name="date" class="text" value="<? $doc->htmlspecialwrite('date'); ?>" /></p>
		
		<p><a href="<?= $doc->permalink; ?>">View</a></p>

	</div>


	<? if ($doc->saved()) { ?>

		<? if ($doc->get('parentId')) { 
			$parent = $this->POD->getContent(array('id'=>$doc->get('parentId')));
			?>
			<div class="option_set">		
				<h3>Parent Content</h3>
				<P>Child of <a href="<? $doc->POD->podRoot(); ?>/admin/content/?id=<? $parent->write('id'); ?>"><? $parent->write('headline'); ?></a>.</p>
			</div>
		<? } ?>
		<? if ($doc->get('groupId')) { 
			$group = $this->POD->getGroup(array('id'=>$doc->get('groupId')));
			?>
			<div class="option_set">
				<h3>Group</h3>
				<P>Belongs to the <a href="<? $doc->POD->podRoot(); ?>/admin/groups/?id=<? $group->write('id'); ?>"><? $group->write('groupname'); ?></a> group.</p>
			</div>		
		<? } ?>			
		<div class="option_set">
			<h3>Author</h3>

			<p class="input" id="creator_display">
				<label>Creator:</label>
				<? if ($file = $doc->creator()->files()->contains('file_name','img')) { ?>
					<img src="<?= $file->src('50',true); ?>" align="left" hspace="5"/>
				<? } ?>
				<a href="<? $doc->POD->podRoot(); ?>/admin/people/?id=<? echo $doc->creator('id'); ?>"><? echo $doc->creator('nick'); ?></a><br />
				<a href="<? $doc->POD->podRoot(); ?>/admin/content/search.php?userId=<? $doc->creator()->write('id'); ?>" class="tool">More from this person...</a><br />
			</p>
			<? if ($doc->author()->id!=$doc->creator()->id) { ?>
			<p class="input" id="author_display">
				<label>Author:</label>
				<? if ($file = $doc->author()->files()->contains('file_name','img')) { ?>
					<img src="<?= $file->src('50',true); ?>" align="left" hspace="5" />
				<? } ?>
				<a href="<? $doc->POD->podRoot(); ?>/admin/people/?id=<? echo $doc->author('id'); ?>"><? echo $doc->author('nick'); ?></a><br />
				<a href="<? $doc->POD->podRoot(); ?>/admin/content/search.php?userId=<? $doc->author()->write('id'); ?>" class="tool">More from this person...</a><br />
			</p>
			<? } ?>
			
			<a href="#" onclick="return changeAuthor();" id="changeAuthorLink">Change Author</a>
			<p class="input" id="author_edit" style="display:none;">
				<label>Change author to:</label>
				<input name="userId_autofill" id="userId_autofill" />
				<input name="userId" id="userId" type="hidden" value="<?= $doc->userId; ?>" />
			</p>

			
			<p class="input">
				<label>Last Modified:</label>
				<?= date('M j, Y \@ H:i',strtotime($doc->get('changeDate'))); ?>
			</p>
		</div>

		<div class="option_set">
			<a href="<? $doc->POD->podRoot(); ?>/admin/content/?id=<? $doc->write('id'); ?>&action=delete" onclick="return confirm('Really delete this document FOREVER?');" class="tool">Delete</a>
		</div>
	<? }  ?>

		

</div>
<div class="panel panel_with_options" id="post_info_panel">
	<? if ($doc->saved()) { ?>
		<h1>
			<ul class="attachments">
				<li><a href="#comments" class="tool"><? echo $doc->comments()->totalCount(); ?> <? echo $this->POD->pluralize($doc->comments()->totalCount(),'Comment','Comments'); ?></a></li>
				<li><a href="#children" class="tool"><? echo $doc->children()->totalCount(); ?> <? echo $this->POD->pluralize($doc->children()->totalCount(),'Child post','Child posts'); ?></a></li>
				<li><a href="<? $doc->POD->podRoot(); ?>/admin/files/index.php?contentId=<? $doc->write('id'); ?>" class="tool"><? echo $doc->files()->totalCount(); ?> <? echo $this->POD->pluralize($doc->files()->totalCount(),'File','Files'); ?></a></li>
				<li><a href="<? $doc->POD->podRoot(); ?>/admin/flags/index.php?contentId=<? $doc->write('id'); ?>" class="tool">Flags</a></li>
			</ul>
			Edit <?= ucfirst($doc->type); ?> 
		</h1>
	<? } else { ?>
		<h1>Add <?= ucfirst($doc->type); ?></h1>
	<? } ?>


		<? if ($this->hidden==1) { ?>
		<div class="info">
			This content was deleted and should no longer be available on the public site.
		</div>
		<? } ?>
		<p class="input">
			<label for="headline">Headline:</label>
			<textarea id="headline" name="headline" class="required text"  wrap="virtual"><? $doc->htmlspecialwrite('headline'); ?></textarea>
		</p>

		<p class="input">
			<label for="body">Body:</label>
			<textarea id="body" name="body" class="text tinymce" wrap="virtual"><? $doc->htmlspecialwrite('body'); ?></textarea>
		</p>

		<p class="input" id="content_img" <? if (!$doc->saved() || !($file = $doc->files()->contains('file_name','img'))) {?>style="display: none;"<? } ?>>
			<label for="link">Primary Image:</label> <? if ($doc->saved()) { ?><a href="<? $POD->podRoot(); ?>/admin/files?contentId=<? $doc->write('id'); ?>">More Files</a><? } ?>
			<? if ($file) { ?>
				<br />			
				<a href="<? $POD->podRoot(); ?>/admin/files?id=<?= $file->id; ?>"><img src="<?= $file->src('100',true); ?>" border="0"></a>
			<? } else { ?>
				<input name="img" type="file" class="text" />	
			<? } ?>
		</p>		
		
		<p class="input" id="content_link" <? if (!$doc->saved() || !$doc->link) {?>style="display: none;"<? } ?>>
			<label for="link">Link:</label> Include a link to an external site
			<input name="link" class="text url" value="<? $doc->htmlspecialwrite('link'); ?>" />
		</p>


		<p class="input" id="content_tags" <? if (!$doc->saved() || $doc->tags()->count()==0) {?>style="display: none;"<? } ?>>
			<label for="tags">Tags:</label>
			<input id="tags" name="tags" class="text" value="<? echo $doc->tagsAsString(); ?>" />
		</p>

		<? if (!$doc->saved() || !$doc->files()->contains('file_name','img')) {?>
			<a href="#" onclick="return showOptional(this,'#content_img');" class="optional_field">+ Add Image</a>
		<? } ?>
		<? if (!$doc->saved() || !$doc->link) {?>
			<a href="#" onclick="return showOptional(this,'#content_link');" class="optional_field">+ Add Link</a>
		<? } ?>
		<? if (!$doc->saved() || $doc->tags()->count()==0) {?>
			<a href="#" onclick="return showOptional(this,'#content_tags');" class="optional_field">+ Add Tags</a>
		<? } ?>

		<h2>Additional Information</h2>
		
		<? $meta = $doc->getMeta();
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
	
	<? if ($doc->saved()) {?>

	<!-- BEGIN COMMENTS -->
	<div id="comments_panel" class="panel panel_with_options">
		<h1><a name="comments"></a>Comments</h1>
		<? $doc->comments()->output('comment','header','footer',null,'This content has no comments.'); ?>
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
			<? if ($doc->children()->exists()) { 
				while ($child = $doc->children()->getNext()) { 
					$child->output('child');
				}
			} else { ?>
				<p class="column_padding">This post has no child posts.</p>
			<? } ?>
		</div>
	</div>
	<!-- END CHILD POSTS -->
	
	<? } ?>

</div>