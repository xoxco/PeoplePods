<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/people/member_friends.php
* Creates a summary of the users friends and followers.
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/person-object
/**********************************************/
?>
<li class="dashboard_navigator sub_option">
	You are following <a href="<? $POD->siteRoot(); ?>/friends"><? echo $user->friends()->totalCount();  echo " " . $POD->pluralize($user->friends()->totalCount(),'person','people'); ?></a>
</li>
<li class="dashboard_navigator sub_option">
	<a href="<? $POD->siteRoot(); ?>/friends/followers"><? echo $user->followers()->totalCount(); echo " " . $POD->pluralize($user->followers()->totalCount(),'person is','people are'); ?></a> following you.
</li>
<li class="dashboard_navigator sub_option">
	<a href="<? $POD->siteRoot(); ?>/friends/recommended">Meet some new people &#187;</a>
</li>
