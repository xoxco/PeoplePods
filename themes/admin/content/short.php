<tr class="<? if ($this->isEvenItem) {?>even<? } else { ?>odd<? } ?>">
	<td valign="top" width="400">
		<a href="<? $POD->podRoot(); ?>/admin/content/?id=<? $doc->write('id'); ?>"><? echo $doc->get_short('headline',55); ?></a>
		<br />
		<span class="preview"><? echo $doc->shorten('body',100); ?></span>	
	</td>
	<td valign="top">
		<a href="<? $doc->POD->podRoot(); ?>/admin/people/?id=<? $doc->author()->write('id'); ?>"><? $doc->author()->write('nick'); ?></a>
	</td>
	<td align="center">
		<? echo $doc->comments()->totalCount(); ?>
	</td>
	<td>
		<?= date('Y-m-d H:i',strtotime($doc->get('date'))); ?>
	</td>
	<td class="tools">
		<a href="chstatus.php?id=<? $doc->write('id'); ?>&status=new" class="tool <? if ($doc->get('status')=="new"){echo "hot";}?>">New</a> &rarr;
		<a href="chstatus.php?id=<? $doc->write('id'); ?>&status=approved" class="tool <? if ($doc->get('status')!="new" && $doc->get('status')!='featured'){echo "hot";}?>">Reviewed</a> &rarr;
		<a href="chstatus.php?id=<? $doc->write('id'); ?>&status=featured" class="tool <? if ($doc->get('status')=="featured"){echo "hot";}?>">Featured</a>	
	</td>
</tr>