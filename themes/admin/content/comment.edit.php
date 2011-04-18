<form method="post" action="<? $POD->podRoot(); ?>/admin/comments/index.php" class="edit_form valid" id="comment_form">
	<input name="id" value="<?= $comment->id; ?>" type="hidden" />
	
	<div id="options">
		<div class="option_set">
			<input type="submit" class="button" value="Save" />
		</div>
		<div class="option_set">

			<p class="input">
				<label>Attached to:</label>
				<? if ($comment->parent()->TYPE=='content') { ?>
					<a href="<? $POD->podRoot(); ?>/admin/content/?id=<?= $comment->parent()->id; ?>"><?= $comment->parent()->headline; ?></a>
				<? } else { ?>
					<a href="<? $POD->podRoot(); ?>/admin/people/?id=<?= $comment->parent()->id; ?>"><?= $comment->parent()->nick; ?></a>
				<? } ?>
		
			</p>
		
			<p class="input">
				<label>Author:</label>
				<a href="<? $POD->podRoot(); ?>/admin/people/?id=<? $comment->author()->write('id'); ?>"><? $comment->author()->write('nick'); ?></a>
			</p>	

			<a href="?delete=<?= $comment->id; ?>" onclick="return confirm('Are you sure you want to permanently delete this comment?');">Delete</a>
		</div>
	</div>	

	<div class="panel panel_with_options">	
	
	<h1>Edit Comment</h1>

	<p class="input">
		<label for="comment">Comment:</label>
		<textarea name="comment" id="comment" class="text required"><? $comment->htmlspecialwrite('comment'); ?></textarea>
	</p>	
	
	<p class="input">
		<label for="type">Type:</label>
		<input name="type" id="type" class="text" value="<? $comment->htmlspecialwrite('type'); ?>" />
	</p>
	
	<h2>Additional Information</h2>
	
	<? $meta = $comment->getMeta();
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