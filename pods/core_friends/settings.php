<?php 

$POD->registerPOD("core_friends",'Display list of friends, followers, recommended friends',array("^friends$"=>"core_friends/index.php","^friends/(.*)"=>'core_friends/index.php?mode=$1'),array());

?>