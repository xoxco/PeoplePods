<li>
	<a href="<?php $POD->podRoot(); ?>/admin/content/?id=<?php echo $doc->id; ?>"><?php echo $doc->headline; ?></a><br />
	<span class="smaller">
		a <strong><?php echo $doc->type; ?></strong> 
		created by <a href="<?php $POD->podRoot(); ?>/admin/people/?id=<?php echo $doc->author()->id; ?>"><?php echo $doc->author()->nick; ?></a>
		<?php echo $POD->timesince($doc->minutes); ?>
	</span>
</li>