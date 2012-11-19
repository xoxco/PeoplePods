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
<section class="stack_output <? if ($title) {?>stack_<?  echo $POD->tokenize($title); } ?>">
<ul>
	<? if ($title) { ?>
		<header>
			<h1><?= $title; ?></h1>
		</header>
	<? } ?>