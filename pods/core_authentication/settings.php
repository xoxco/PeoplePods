<? 

	$POD->registerPOD('core_authentication_login','Allows Login, Logout, Password Reset',array('^login'=>'core_authentication/login.php','^logout'=>'core_authentication/logout.php','^password_reset/(.*)'=>'core_authentication/password.php?resetCode=$1','^password_reset$'=>'core_authentication/password.php'),array());
	$POD->registerPOD('core_authentication_creation','Allows new members to join your site',array('^join'=>'/core_authentication/join.php','^verify'=>'core_authentication/verify.php'),array());

?>