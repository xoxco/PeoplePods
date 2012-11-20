<tr class="<?php if ($this->isEvenItem) {?>even<?php } else { ?>odd<?php } ?>">
	<td valign="top" width="400">
		<a href="<?php $group->POD->podRoot(); ?>/admin/groups/?id=<?php $group->write('id'); ?>"><?php $group->write('groupname'); ?></a>
		<br />
		<span class="preview"><?php echo $group->get_short('description',55); ?></span>
	</td>
	<td valign="top">
		<?php echo $group->members()->totalCount(); ?>
	</td>
	<td valign="top">
		<?php echo $group->content()->totalCount(); ?>
	</td>
	<td valign="top">
		<?php $group->owner()->permalink(); ?>		
	</td>
</tr>