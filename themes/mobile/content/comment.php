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
<a name="<? $comment->write('id'); ?>"></a>
<div class="comment <? if ($comment->get('isOddItem')) {?>comment_odd<? } ?> <? if ($comment->get('isEvenItem')) {?>comment_even<? } ?> <? if ($comment->get('isLastItem')) {?>comment_last<? } ?> <? if ($comment->get('isFirstItem')) {?>comment_first<? } ?>" id="comment<? $comment->write('id'); ?>">
	<? $comment->author()->output('avatar'); ?>
	<div class="attributed_content comment_body">
		<span class="byline">
			<? if ($comment->POD->isAuthenticated() && ($comment->parent('userId') == $comment->POD->currentUser()->get('id') || $comment->get('userId') == $comment->POD->currentUser()->get('id'))) { ?>
				<span class="gray remove_comment"><a href="#deleteComment" data-ajax="false" data-comment="<? $comment->write('id'); ?>">Remove Comment</a></span>
			<? } ?>
			<span class="author"><? $comment->author()->write('nick'); ?></span> said, (<span class="post_time"><? echo $this->POD->timesince($comment->get('minutes')); ?></span>)
			<a href="#reply" data-ajax="false" data-comment="<?= $comment->id; ?>" data-author="<?= htmlspecialchars($comment->author()->nick); ?>">Reply</a>
		</span>
		<? $comment->writeFormatted('comment') ?>
	</div>
	<div class="clearer"></div>
</div>
