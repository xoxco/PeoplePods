<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/people/avatar.php
* Default avatar template for a person
* Used in various places where only the user's picture is needed
*
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/person-object
/**********************************************/
?>
<aside class="person_avatar">
	<a href="<?php $user->write('permalink'); ?>" title="View <?php echo $user->htmlspecialwrite('nick'); ?>'s profile"><img src="<?php echo $user->avatar(); ?>" alt="<?php $user->htmlspecialwrite('nick'); ?>" /></a>
</aside>
