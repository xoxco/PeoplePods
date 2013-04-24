<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/stacks/uc_header.php
* Wrap usercontent stack output from
* core_usercontent
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/stack-output
/**********************************************/
?>
<div class="column_8">

	<section class="stack_output <? if ($title) {?>stack_<?  echo $POD->tokenize($title); } ?>">
		<ul>
			<? if ($title) { ?>
				<li>
					<header>
						<h1><?= $title; ?></h1>
					</header>
				</li>
			<? } ?>
			