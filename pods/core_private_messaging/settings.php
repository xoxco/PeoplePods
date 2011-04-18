<?

	$POD->registerPOD('core_private_messaging','Allow members to send messages to one another',array('^inbox$'=>'core_private_messaging/inbox.php','^inbox/conversationwith/(.*)'=>'core_private_messaging/thread.php?username=$1'),array('messagePath'=>'/inbox/conversationwith'));

?>