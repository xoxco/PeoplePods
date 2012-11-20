<div class="user_short">
	<div class="column_2">
		<div class="column_padding">
			<a href="<?php $user->POD->podRoot(); ?>/admin/people/?id=<?php $user->write('id'); ?>"><?php $user->write('nick'); ?></a>
		</div>	
	</div>
	<div class="column_2">
		<div class="column_padding">
			<select id="member_type_<?php $user->write('id'); ?>"><option value="member">Member</option><option value="invitee">Invitee</option><option value="manager">Manager</option><option value="owner">Owner</option></select>
		</div>
	</div>
	<div class="column_1 last subItemControls">
		<div class="column_padding">
			<a href="#" onclick="return addMember(<?php $user->write('id'); ?>);">Add</a>
		</div>
	</div>
	<div class="clearer"></div>
</div>
