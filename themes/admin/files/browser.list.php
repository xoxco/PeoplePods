<li class="fileBrowser_file">
	<? if ($file->isImage()) { ?>
		<a href="#" onclick="return fileBrowserDetails(<?= $file->parent()->id ?>,<?= $file->id ?>);"><img src="<?= $file->src('50'); ?>" border="0" /></a>

		<a href="#" onclick="return fileBrowserDetails(<?= $file->parent()->id ?>,<?= $file->id ?>);"><strong><?= $file->file_name; ?></strong><br />
		<?= $file->original_name; ?></a>
		
	<? } else { ?>
		<img src="<? $POD->templateDir(); ?>/img/document_icon.png" border="0" />
		
		<strong><?= $file->file_name; ?></strong><br />
		<?= $file->original_name; ?>
		
		<a class="submit" href="#" onclick='return insertMarkup("<a href=\"<?= $file->original_file; ?>\"><?= $file->file_name; ?></a>");'>Insert Link</a>

	<? } ?>
	<div class="clearer"></div>
</li>