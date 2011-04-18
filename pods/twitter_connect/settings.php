<?php 



$path = dirname(__FILE__);



$POD->registerPOD("twitter_connect",'Add a Twitter account',array(
"^twitter$"=>"twitter_connect/index.php",
"^twitter/friends$"=>"twitter_connect/friends.php",
"^twitter/(.*)"=>'twitter_connect/index.php?mode=$1',

),array(),
$path . '/methods.php',
'twitter_connect_settings'
);

?>