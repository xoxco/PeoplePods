<tr class="<?php if ($this->isEvenItem) {?>even<?php } else { ?>odd<?php } ?>">
	<td valign="top" align="left">
		<a href="?id=<?php echo $comment->id; ?>"><?php echo $comment->shorten('comment',150); ?></a>
	</td>
	<td valign="top" align="left">
		<a href="<?php $POD->podRoot(); ?>/admin/people/?id=<?php $comment->author()->write('id'); ?>"><?php $comment->author()->write('nick'); ?></a>
	</td>
	<td valign="top" align="left">
		<?php if ($comment->parent()->TYPE=='content') { ?>
			<a href="<?php $POD->podRoot(); ?>/admin/content/?id=<?php echo $comment->parent()->id; ?>"><?php echo $comment->parent()->headline; ?></a>
		<?php } else { ?>
			<a href="<?php $POD->podRoot(); ?>/admin/people/?id=<?php echo $comment->parent()->id; ?>"><?php echo $comment->parent()->nick; ?></a>
		<?php } ?>
	</td>
	<td>
		<?php echo date('Y-m-d H:i',strtotime($comment->get('date'))); ?>
	</td>
</tr>