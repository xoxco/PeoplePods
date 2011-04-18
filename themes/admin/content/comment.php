<div class="comment">
	<p><a href="<? $POD->podRoot(); ?>/admin/people/?id=<? $comment->author()->write('id'); ?>"><? $comment->author()->write('nick'); ?></a> left a comment on 		<? if ($comment->parent()->TYPE=='content') { ?>
			<a href="<? $POD->podRoot(); ?>/admin/content/?id=<?= $comment->parent()->id; ?>"><?= $comment->parent()->headline; ?></a>
		<? } else { ?>
			<a href="<? $POD->podRoot(); ?>/admin/people/?id=<?= $comment->parent()->id; ?>"><?= $comment->parent()->nick; ?></a>
		<? } ?>, (<a href="<?= $comment->parent()->permalink; ?>#<?= $comment->id; ?>"><?= $POD->timesince($comment->get('minutes')); ?></a>)</p>
	<p><? $comment->writeFormatted('comment'); ?></p>
	<p><a href="<? $POD->podRoot(); ?>/admin/comments/?id=<?= $comment->id; ?>">Edit</a> | <a href="<? $POD->podRoot(); ?>/admin/comments/?delete=<?= $comment->id; ?>" onclick="return confirm('Are you sure you want to permanently delete this comment?');">Delete</a></p>
</div>