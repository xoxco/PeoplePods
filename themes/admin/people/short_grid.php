<tr class="<? if ($this->isEvenItem) {?>even<? } else { ?>odd<? } ?>">
	<td valign="top" align="left" width="50">
		<? if ($img = $user->files()->contains('file_name','img')) { ?>
			<a href="<? $POD->podRoot(); ?>/admin/people/?id=<? $user->write('id'); ?>"><img src="<?= $img->src(50,true); ?>" border="0" height="50" width="50"  /></a>
		<? } else { ?>
			<a href="<? $POD->podRoot(); ?>/admin/people/?id=<? $user->write('id'); ?>"><img src="<? $user->POD->templateDir(); ?>/img/noimage.png" border="0" /></a>
		<? } ?> 	
	</td>
	<td valign="top" align="left">
		<a href="<? $POD->podRoot(); ?>/admin/people/?id=<? $user->write('id'); ?>"  title="View this person's account details"><? $user->write('nick'); ?></a>
	</td>
	<td valign="top" align="left">
		<? 
		// check lastVisit format. might be a timestamp, might be a datetime
		if (is_numeric($user->lastVisit)) {
			echo $POD->timesince(intval(time() - $user->get('lastVisit')) / 60);
		} else {
			echo $POD->timesince(intval(time() - strtotime($user->get('lastVisit'))) / 60);		
		} ?>
	</td>	
	<td valign="top" align="left">
		<?= date('F d, Y',strtotime($user->get('memberSince'))); ?>
	</td>	
	<td valign="top" align="left">
		<? if ($user->get('verificationKey')) { ?>Unverified<? } else { ?>Verified<? } ?>
	</td>	
</tr>