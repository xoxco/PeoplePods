<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/sidebars/recent_posts.php
* Displays last 5 posts of any type
*
* Use this in other templates:
* $POD->output('sidebars/recent_posts');
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/person-object
/**********************************************/
?><? $recent = $POD->getContents(array('1'=>'1'),'d.date DESC',5); ?>
<div class="sidebar padded" id="recent_visitors_sidebar">
	<h3>Recent Posts</h3>
	<? $recent->output('list_item','ul_header','ul_footer'); ?>
</div>