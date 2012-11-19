<? /*
 	Always available:

	$activity - object representing the activity stream post
	$activity->author() - Person object representing the creator of this action stream

	May be available depending on the content of the activity:

	$targetUser - Person object representing the target user of this action
	$targetContent - Object (not necessarily content) that represents the target of the action.  Check $targetContent->TYPE
	$resultContent - Object (not necessarily content) that represents the result of the action.  Check $resultContent->TYPE

*/ ?>
<li>
	<span class="timestamp"><?= $POD->timesince($activity->minutes); ?></span><span class="action"><?= $activity->formatMessage(); ?></span>
	<div class="clearer"></div>
</li>