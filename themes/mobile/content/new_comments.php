<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/content/new_comments.php
* Display a summary of the content and any new comments that were posted since
* the last time this user commented
*
* used by dashboard pod
* 
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/themes
/**********************************************/
?>
<article class="content_short content_new_comments content_<? $doc->write('type'); ?> <? if ($doc->get('isOddItem')) {?>content_odd<? } ?> <? if ($doc->get('isEvenItem')) {?>content_even<? } ?> <? if ($doc->get('isLastItem')) {?>content_last<? } ?> <? if ($doc->get('isFirstItem')) {?>content_first<? } ?>" id="content<? $doc->write('id'); ?>">	
	<? $doc->author()->output('avatar'); ?>
		<section class="attributed_content content_body">
			<header>
			<span class="content_meta">
				<span class="content_author"><? $doc->author()->permalink(); ?></span> posted (<span class="content_time"><? echo $doc->write('timesince'); ?></span>)
			</span>
			<h1><a href="<? $doc->write('permalink'); ?>" title="<? $doc->write('headline'); ?>"><? $doc->write('headline'); ?></a></h1>
			</header>
			<div class="new_comments" id="new_comments_<? $doc->write('id'); ?>">
				<? $doc->goToFirstUnreadComment(); ?>
				<? $count = 0;
				   while ($comment = $doc->comments()->getNext()) { 
						$comment->output();	
						$count++;
					} ?>
			</div>			
			
			<ul class="content_options">
				<li class="option_reply"><a href="<? $doc->write('permalink'); ?>#reply">Reply</a></li>
				<li class="option_mark_as_read" id="option_mark_as_read_<? $doc->write('id'); ?>">
					<? if ($count < 1) { ?>
						<span class="gray">Nothing new. :(</span>
					<? } else { ?>
						<a href="#markAsRead" data-content="<?= $doc->id; ?>">Mark as Read</a>
					<? } ?>
				</li>
				<li class="option_watching">
					<a href="#toggleFlag" data-flag="watching" data-content="<?= $doc->id; ?>" data-active="Stop Tracking" data-inactive="Start Tracking" title="Track new comments" class="<? if ($doc->hasFlag('watching',$POD->currentUser())){?>active<? } ?>">Stop Tracking</a>
				</li>
			</ul>
		</section>
	<div class="clearer"></div>
</article>