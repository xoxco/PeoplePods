<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/messages/message.php
* Default output template for a single private message
*
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/messaging
/**********************************************/
?>
<div class="message">
	<? $message->from()->output('avatar'); ?>
	<div class="attributed_content">
		<? 	echo $message->from()->get('nick') . " said, (<a href=\"#" . $message->get('id') . "\">" . $this->POD->timesince($message->get('minutes')) . "</a>)"; ?>
		<?= $message->writeFormatted('message'); ?>
	</div>
	<div class="clearer"></div>
</div>
