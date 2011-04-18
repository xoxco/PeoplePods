<li>
	<a href="<? $POD->podRoot(); ?>/admin/content/?id=<?= $doc->id; ?>"><?= $doc->headline; ?></a><br />
	<span class="smaller">
		a <strong><?= $doc->type; ?></strong> 
		created by <a href="<? $POD->podRoot(); ?>/admin/people/?id=<?= $doc->author()->id; ?>"><?= $doc->author()->nick; ?></a>
		<?= $POD->timesince($doc->minutes); ?>
	</span>
</li>