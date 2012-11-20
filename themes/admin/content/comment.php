<div class="comment">
	<p><a href="<?php $POD->podRoot(); ?>/admin/people/?id=<?php $comment->author()->write('id'); ?>"><?php $comment->author()->write('nick'); ?></a> left a comment on 		<?php if ($comment->parent()->TYPE=='content') { ?>
			<a href="<?php $POD->podRoot(); ?>/admin/content/?id=<?php echo $comment->parent()->id; ?>"><?php echo $comment->parent()->headline; ?></a>
		<?php } else { ?>
			<a href="<?php $POD->podRoot(); ?>/admin/people/?id=<?php echo $comment->parent()->id; ?>"><?php echo $comment->parent()->nick; ?></a>
		<?php } ?>, (<a href="<?php echo $comment->parent()->permalink; ?>#<?php echo $comment->id; ?>"><?php echo $POD->timesince($comment->get('minutes')); ?></a>)</p>
	<p><?php $comment->writeFormatted('comment'); ?></p>
	<p><a href="<?php $POD->podRoot(); ?>/admin/comments/?id=<?php echo $comment->id; ?>">Edit</a> | <a href="<?php $POD->podRoot(); ?>/admin/comments/?delete=<?php echo $comment->id; ?>" onclick="return confirm('Are you sure you want to permanently delete this comment?');">Delete</a></p>
</div>