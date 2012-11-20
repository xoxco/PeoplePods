<div class="person_short">
	<div class="column_1">
		<div class="column_padding">
			<?php if ($img = $user->files()->contains('file_name','img')) { ?>
				<a href="<?php $user->write('permalink'); ?>"><img src="<?php echo $img->src(50,true); ?>" border="0" height="50" width="50" /></a>
			<?php } else { ?>
				<a href="<?php $user->write('permalink'); ?>"><img src="<?php $user->POD->templateDir(); ?>/img/noimage.png" border="0" /></a>
			<?php } ?> 
		</div>
	</div>
	<div class="column_7">
		<div class="column_padding">
			<a href="<?php $user->POD->podRoot(); ?>/admin/people/?id=<?php $user->write('id'); ?>"  title="View this person's account details"><?php $user->write('nick'); ?></a>
		</div>
	</div>
	<div class="column_2 last">
		<div class="column_padding">
			<?php if ($user->flag_userId) { ?>
			<a href="#" id="remove_<?php $user->write('flag'); ?>_<?php $user->write('flag_itemId'); ?>_<?php $user->write('flag_userId'); ?>" onclick="return removeFlag('<?php $user->write('flag'); ?>','<?php $user->write('flag_type'); ?>',<?php $user->write('flag_itemId'); ?>,<?php $user->write('flag_userId'); ?>);">Remove <?php $user->write('flag'); ?></a>
			<a href="#" style="display:none;" id="add_<?php $user->write('flag'); ?>_<?php $user->write('flag_itemId'); ?>_<?php $user->write('flag_userId'); ?>" onclick="return addFlag('<?php $user->write('flag'); ?>','<?php $user->write('flag_type'); ?>',<?php $user->write('flag_itemId'); ?>,<?php $user->write('flag_userId'); ?>);">Add <?php $user->write('flag'); ?></a>
			<?php } else { ?>
				<a href="<?php $user->POD->podRoot(); ?>/admin/flags/index.php?userId=<?php $user->write('id'); ?>&flag=<?php echo $user->flag; ?>&type=user&direction=in">
				<?php echo $POD->pluralize($user->flagCount($user->flag),'@number time','@number times'); ?>
				</a>
			<?php } ?>

		</div>
	</div>
	<div class="clearer"></div>
</div>
