	<div id="tools">
		<ul>
			<li id="section_name">Flags</li><?php if ($user) { ?><li><A href="<?php $POD->podRoot(); ?>/admin/people/?id=<?php $user->write('id'); ?>">&larr; Back to <?php $user->write('nick'); ?></a></li><?php } ?><?php if ($content) { ?><li><A href="<?php $POD->podRoot(); ?>/admin/content/?id=<?php $content->write('id'); ?>">&larr; Back to <?php $content->write('headline'); ?></a></li><?php } ?>
		</ul>
	</div>