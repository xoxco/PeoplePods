<?

	$POD->registerPOD('core_profiles','Give each member a personal profile',array('^people/(.*)'=>'core_profiles/profile.php?username=$1','^editprofile'=>'core_profiles/editprofile.php'),array('profilePath'=>'/people'));

?>