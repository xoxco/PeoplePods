<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/people/welcome.php
* Used by the dashboard pod to create the homepage of the site for non-members
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/person-object
/**********************************************/
?>
<? 	

	$offset = @$_GET['offset'] ? $_GET['offset'] : 0;
	$recent_posts = $POD->getContents(array('status:!='=>'friends_only'),null,5,$offset);

?>


<div class="column_8">
	<h1>Welcome to <? $POD->siteName(); ?></h1>

	<? $recent_posts->output('short','header','pager','Recent Posts'); ?>
</div>
<div class="column_4 structure_only">
	<? 
	
		$POD->output('sidebars/login');
	
		$POD->output('sidebars/search');

		$POD->output('sidebars/ad_unit');
		
		$POD->output('sidebars/recent_visitors');

	?>
</div>