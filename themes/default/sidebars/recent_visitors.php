<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/sidebars/recent_visitors.php
* Displays last 5 visitors
*
* Use this in other templates:
* $POD->output('sidebars/recent_visitors');
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/person-object
/**********************************************/
?><? $recent = $POD->getPeople(array('1'=>'1'),'u.lastVisit DESC',5); ?>
<div class="sidebar padded" id="recent_visitors_sidebar">
	<h3>Recent Visitors</h3>
	<? $recent->output('list_item','ul_header','ul_footer'); ?>
</div>