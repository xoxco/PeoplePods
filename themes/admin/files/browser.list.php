<li class="fileBrowser_file">
	<?php if ($file->isImage()) { ?>
		<a href="#" onclick="return fileBrowserDetails(<?php echo $file->parent()->id ?>,<?php echo $file->id ?>);"><img src="<?php echo $file->src('50'); ?>" border="0" /></a>

		<a href="#" onclick="return fileBrowserDetails(<?php echo $file->parent()->id ?>,<?php echo $file->id ?>);"><strong><?php echo $file->file_name; ?></strong><br />
		<?php echo $file->original_name; ?></a>
		
	<?php } else { ?>
		<img src="<?php $POD->templateDir(); ?>/img/document_icon.png" border="0" />
		
		<strong><?php echo $file->file_name; ?></strong><br />
		<?php echo $file->original_name; ?>
		
		<a class="submit" href="#" onclick='return insertMarkup("<a href=\"<?php echo $file->original_file; ?>\"><?php echo $file->file_name; ?></a>");'>Insert Link</a>

	<?php } ?>
	<div class="clearer"></div>
</li>