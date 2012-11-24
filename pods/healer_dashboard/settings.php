<?php 
//@todo must settle path issues and rounting as this path is the same as the regular dashboard
$POD->registerPOD("healer_dashboard",'Fancy posting and subscribing dashboard',array("^healer$"=>"healer_dashboard/index.php",'^healer_replies'=>'healer_dashboard/index.php?replies=1'),array());

?>