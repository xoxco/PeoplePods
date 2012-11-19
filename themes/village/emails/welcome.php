<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/emails/welcome.php
* sent to members when they create their accounts.
* this template needs to include logic to check if a verification is required.
* This can be done by checking the verificationKey field on $sender
*
* Define $subject as a variable
* The output of this template is otherwise used as the body of the email
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/themes
/**********************************************/
?>

<?


$subject='Welcome to ' . $sender->POD->siteName(false);

?>

Howdy <? $sender->write('nick'); ?>,

<? if ($sender->get('verificationKey')) { ?>

Before you can post things and leave comments, you must verify your email address.  To do so, click the link below.

<? $sender->POD->siteRoot(); ?>/verify?key=<? $sender->write('verificationKey'); ?>

Your confirmation code is: <? $sender->write('verificationKey'); ?>
<? } ?>

Welcome to <? $sender->POD->siteName(); ?>.  The first thing you should do is update your profile and add a profile image!

<? $sender->POD->siteRoot(); ?>/editprofile

Love,
<? $sender->POD->siteName(); ?>


You can update your account here:
<? $sender->POD->siteRoot(); ?>/editprofile