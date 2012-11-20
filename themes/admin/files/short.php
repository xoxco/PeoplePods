<tr  class="<?php if ($this->isEvenItem) {?>even<?php } else { ?>odd<?php } ?>">
	<td valign="top" align="left">
		<a href="<?php $POD->podRoot(); ?>/admin/files/?id=<?php $file->write('id'); ?>">
		<?php if ($file->isImage()) { ?>
			<img src="<?php echo $file->src(50,true); ?>" width="50" height="50" border="0" />
		<?php } else { ?>				
			<img src="<?php $POD->templateDir(); ?>/img/document_icon.png" width="50" height="50" border="0" />
		<?php } ?></a>	
	</td>
	<td valign="top" align="left">	
		<a href="<?php $POD->podRoot(); ?>/admin/files/?id=<?php $file->write('id'); ?>"><?php $file->write('file_name'); ?></a>
		<br />
		<?php $file->write('original_name'); ?>
	</td>
	<td valign="top" align="left">
		<a href="<?php $POD->podRoot(); ?>/admin/people/?id=<?php $file->author()->write('id'); ?>"><?php $file->author()->write('nick'); ?></a>	
	</td>
	<td valign="top" align="left">
		<?php if ($file->parent()) { ?>
			<a href="<?php $POD->podRoot(); ?>/admin/content/?id=<?php $file->parent()->write('id'); ?>"><?php $file->parent()->write('headline'); ?></a>		
		<?php } ?>	
		<?php if ($file->group()) { ?>
			<a href="<?php $POD->podRoot(); ?>/admin/groups/?id=<?php $file->group()->write('id'); ?>"><?php $file->group()->write('groupname'); ?></a>		
		<?php } ?>	
	</td>
	<td valign="top" align="left">	
		<?php echo date('Y-m-d H:i',strtotime($file->get('date'))); ?>
	</td>
</tr>