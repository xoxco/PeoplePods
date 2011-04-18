<tr class="<? if ($this->isEvenItem) {?>even<? } else { ?>odd<? } ?>">
	<td valign="top" align="left">
		<a href="?id=<?= $comment->id; ?>"><?= $comment->shorten('comment',150); ?></a>
	</td>
	<td valign="top" align="left">
		<a href="<? $POD->podRoot(); ?>/admin/people/?id=<? $comment->author()->write('id'); ?>"><? $comment->author()->write('nick'); ?></a>
	</td>
	<td valign="top" align="left">
		<? if ($comment->parent()->TYPE=='content') { ?>
			<a href="<? $POD->podRoot(); ?>/admin/content/?id=<?= $comment->parent()->id; ?>"><?= $comment->parent()->headline; ?></a>
		<? } else { ?>
			<a href="<? $POD->podRoot(); ?>/admin/people/?id=<?= $comment->parent()->id; ?>"><?= $comment->parent()->nick; ?></a>
		<? } ?>
	</td>
	<td>
		<?= date('Y-m-d H:i',strtotime($comment->get('date'))); ?>
	</td>
</tr>