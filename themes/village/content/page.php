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
			<h1><?php $doc->write('headline'); ?></h1>
		</header>

		<?php $doc->write('body'); ?>
											
		<?php if ($doc->tags()->count() > 0){ ?>
			<p>
				<img src="<?php $POD->templateDir(); ?>/img/tag_pink.png" alt="Tags" align="absmiddle" />
				<?php $doc->tags()->output('tag',null,null); ?>
			</p>
		<?php } ?>	
	</article>	
	<div class="clearer"></div>
</div>
<div class="column_4 structure_only" id="post_info">



</div>


