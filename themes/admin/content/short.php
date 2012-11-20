<tr class="<?php if ($this->isEvenItem) {?>even<?php } else { ?>odd<?php } ?>">
	<td valign="top" width="400">
		<a href="<?php $POD->podRoot(); ?>/admin/content/?id=<?php $doc->write('id'); ?>"><?php echo $doc->get_short('headline',55); ?></a>
		<br />
		<span class="preview"><?php echo $doc->shorten('body',100); ?></span>	
	</td>
	<td valign="top">
		<a href="<?php $doc->POD->podRoot(); ?>/admin/people/?id=<?php $doc->author()->write('id'); ?>"><?php $doc->author()->write('nick'); ?></a>
	</td>
	<td align="center">
		<?php echo $doc->comments()->totalCount(); ?>
	</td>
	<td>
		<?php echo date('Y-m-d H:i',strtotime($doc->get('date'))); ?>
	</td>
	<td class="tools">
		<a href="chstatus.php?id=<?php $doc->write('id'); ?>&status=new" class="tool <?php if ($doc->get('status')=="new"){echo "hot";}?>">New</a> &rarr;
		<a href="chstatus.php?id=<?php $doc->write('id'); ?>&status=approved" class="tool <?php if ($doc->get('status')!="new" && $doc->get('status')!='featured'){echo "hot";}?>">Reviewed</a> &rarr;
		<a href="chstatus.php?id=<?php $doc->write('id'); ?>&status=featured" class="tool <?php if ($doc->get('status')=="featured"){echo "hot";}?>">Featured</a>	
	</td>
</tr>