<? if ($POD->isAuthenticated()) { ?>
	<? $activity = $POD->currentUser()->getActivityStream(); ?>
	<div class="sidebar padded" id="activity_stream_sidebar">
		<h3>Recent Activity</h3>
		<ul>
			<? $activity->output('output'); ?>
		</ul>
	</div>
<? } ?>