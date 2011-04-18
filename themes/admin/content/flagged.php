<div class="doc_short">
	<div class="column_1">
		<div class="column_padding">
			<? if ($img = $content->files()->contains('file_name','img')) { ?>
				<a href="<? $content->write('permalink'); ?>"><img src="<?= $img->src(50,true); ?>" border="0" height="50" width="50" /></a>
			<? } else { ?>
				<a href="<? $content->write('permalink'); ?>"><img src="<? $content->POD->templateDir(); ?>/img/noimage.png" border="0" /></a>
			<? } ?> 
		</div>
	</div>
	<div class="column_7">
		<div class="column_padding">
			<a href="<? $content->POD->podRoot(); ?>/admin/content/?id=<? $content->write('id'); ?>"  title="View this content"><? $content->write('headline'); ?></a>
		</div>
	</div>
	<div class="column_2 last">
		<div class="column_padding">
			<? if ($content->flag_userId) { ?>
			<a href="#" id="remove_<? $content->write('flag') ?>_<? $content->write('flag_itemId') ?>_<? $content->write('flag_userId'); ?>" onclick="return removeFlag('<? $content->write('flag'); ?>','<? $content->write('flag_type'); ?>',<? $content->write('flag_itemId'); ?>,<? $content->write('flag_userId'); ?>);">Remove <? $content->write('flag'); ?></a>
			<a href="#" style="display:none;" id="add_<? $content->write('flag') ?>_<? $content->write('flag_itemId') ?>_<? $content->write('flag_userId'); ?>" onclick="return addFlag('<? $content->write('flag'); ?>','<? $content->write('flag_type'); ?>',<? $content->write('flag_itemId'); ?>,<? $content->write('flag_userId'); ?>);">Add <? $content->write('flag'); ?></a>
			<? } else { ?>
				<a href="<? $content->POD->podRoot(); ?>/admin/flags/index.php?contentId=<? $content->write('id'); ?>&flag=<?= $content->flag; ?>&type=user&direction=in">
				<? echo $POD->pluralize($content->flagCount($content->flag),'@number time','@number times'); ?>
				</a>
			<? } ?>

		</div>
	</div>
	<div class="clearer"></div>
</div>
