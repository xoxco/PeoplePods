<?php 

$POD->registerPOD("village",'Specialized relationships within the software',array("^villages$"=>"villages/index.php","^villages/(.*)/(.*)"=>'villages/village.php?stub=$1&command=$2',"^villages/(.*)"=>'villages/village.php?stub=$1'),array('groupPath'=>'villages'));

?>