<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/content/output.php
* Default output template for a piece of content
* Use this file as a basis for your custom content templates
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/themes
/**********************************************/
?>
<div class="column_8 structure_only" id="post_output">
	<article>
		<header>
			<h1><a href="<? $doc->write('permalink'); ?>" rel="external" title="<? $doc->write('headline'); ?>"><? $doc->write('headline'); ?></a></h1>
			<? if ($doc->get('privacy')=="friends_only") { ?>
				<span class="privacy friends_only_option">Friends Only</span>
			<? } else if ($doc->get('privacy')=="group_only") { ?>
				<span class="privacy group_only_option">Group Members Only</span>
			<? } else if ($doc->get('privacy')=="owner_only") { ?>
				<span class="privacy owner_only_option">Only you can see this.</span>
			<? } ?>

		</header>
			
			<? if ($doc->get('link')) { ?>
				<p>View Link: <a href="<? $doc->write('link'); ?>"><? $doc->write('link'); ?></a></p>
			<? } ?>		

			<? if ($doc->get('video')) {
				if ($embed = $POD->GetVideoEmbedCode($doc->get('video'),530,400,'true','always')) { 
					echo $embed; 
				} else { ?>
					<p>Watch Video: <a href="<? $doc->write('video'); ?>"><? $doc->write('video'); ?></a></p>
				<? }
			} ?>
			<? if ($img = $doc->files()->contains('file_name','img')) { ?>
				<p class="post_image"><img src="<? $img->write('resized'); ?>" /></p>
			<? } ?>	
			<? if ($doc->get('body')) { 
				$doc->writeFormatted('body');
			} ?>	
											
			<? if ($doc->tags()->count() > 0){ ?>
				<p>
					<img src="<? $POD->templateDir(); ?>/img/tag_pink.png" alt="Tags" align="absmiddle" />
					<? $doc->tags()->output('tag',null,null); ?>
				</p>
			<? } ?>	
	</article>
    <div class="clearer"></div>
	<aside id="post_tools">
				<? if ($POD->isAuthenticated()) {  ?>
                                <div data-role="controlgroup" data-type="horizontal">
				<ul class="post_actions">

					<a href="#toggleFlag" data-ajax="false" data-role="button" data-flag="favorite" data-active="Un-fave" title="Mark as a favorite" data-inactive="Fave" data-content="<?= $doc->id; ?>" class="flag <? if ($doc->hasFlag('favorite',$POD->currentUser())) {?>active<? } ?>">Fave</a>
					<a href="#toggleFlag" data-ajax="false" data-role="button" data-flag="watching" data-active="Tracking" title="Track new comments on the dashboard" data-inactive="Track" data-content="<?= $doc->id; ?>" class="flag <? if ($doc->hasFlag('watching',$POD->currentUser())) {?>active<? } ?>">Track</a>
				
					<? if ($doc->isEditable()) { ?>
						
                                            <a href="<? $doc->write('editlink'); ?>" data-role="button" rel="external" title="Edit this post" class="edit_button">Edit</a>
					
					<? } ?>
				</ul>
                                </div>
			<? } ?>
	</aside>
	<div class="clearer"></div>
	<aside class="padded">
		<div id="comments">
			<!-- COMMENTS -->	
			<? 
			   	while ($comment = $doc->comments()->getNext()) { 
					$comment->output();	
				} 
			?>
			<!-- END COMMENTS -->
		</div>	
		<? $doc->output('comment.form'); ?>
	</aside>
</div>
