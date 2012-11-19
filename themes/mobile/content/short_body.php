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
						<span class="content_author"><? $doc->author()->permalink(); ?></span> posted (<span class="content_time"><? echo $doc->write('timesince'); ?></span>)
					</span>
					<h1><a href="<? $doc->write('permalink'); ?>" title="<? $doc->write('headline'); ?>" rel="external"><? $doc->write('headline'); ?></a></h1>
				</header>
				
				<? if ($doc->get('video')) {
					if ($embed = $POD->GetVideoEmbedCode($doc->get('video'),520,400,'true','always')) { 
						echo $embed; 
					} else { ?>
						<p>Watch Video: <a href="<? $doc->write('video'); ?>"><? $doc->write('video'); ?></a></p>
					<? }
				} ?>	

				<? if ($img = $doc->files()->contains('file_name','img')) { ?>
					<p class="content_image"><a href="<? $doc->write('permalink'); ?>"><img src="<? $img->write('thumbnail') ?>" border="0" /></a></p>
				<? } ?>			


				<? if ($doc->get('link')) { ?>
					<p>View Link: <a href="<? $doc->write('link'); ?>"><? $doc->write('link'); ?></a></p>
				<? } ?>		

				<? if ($doc->get('body')) { 
					$doc->writeFormatted('body');
				} ?>
				
				<div class="clearer"></div>

				<ul class="content_options">
					<li class="comments_option">
						<a href="<? $doc->write('permalink'); ?>" rel="external"><?  if ($doc->comments()->totalCount() > 0) {  echo $doc->comments()->totalCount() . " comments"; } else { echo "No comments"; } ?></a>
					</li>
					<? if ($doc->POD->isAuthenticated()) { ?>
						<li class="watching_option">
							<a href="#toggleFlag" data-ajax="false" data-flag="watching" data-active="Stop tracking" title="Track new comments on the dashboard" data-inactive="Track" data-content="<?= $doc->id; ?>" class="trackingLink <? if ($doc->hasFlag('watching',$POD->currentUser())) {?>active<? } ?>">Track</a>
						</li>
					<? } ?>				
					<? if ($doc->get('privacy')=="friends_only") { ?>
						<li class="friends_only_option">Friends Only</li>
					<? } else if ($doc->get('privacy')=="group_only") { ?>
						<li class="group_only_option">Group Members Only</li>
					<? } else if ($doc->get('privacy')=="owner_only") { ?>
						<li class="owner_only_option">Only you can see this.</li>
					<? } ?>
					<? if ($doc->isEditable()) { ?>
						<li class="delete_option">
							<a href="#deleteContent" data-ajax="false" data-content="<?= $doc->id; ?>">Delete</a>
						</li>
					<? } ?>
				</ul>
		</article>
