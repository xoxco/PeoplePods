<div class="doc_short">
	<div class="column_1">
		<div class="column_padding">
			<?php if ($img = $content->files()->contains('file_name','img')) { ?>
				<a href="<?php $content->write('permalink'); ?>"><img src="<?php echo $img->src(50,true); ?>" border="0" height="50" width="50" /></a>
			<?php } else { ?>
				<a href="<?php $content->write('permalink'); ?>"><img src="<?php $content->POD->templateDir(); ?>/img/noimage.png" border="0" /></a>
			<?php } ?> 
		</div>
	</div>
	<div class="column_7">
		<div class="column_padding">
			<a href="<?php $content->POD->podRoot(); ?>/admin/content/?id=<?php $content->write('id'); ?>"  title="View this content"><?php $content->write('headline'); ?></a>
		</div>
	</div>
	<div class="column_2 last">
		<div class="column_padding">
			<?php if ($content->flag_userId) { ?>
			<a href="#" id="remove_<?php $content->write('flag') ?>_<?php $content->write('flag_itemId') ?>_<?php $content->write('flag_userId'); ?>" onclick="return removeFlag('<?php $content->write('flag'); ?>','<?php $content->write('flag_type'); ?>',<?php $content->write('flag_itemId'); ?>,<?php $content->write('flag_userId'); ?>);">Remove <?php $content->write('flag'); ?></a>
			<a href="#" style="display:none;" id="add_<?php $content->write('flag') ?>_<?php $content->write('flag_itemId') ?>_<?php $content->write('flag_userId'); ?>" onclick="return addFlag('<?php $content->write('flag'); ?>','<?php $content->write('flag_type'); ?>',<?php $content->write('flag_itemId'); ?>,<?php $content->write('flag_userId'); ?>);">Add <?php $content->write('flag'); ?></a>
			<?php } else { ?>
				<a href="<?php $content->POD->podRoot(); ?>/admin/flags/index.php?contentId=<?php $content->write('id'); ?>&flag=<?php echo $content->flag; ?>&type=user&direction=in">
				<?php echo $POD->pluralize($content->flagCount($content->flag),'@number time','@number times'); ?>
				</a>
			<?php } ?>

		</div>
	</div>
	<div class="clearer"></div>
</div>
