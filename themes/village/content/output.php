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
			<h1><a href="<?php $doc->write('permalink'); ?>" title="<?php $doc->write('headline'); ?>"><?php $doc->write('headline'); ?></a></h1>
			<?php if ($doc->get('privacy')=="friends_only") { ?>
				<span class="privacy friends_only_option">Friends Only</span>
			<?php } else if ($doc->get('privacy')=="group_only") { ?>
				<span class="privacy group_only_option">Group Members Only</span>
			<?php } else if ($doc->get('privacy')=="owner_only") { ?>
				<span class="privacy owner_only_option">Only you can see this.</span>
			<?php } ?>

		</header>
			
			<?php if ($doc->get('link')) { ?>
				<p>View Link: <a href="<?php $doc->write('link'); ?>"><?php $doc->write('link'); ?></a></p>
			<?php } ?>		

			<?php if ($doc->get('video')) {
				if ($embed = $POD->GetVideoEmbedCode($doc->get('video'),530,400,'true','always')) { 
					echo $embed; 
				} else { ?>
					<p>Watch Video: <a href="<?php $doc->write('video'); ?>"><?php $doc->write('video'); ?></a></p>
				<?php }
			} ?>
			<?php if ($img = $doc->files()->contains('file_name','img')) { ?>
				<p class="post_image"><img src="<?php $img->write('resized'); ?>" /></p>
			<?php } ?>	
			<?php if ($doc->get('body')) { 
				$doc->writeFormatted('body');
			} ?>	
											
			<?php if ($doc->tags()->count() > 0){ ?>
				<p>
					<img src="<?php $POD->templateDir(); ?>/img/tag_pink.png" alt="Tags" align="absmiddle" />
					<?php $doc->tags()->output('tag',null,null); ?>
				</p>
			<?php } ?>	
	</article>	
	<aside id="post_tools">
				<?php if ($POD->isAuthenticated()) {  ?>
				<ul class="post_actions">

					<li><a href="#toggleFlag" data-flag="favorite" data-active="Un-fave" title="Mark as a favorite" data-inactive="Fave" data-content="<?php echo $doc->id; ?>" class="flag <?php if ($doc->hasFlag('favorite',$POD->currentUser())) {?>active<?php } ?>">Fave</a></li>				
					<li><a href="#toggleFlag" data-flag="watching" data-active="Tracking" title="Track new comments on the dashboard" data-inactive="Track" data-content="<?php echo $doc->id; ?>" class="flag <?php if ($doc->hasFlag('watching',$POD->currentUser())) {?>active<?php } ?>">Track</a></li>
				
					<?php if ($doc->isEditable()) { ?>
						<li>
							<a href="<?php $doc->write('editlink'); ?>" title="Edit this post" class="edit_button">Edit</a>
						</li>
					<?php } ?>
				</ul>
			<?php } ?>
	</aside>
	<div class="clearer"></div>
	<aside class="padded">
		<div id="comments">
			<!-- COMMENTS -->	
			<?php 
			   	while ($comment = $doc->comments()->getNext()) { 
					$comment->output();	
				} 
			?>
			<!-- END COMMENTS -->
		</div>	
		<?php $doc->output('comment.form'); ?>
	</aside>
</div>
<div class="column_4 structure_only" id="post_info">

	<?php $doc->author()->output('author_info'); ?>
	
	<section class="post_stream_navigation">
		<header>
		<p id="post_date">
			Posted on <?php echo date_format(date_create($doc->get('date')),'l, M jS'); ?>
			(<?php $doc->write('timesince'); ?>)
		</p>	
		</header>
		<?
			$previous = $POD->getContents(array('userId'=>$doc->author('id'),'id:lt'=>$doc->get('id')),'d.id DESC',1);
			if ($previous->success() && $previous->count() > 0) { 
				$previous = $previous->getNext();
				?>
				<a href="<?php $previous->write('permalink');?>" class="post_previous"><strong>&#171;&nbsp;Previous</strong> <?php echo $POD->shorten($previous->get('headline'),100); ?></a>
		<?php } 				

			$next = $POD->getContents(array('userId'=>$doc->author('id'),'id:gt'=>$doc->get('id')),'d.id ASC',1);	
			if ($next->success() && $next->count() > 0) {
				$next = $next->getNext(); 
			?>
				<a href="<?php $next->write('permalink');?>" class="post_next"><strong>&#187;&nbsp;Next</strong> <?php  echo $POD->shorten($next->get('headline'),80); ?></a>
		<?php }  else { ?>
			<span class="post_next"><strong>&#187;&nbsp;Next</strong> This is <?php echo $doc->author('nick'); ?>'s most recent post</span>
		<?php } ?>
		<div class="clearer"></div>
	</section>

	<?php if ($doc->group()) {
		if ($POD->isAuthenticated()) {
			$member = $doc->group()->isMember($POD->currentUser());
		}

		?>
		<section class="post_stream_navigation">
			<header>
				<p>This is part of <?php $doc->group()->permalink(); ?>.</p>
				<?php if ($POD->isAuthenticated() && ($member == "manager" || $member=="owner")) { ?>
					<p class="highlight">
						<strong>You are a manager of this group.</strong><br />
						<a href="<?php $doc->group()->write('permalink'); ?>/remove?docId=<?php $doc->write('id'); ?>">Remove this post from the group</a></p>
				<?php } ?>
			</header>
			<?
				$previous = $POD->getContents(array('groupId'=>$doc->group('id'),'id:lt'=>$doc->get('id')),'d.id DESC',1);
				if ($previous->success() && $previous->count() > 0) { 
					$previous = $previous->getNext();
					?>
					<a href="<?php $previous->write('permalink');?>"  class="post_previous"><strong>&#171;&nbsp;Previous</strong> <?php echo $POD->shorten($previous->get('headline'),100); ?></a>
			<?php } ?>
			<?
				$next = $POD->getContents(array('groupId'=>$doc->group('id'),'id:gt'=>$doc->get('id')),'d.id ASC',1);	
				if ($next->success() && $next->count() > 0) {
					$next = $next->getNext(); 
				?>
					<a href="<?php $next->write('permalink');?>" class="post_next"><strong>&#187;&nbsp;Next</strong> <?php  echo $POD->shorten($next->get('headline'),80); ?></a>
			<?php }  else { ?>
				<span class="post_next">
					<strong>&#187;&nbsp;Next</strong> This is the most recent post in <?php $doc->group()->write('groupname'); ?>.
				</span>
			<?php } ?>
			<div class="clearer"></div>
		</section>
	<?php } ?>
	
			
	<section id="watchers">
			<?php  
				$watching = $POD->getPeopleByWatching($doc); 
				if ($watching->totalCount() > 0) {
					$watching->output('short','header','footer',$watching->totalCount() . $POD->pluralize($watching->totalCount(),' Person Tracking',' People Tracking')); 
				}
			?>
	</section>

</div>


