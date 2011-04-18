<div class="person_short">
	<div class="column_1">
		<div class="column_padding">
			<? if ($img = $user->files()->contains('file_name','img')) { ?>
				<a href="<? $user->write('permalink'); ?>"><img src="<?= $img->src(50,true); ?>" border="0" height="50" width="50" /></a>
			<? } else { ?>
				<a href="<? $user->write('permalink'); ?>"><img src="<? $user->POD->templateDir(); ?>/img/noimage.png" border="0" /></a>
			<? } ?> 
		</div>
	</div>
	<div class="column_7">
		<div class="column_padding">
			<a href="<? $user->POD->podRoot(); ?>/admin/people/?id=<? $user->write('id'); ?>"  title="View this person's account details"><? $user->write('nick'); ?></a>
		</div>
	</div>
	<div class="column_2 last">
		<div class="column_padding">
			<? if ($user->flag_userId) { ?>
			<a href="#" id="remove_<? $user->write('flag'); ?>_<? $user->write('flag_itemId'); ?>_<? $user->write('flag_userId'); ?>" onclick="return removeFlag('<? $user->write('flag'); ?>','<? $user->write('flag_type'); ?>',<? $user->write('flag_itemId'); ?>,<? $user->write('flag_userId'); ?>);">Remove <? $user->write('flag'); ?></a>
			<a href="#" style="display:none;" id="add_<? $user->write('flag'); ?>_<? $user->write('flag_itemId'); ?>_<? $user->write('flag_userId'); ?>" onclick="return addFlag('<? $user->write('flag'); ?>','<? $user->write('flag_type'); ?>',<? $user->write('flag_itemId'); ?>,<? $user->write('flag_userId'); ?>);">Add <? $user->write('flag'); ?></a>
			<? } else { ?>
				<a href="<? $user->POD->podRoot(); ?>/admin/flags/index.php?userId=<? $user->write('id'); ?>&flag=<?= $user->flag; ?>&type=user&direction=in">
				<? echo $POD->pluralize($user->flagCount($user->flag),'@number time','@number times'); ?>
				</a>
			<? } ?>

		</div>
	</div>
	<div class="clearer"></div>
</div>
