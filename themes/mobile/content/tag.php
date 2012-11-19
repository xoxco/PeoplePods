<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/content/tag.php
* Default output template for tags
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/themes
/**********************************************/
?>

<a href="<? $tag->POD->siteRoot(); ?>/lists/tags/<? $tag->write('value'); ?>" title="See posts tagged <? $tag->write('value'); ?>" rel="tag" class="tag"><? $tag->write('value'); ?></a>&nbsp;