<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/stacks/ul_footer.php
* Header used to create lists
* (Used in connection with the content/list_item.php and people/list_item.php
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/stack-output
/**********************************************/
?>
<ul class="stack_output <? if ($title) {?>stack_<? echo $POD->tokenize($title); } ?>">