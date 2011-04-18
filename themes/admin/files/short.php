<tr  class="<? if ($this->isEvenItem) {?>even<? } else { ?>odd<? } ?>">
	<td valign="top" align="left">
		<a href="<? $POD->podRoot(); ?>/admin/files/?id=<? $file->write('id'); ?>">
		<? if ($file->isImage()) { ?>
			<img src="<?= $file->src(50,true); ?>" width="50" height="50" border="0" />
		<? } else { ?>				
			<img src="<? $POD->templateDir(); ?>/img/document_icon.png" width="50" height="50" border="0" />
		<? } ?></a>	
	</td>
	<td valign="top" align="left">	
		<a href="<? $POD->podRoot(); ?>/admin/files/?id=<? $file->write('id'); ?>"><? $file->write('file_name'); ?></a>
		<br />
		<? $file->write('original_name'); ?>
	</td>
	<td valign="top" align="left">
		<a href="<? $POD->podRoot(); ?>/admin/people/?id=<? $file->author()->write('id'); ?>"><? $file->author()->write('nick'); ?></a>	
	</td>
	<td valign="top" align="left">
		<? if ($file->parent()) { ?>
			<a href="<? $POD->podRoot(); ?>/admin/content/?id=<? $file->parent()->write('id'); ?>"><? $file->parent()->write('headline'); ?></a>		
		<? } ?>	
		<? if ($file->group()) { ?>
			<a href="<? $POD->podRoot(); ?>/admin/groups/?id=<? $file->group()->write('id'); ?>"><? $file->group()->write('groupname'); ?></a>		
		<? } ?>	
	</td>
	<td valign="top" align="left">	
		<?= date('Y-m-d H:i',strtotime($file->get('date'))); ?>
	</td>
</tr>