<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/content/short.php
* Default short template for content.
* Used by core_usercontent/list.php
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/themes
/**********************************************/
?>	<li class="content_short content_<?php $doc->write('type'); ?> <?php if ($doc->get('isOddItem')) {?>odd<?php } ?> <?php if ($doc->get('isEvenItem')) {?>even<?php } ?> <?php if ($doc->get('isLastItem')) {?>last<?php } ?> <?php if ($doc->get('isFirstItem')) {?>first<?php } ?>" id="content<?php $doc->write('id'); ?>">	
		<?php $doc->author()->output('avatar'); ?>
		<?php $doc->output('short_body'); ?>
		<div class="clearer"></div>
	</li>
