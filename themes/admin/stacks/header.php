<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/stacks/header.php
* Default header used by $stack->output()
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/stack-output
/**********************************************/
?>
<div class="stack_output <?php if ($title) {?>stack_<?php  echo $POD->tokenize($title); } ?>">
	<?php if ($title) { ?>
		<h2 class="stack_header"><?php echo $title; ?></h2>
	<?php } ?>