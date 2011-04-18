<?php 

$POD->registerPOD("core_groups",'Member created groups',array("^groups$"=>"core_groups/index.php","^groups/(.*)/(.*)"=>'core_groups/group.php?stub=$1&command=$2',"^groups/(.*)"=>'core_groups/group.php?stub=$1'),array('groupPath'=>'groups'));

?>