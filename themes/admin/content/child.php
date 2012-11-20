	<div class="doc_short">
		<div class="column_4">
			<div class="column_padding">
				<a href="<?php $doc->POD->podRoot(); ?>/admin/content/?id=<?php $doc->write('id'); ?>"><?php echo $doc->get_short('headline',55); ?></a>
				<span class="preview">by <a href="<?php $doc->POD->podRoot(); ?>/admin/people/?id=<?php $doc->author()->write('id'); ?>" /><?php $doc->author()->write('nick'); ?></a></span>
			</div>
		</div>
		<div class="column_1 last subItemControls">
			<div class="column_padding">
				<a href="#" onclick="return removeChildDoc(<?php $doc->write('id'); ?>);">Remove</a>
			</div>
		</div>
		<div class="clearer"></div>
	</div>
