<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/emails/invite.php
* This email is sent when a member sends an invite
* This email takes several forms:
* Email to non-member inviting to join the site
* Email to non-member inviting to join a group
* Email to member inviting to join a group
* (Emails to members inviting to join the site are converted to friend-adds)
*
* Define $subject as a variable
* The output of this template is otherwise used as the body of the email
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/themes
/**********************************************/
?>
<?

if (isset($group)) { 
	$subject = $sender->get('nick') . ' wants you to join ' . $group->get('groupname') . ' on ' . $sender->POD->siteName(false);
} else {
	$subject = $sender->get('nick') . ' wants you to join ' . $sender->POD->siteName(false);
}

if (isset($code)) {  // this version will be sent to non-members ?>

Hello!

<? if (isset($group)) { ?>
<? $sender->write('nick'); ?> invited you to join a group on <? $sender->POD->siteName(); ?>.  The group is called <? $group->write('groupname'); ?>.
<? } else { ?>
<? $sender->write('nick'); ?> invited you to join <? $sender->POD->siteName(); ?>.
<? } ?>

<? $sender->write('nick'); ?> sent you this message:
------------------------------------------------------------

<? echo $message; ?> 

------------------------------------------------------------

Click below to accept the invitation:
<? $sender->POD->siteRoot(); ?>/join?code=<? echo $code; ?>

<? if (isset($group)) { ?>
Learn more about this group here:
<? $group->write('permalink'); ?>


<? } ?>

Love,
<? $sender->POD->siteName(); ?>

<? } else { // this version will be sent to members ?>
Hello!

<? $sender->write('nick'); ?> invited you to join a group on <? $sender->POD->siteName(); ?>.  The group is called <? $group->write('groupname'); ?>.

<? $sender->write('nick'); ?> sent you this message:
------------------------------------------------------------

<? echo $message; ?> 

------------------------------------------------------------

Learn more about this group here and accept your invitation here:
<? $group->write('permalink'); ?>


Love,
<? $sender->POD->siteName(); ?>


You can update your account here:
<? $sender->POD->siteRoot(); ?>/editprofile
<? } ?>


