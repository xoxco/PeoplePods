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
<section class="stack_output <?php if ($title) {?>stack_<?php  echo $POD->tokenize($title); } ?>">
<ul>
	<?php if ($title) { ?>
		<li>
		<header>
			<h1><?php echo $title; ?></h1>
		</header>
		</li>
	<?php } ?>