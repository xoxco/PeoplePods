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

<?php if (isset($group)) { ?>
<?php $sender->write('nick'); ?> invited you to join a group on <?php $sender->POD->siteName(); ?>.  The group is called <?php $group->write('groupname'); ?>.
<?php } else { ?>
<?php $sender->write('nick'); ?> invited you to join <?php $sender->POD->siteName(); ?>.
<?php } ?>

<?php $sender->write('nick'); ?> sent you this message:
------------------------------------------------------------

<?php echo $message; ?> 

------------------------------------------------------------

Click below to accept the invitation:
<?php $sender->POD->siteRoot(); ?>/join?code=<?php echo $code; ?>

<?php if (isset($group)) { ?>
Learn more about this group here:
<?php $group->write('permalink'); ?>


<?php } ?>

Love,
<?php $sender->POD->siteName(); ?>

<?php } else { // this version will be sent to members ?>
Hello!

<?php $sender->write('nick'); ?> invited you to join a group on <?php $sender->POD->siteName(); ?>.  The group is called <?php $group->write('groupname'); ?>.

<?php $sender->write('nick'); ?> sent you this message:
------------------------------------------------------------

<?php echo $message; ?> 

------------------------------------------------------------

Learn more about this group here and accept your invitation here:
<?php $group->write('permalink'); ?>


Love,
<?php $sender->POD->siteName(); ?>


You can update your account here:
<?php $sender->POD->siteRoot(); ?>/editprofile
<?php } ?>


