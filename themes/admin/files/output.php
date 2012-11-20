<div id="options">
	<div class="option_set">
		<?php
			$file->output('upload');
		
		?>
	</div>
</div>
<div class="panel panel_with_options">
	<h1>File: <?php $file->write('file_name'); ?></h1>
	
	<p>
		<label>Original Name:</label>
		<?php $file->write('original_name'); ?>
	</p>

	<?php if ($file->isImage()) { ?>
		<p>
			<a href="<?php $file->write('thumbnail');?>">Download Thumbnail</a>
			| <a href="<?php $file->write('original_file');?>">Download Original</a>
		</p>
		<a href="<?php $file->write('resized'); ?>"><img src="<?php $file->write('resized'); ?>" /></a>
	<?php } else { ?>

		<p>
			<a href="<?php $file->write('original_file');?>">Download Original</a>
		</p>

	<?php } ?>

		<form method="post" action="<?php $POD->podRoot(); ?>/admin/files/?<?php if ($file->get('contentId')) { ?>&contentId=<?php $file->write('contentId'); ?><?php } else { ?>userId=<?php $file->write('userId'); ?><?php } ?>">
			<input type="hidden" name="id" value="<?php $file->write('id'); ?>" />
			<p><input type="submit" name="delete" value="Delete" onclick="return confirm('Are you sure you want to delete this file?');" />	</p>
		</form>	


</div>

