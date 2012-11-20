<?php if ($POD->isAuthenticated()) { ?>
	<?php $activity = $POD->currentUser()->getActivityStream(); ?>
	<div class="sidebar padded" id="activity_stream_sidebar">
		<h3>Recent Activity</h3>
		<ul>
			<?php $activity->output('output'); ?>
		</ul>
	</div>
<?php } ?>