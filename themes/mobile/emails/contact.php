<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/emails/contact.php
* This email is sent when a member sends another member a private message
* This email is sent to the member who receives the message
*
* Define $subject as a variable
* The output of this template is otherwise used as the body of the email
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/themes
/**********************************************/
?>
<?

$subject= 'New message from ' . $sender->get('nick') . ' on ' . $sender->POD->siteName(false);

?>

<? $sender->write('nick'); ?> sent you this message:
------------------------------------------------------------

<? echo $message; ?> 

------------------------------------------------------------

You can reply by clicking below:
<? $sender->POD->siteRoot(); ?>/inbox/conversationwith/<? $sender->write('safe_nick'); ?>


Love,
<? $sender->POD->siteName(); ?>


You can update your account here:
<? $sender->POD->siteRoot(); ?>/editprofile