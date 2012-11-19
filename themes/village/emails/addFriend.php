<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/emails/addFriend.php
* Defines the email that is sent when one user adds another as a friend
* This email is sent to the new friend
*
* Define $subject as a variable
* The output of this template is otherwise used as the body of the email
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/themes
/**********************************************/
?>
<? 

$subject= $sender->get('nick') ." is following you on " . $sender->POD->siteName(false);

?>

<? $sender->write('nick'); ?> has added you as a friend on <? $sender->POD->siteName(); ?>.

You can check them out here:

=> <? $sender->write('permalink'); ?>


Love,
<? $sender->POD->siteName(); ?>


You can update your account here:
<? $sender->POD->siteRoot(); ?>/editprofile