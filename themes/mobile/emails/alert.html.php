<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/emails/alert.php
* Defines the email that is sent when a user receives an alert message
*
* Define $subject as a variable
* The output of this template is otherwise used as the body of the email
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/themes
/**********************************************/
?>
<? 

$subject= 'There has been activity on your account at ' . $POD->siteName(false);
?>
<html>
<head>
	
</head>
<body>

<?= $message; ?>

</body>
</html>