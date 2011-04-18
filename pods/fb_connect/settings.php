<?php 



$path = dirname(__FILE__);



$POD->registerPOD("fb_connect",'new style fb connect with oauth',array(
"^facebook$"=>"fb_connect/index.php",
"^facebook/friends"=>"fb_connect/friends.php",
"^facebook/(.*)"=>'fb_connect/index.php?mode=$1',
"^xd_receiver.htm"=>"fb_connect/xd_receiver.htm",
),array(),
$path . '/methods.php',
'fb_connect_settings'
);

?>