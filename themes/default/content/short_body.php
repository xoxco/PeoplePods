<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/content/short_body.php
* Defines the body output as included by short.php
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/themes
/**********************************************/
?>
		<article class="attributed_content content_body">
				<header>
					<span class="content_meta">
						<span class="content_author"><?php $doc->author()->permalink(); ?></span> posted (<span class="content_time"><?php echo $doc->write('timesince'); ?></span>)
					</span>
					<h1><a href="<?php $doc->write('permalink'); ?>" title="<?php $doc->write('headline'); ?>"><?php $doc->write('headline'); ?></a></h1>
				</header>
				
				<?php if ($doc->get('video')) {
					if ($embed = $POD->GetVideoEmbedCode($doc->get('video'),530,400,'true','always')) { 
						echo $embed; 
					} else { ?>
						<p>Watch Video: <a href="<?php $doc->write('video'); ?>"><?php $doc->write('video'); ?></a></p>
					<?php }
				} ?>	

				<?php if ($img = $doc->files()->contains('file_name','img')) { ?>
					<p class="content_image"><a href="<?php $doc->write('permalink'); ?>"><img src="<?php $img->write('resized') ?>" /></a></p>
				<?php } ?>			


				<?php if ($doc->get('link')) { ?>
					<p>View Link: <a href="<?php $doc->write('link'); ?>"><?php $doc->write('link'); ?></a></p>
				<?php } ?>		

				<?php if ($doc->get('body')) { 
					$doc->writeFormatted('body');
				} ?>
				
				<div class="clearer"></div>

				<ul class="content_options">
					<li class="comments_option">
						<a href="<?php $doc->write('permalink'); ?>"><?php  if ($doc->comments()->totalCount() > 0) {  echo $doc->comments()->totalCount() . " comments"; } else { echo "No comments"; } ?></a>
					</li>
					<?php if ($doc->POD->isAuthenticated()) { ?>
						<li class="watching_option">
							<a href="#toggleFlag" data-flag="watching" data-active="Stop tracking" title="Track new comments on the dashboard" data-inactive="Track" data-content="<?php echo $doc->id; ?>" class="trackingLink <?php if ($doc->hasFlag('watching',$POD->currentUser())) {?>active<?php } ?>">Track</a>
						</li>
					<?php } ?>				
					<?php if ($doc->get('privacy')=="friends_only") { ?>
						<li class="friends_only_option">Friends Only</li>
					<?php } else if ($doc->get('privacy')=="group_only") { ?>
						<li class="group_only_option">Group Members Only</li>
					<?php } else if ($doc->get('privacy')=="owner_only") { ?>
						<li class="owner_only_option">Only you can see this.</li>
					<?php } ?>
					<?php if ($doc->isEditable()) { ?>
						<li class="delete_option">
							<a href="#deleteContent" data-content="<?php echo $doc->id; ?>">Delete</a>
						</li>
					<?php } ?>
				</ul>
		</article>
