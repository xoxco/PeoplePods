	<div id="tools">
		<ul>
			<li id="section_name">Flags</li><? if ($user) { ?><li><A href="<? $POD->podRoot(); ?>/admin/people/?id=<? $user->write('id'); ?>">&larr; Back to <? $user->write('nick'); ?></a></li><? } ?><? if ($content) { ?><li><A href="<? $POD->podRoot(); ?>/admin/content/?id=<? $content->write('id'); ?>">&larr; Back to <? $content->write('headline'); ?></a></li><? } ?>
		</ul>
	</div>