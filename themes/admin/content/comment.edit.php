<form method="post" action="<?php $POD->podRoot(); ?>/admin/comments/index.php" class="edit_form valid" id="comment_form">
	<input name="id" value="<?php echo $comment->id; ?>" type="hidden" />
	
	<div id="options">
		<div class="option_set">
			<input type="submit" class="button" value="Save" />
		</div>
		<div class="option_set">

			<p class="input">
				<label>Attached to:</label>
				<?php if ($comment->parent()->TYPE=='content') { ?>
					<a href="<?php $POD->podRoot(); ?>/admin/content/?id=<?php echo $comment->parent()->id; ?>"><?php echo $comment->parent()->headline; ?></a>
				<?php } else { ?>
					<a href="<?php $POD->podRoot(); ?>/admin/people/?id=<?php echo $comment->parent()->id; ?>"><?php echo $comment->parent()->nick; ?></a>
				<?php } ?>
		
			</p>
		
			<p class="input">
				<label>Author:</label>
				<a href="<?php $POD->podRoot(); ?>/admin/people/?id=<?php $comment->author()->write('id'); ?>"><?php $comment->author()->write('nick'); ?></a>
			</p>	

			<a href="?delete=<?php echo $comment->id; ?>" onclick="return confirm('Are you sure you want to permanently delete this comment?');">Delete</a>
		</div>
	</div>	

	<div class="panel panel_with_options">	
	
	<h1>Edit Comment</h1>

	<p class="input">
		<label for="comment">Comment:</label>
		<textarea name="comment" id="comment" class="text required"><?php $comment->htmlspecialwrite('comment'); ?></textarea>
	</p>	
	
	<p class="input">
		<label for="type">Type:</label>
		<input name="type" id="type" class="text" value="<?php $comment->htmlspecialwrite('type'); ?>" />
	</p>
	
	<h2>Additional Information</h2>
	
	<?php $meta = $comment->getMeta();
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