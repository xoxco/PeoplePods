<?php 

$POD->registerPOD("core_patients",'Display list of patients, followers, recommended friends',array("^patients$"=>"core_patients/index.php","^patients/(.*)"=>'core_patients/index.php?mode=$1'),array());

?>