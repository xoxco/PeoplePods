<div id="options">
	<div class="option_set">
		<?
			$file->output('upload');
		
		?>
	</div>
</div>
<div class="panel panel_with_options">
	<h1>File: <? $file->write('file_name'); ?></h1>
	
	<p>
		<label>Original Name:</label>
		<? $file->write('original_name'); ?>
	</p>

	<? if ($file->isImage()) { ?>
		<p>
			<a href="<? $file->write('thumbnail');?>">Download Thumbnail</a>
			| <a href="<? $file->write('original_file');?>">Download Original</a>
		</p>
		<a href="<? $file->write('resized'); ?>"><img src="<? $file->write('resized'); ?>" /></a>
	<? } else { ?>

		<p>
			<a href="<? $file->write('original_file');?>">Download Original</a>
		</p>

	<? } ?>

		<form method="post" action="<? $POD->podRoot(); ?>/admin/files/?<? if ($file->get('contentId')) { ?>&contentId=<? $file->write('contentId'); ?><? } else { ?>userId=<? $file->write('userId'); ?><? } ?>">
			<input type="hidden" name="id" value="<? $file->write('id'); ?>" />
			<p><input type="submit" name="delete" value="Delete" onclick="return confirm('Are you sure you want to delete this file?');" />	</p>
		</form>	


</div>

