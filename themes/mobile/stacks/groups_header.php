<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/stacks/groups_header.php
* Header used in core_groups to create the /groups page
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/stack-output
/**********************************************/
?>	

<section class="stack_output <? if ($title) {?>stack_<?  echo $POD->tokenize($title); } ?>">
<ul id="group_list">
	<? if ($this->count() > 0) { ?>	
		<header>
			<label class="group_name">Group</label>
			<label class="group_description">Description</label>
			<label class="group_list">Members</label>
		</header>
	<? } ?>