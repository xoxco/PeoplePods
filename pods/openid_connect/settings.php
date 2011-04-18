<?php 

$POD->registerPOD("openid_connect",'Add an OpenID account',array(
"^openid$"=>"openid_connect/index.php",
"^openid/(.*)"=>"openid_connect/index.php?mode=$1",
),array());

?>