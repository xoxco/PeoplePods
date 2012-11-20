<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/content/comment.php
* Default output template for comments
* Used by core_usercontent
* 
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/themes
/**********************************************/
?>
<a name="<?php $comment->write('id'); ?>"></a>
<div class="comment <?php if ($comment->get('isOddItem')) {?>comment_odd<?php } ?> <?php if ($comment->get('isEvenItem')) {?>comment_even<?php } ?> <?php if ($comment->get('isLastItem')) {?>comment_last<?php } ?> <?php if ($comment->get('isFirstItem')) {?>comment_first<?php } ?>" id="comment<?php $comment->write('id'); ?>">
	<?php $comment->author()->output('avatar'); ?>
	<div class="attributed_content comment_body">
		<span class="byline">
			<?php if ($comment->POD->isAuthenticated() && ($comment->parent('userId') == $comment->POD->currentUser()->get('id') || $comment->get('userId') == $comment->POD->currentUser()->get('id'))) { ?>
				<span class="gray remove_comment"><a href="#deleteComment" data-ajax="false" data-comment="<?php $comment->write('id'); ?>">Remove Comment</a></span>
			<?php } ?>
			<span class="author"><?php $comment->author()->write('nick'); ?></span> said, (<span class="post_time"><?php echo $this->POD->timesince($comment->get('minutes')); ?></span>)
			<a href="#reply" data-ajax="false" data-comment="<?php echo $comment->id; ?>" data-author="<?php echo htmlspecialchars($comment->author()->nick); ?>">Reply</a>
		</span>
		<?php $comment->writeFormatted('comment') ?>
	</div>
	<div class="clearer"></div>
</div>
