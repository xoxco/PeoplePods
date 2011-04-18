<tr class="<? if ($this->isEvenItem) {?>even<? } else { ?>odd<? } ?>">
	<td valign="top" width="400">
		<a href="<? $group->POD->podRoot(); ?>/admin/groups/?id=<? $group->write('id'); ?>"><? $group->write('groupname'); ?></a>
		<br />
		<span class="preview"><? echo $group->get_short('description',55); ?></span>
	</td>
	<td valign="top">
		<? echo $group->members()->totalCount(); ?>
	</td>
	<td valign="top">
		<? echo $group->content()->totalCount(); ?>
	</td>
	<td valign="top">
		<? $group->owner()->permalink(); ?>		
	</td>
</tr>