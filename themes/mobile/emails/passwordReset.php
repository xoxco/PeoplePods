<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/emails/passwordReset.php
* sent to a person who has requested a password reset.
*
* Define $subject as a variable
* The output of this template is otherwise used as the body of the email
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/themes
/**********************************************/
?>
<?php
$subject='Reset Your Password on ' . $sender->POD->siteName(false);;
?>
Hey <?php $sender->write('nick') ?>,

We received a request to reset your password.  Click below to do so.

<?php $sender->POD->siteRoot(); ?>/password_reset/<?php $sender->write('passwordResetCode'); ?>


If you didn't request this password reset code, you can safely ignore this email.

Love,
<?php $sender->POD->siteName(); ?>


You can update your account here:
<?php $sender->POD->siteRoot(); ?>/editprofile