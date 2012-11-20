<div class="user_short">
	<div class="column_2">
		<div class="column_padding">
			<a href="<?php $user->POD->podRoot(); ?>/admin/people/?id=<?php $user->write('id'); ?>"><?php $user->write('nick'); ?></a>
		</div>	
	</div>
	<div class="column_2">
		<div class="column_padding">
			<?php $user->write('membership'); ?>
		</div>
	</div>
	<div class="column_1 last subItemControls">
		<div class="column_padding">
			<a href="#" onclick="return removeMember(<?php $user->write('id'); ?>);">Remove</a>
		</div>
	</div>
	<div class="clearer"></div>
</div>
