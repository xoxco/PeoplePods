<div class="user_short">
	<div class="column_2">
		<div class="column_padding">
			<a href="<? $user->POD->podRoot(); ?>/admin/people/?id=<? $user->write('id'); ?>"><? $user->write('nick'); ?></a>
		</div>	
	</div>
	<div class="column_2">
		<div class="column_padding">
			<? $user->write('membership'); ?>
		</div>
	</div>
	<div class="column_1 last subItemControls">
		<div class="column_padding">
			<a href="#" onclick="return removeMember(<? $user->write('id'); ?>);">Remove</a>
		</div>
	</div>
	<div class="clearer"></div>
</div>
