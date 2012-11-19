<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/messages/thread_short.php
* Default output template for a thread in the inbox
*
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/messaging
/**********************************************/
?>
<div class="thread_short <? if ( $thread->unreadCount() > 0) { ?> thread_unread<? } ?>" onclick="window.location='<? $thread->write('permalink'); ?>';">
	<? $thread->recipient()->output('avatar'); ?>
	<div class="column_2">
		<div class="column_padding">
			<a href="<? $thread->write('permalink'); ?>"><? $thread->recipient()->write('nick'); ?></a>
		</div>
	</div>
	<div class="column_2">
		<div class="column_padding">
			<? echo $thread->POD->timesince($this->get('minutes')); ?>
		</div>
	</div>
	<div class="column_2 right_align">
		<div class="column_padding">
			<? echo $thread->unreadCount(); ?> unread
		</div>
	</div>
	<div class="column_5 last"> 
		<div class="column_padding">
	<? 
		$thread->messages()->sortBy('date',false);
		$first = $thread->messages()->getNext(); 
		if ($first) { 
			$first->from()->write('nick');
			echo " says, \"";
			echo $first->get_short('message',200);
			echo "\"&nbsp;&nbsp;&nbsp;<a href=\"" . $thread->get('permalink') ."\">Read More</a>";
		}
	?>
 		</div>
 	</div>
	<div class="clearer"></div>
</div>
