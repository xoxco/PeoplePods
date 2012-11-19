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
	<a rel="external" href="<? $user->write('permalink'); ?>" title="View <?= $user->htmlspecialwrite('nick'); ?>'s profile"><img src="<?= $user->avatar(); ?>" height="25" width="25"border="0" /></a>
</aside>
