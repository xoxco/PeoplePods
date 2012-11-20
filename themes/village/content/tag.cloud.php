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

<a href="<?php $tag->POD->siteRoot(); ?>/lists/tags/<?php $tag->write('value'); ?>" title="See posts tagged <?php $tag->write('value'); ?>" rel="tag" class="tag"><?php $tag->write('value'); ?> (<?php echo $tag->contentCount(); ?>)</a>&nbsp;