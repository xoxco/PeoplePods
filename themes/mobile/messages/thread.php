<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/messages/thread.php
* Default output template for a thread page
*
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/messaging
/**********************************************/
?>
<div class="column_8" id="thread">
	<h1><a href="<?php $thread->POD->siteRoot(); ?>/inbox">Inbox</a> &#187; Conversation with <?php $thread->recipient()->permalink(); ?></h1>
	<div class="message">
		<?php $POD->currentUser()->output('avatar'); ?>
		<div class="attributed_content">
			<form method="post" action="<?php $thread->write('permalink'); ?>" class="valid column_padding" id="send_message">
				<input name="thread" type="hidden" value="<?php $thread->write('id'); ?>" />
				<textarea name="message" id="message" class="required"></textarea>	
				<input type="Submit" value="Send" />
			</form>
		</div>
		<div class="clearer"></div>
	</div>
	<?php $thread->messages()->output('message','header','footer',null,'Write the first message, and it will appear here.'); ?>
</div>
<div class="column_4 structure_only">
	<?php $thread->recipient()->output('member_info'); ?>
	<h3 style="text-align:center;">vs</h3>
	<?php $POD->currentUser()->output('member_info'); ?>
	<div class="column_padding">
		<a href="?clear=<?php $thread->write('id'); ?>" onclick="return confirm('Clearing this conversation will delete all the messages so far.  Do you really want to delete these messages?');">Clear this conversation</a>
	</div>
</div>