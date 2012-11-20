<?php 
//@todo must settle path issues and rounting as this path is the same as the regular dashboard
$POD->registerPOD("doctor_dashboard",'Fancy posting and subscribing dashboard',array("^healer$"=>"doctor_dashboard/index.php",'^replies'=>'doctor_dashboard/index.php?replies=1'),array());

?>