<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/groups/group_navigator.php
* Used by the groups output.php and the core_groups/index.php fil
* Creates the intra-group navigation on the dashboard
*
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/group-object
/**********************************************/
?>
<li class="group_navigator <?if ($group->get('active')) {?>active<? } ?>"><? $group->permalink(); ?></li>