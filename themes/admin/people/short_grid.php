<tr class="<?php if ($this->isEvenItem) {?>even<?php } else { ?>odd<?php } ?>">
	<td valign="top" align="left" width="50">
		<?php if ($img = $user->files()->contains('file_name','img')) { ?>
			<a href="<?php $POD->podRoot(); ?>/admin/people/?id=<?php $user->write('id'); ?>"><img src="<?php echo $img->src(50,true); ?>" border="0" height="50" width="50"  /></a>
		<?php } else { ?>
			<a href="<?php $POD->podRoot(); ?>/admin/people/?id=<?php $user->write('id'); ?>"><img src="<?php $user->POD->templateDir(); ?>/img/noimage.png" border="0" /></a>
		<?php } ?> 	
	</td>
	<td valign="top" align="left">
		<a href="<?php $POD->podRoot(); ?>/admin/people/?id=<?php $user->write('id'); ?>"  title="View this person's account details"><?php $user->write('nick'); ?></a>
	</td>
	<td valign="top" align="left">
		<?php 
		// check lastVisit format. might be a timestamp, might be a datetime
		if (is_numeric($user->lastVisit)) {
			echo $POD->timesince(intval(time() - $user->get('lastVisit')) / 60);
		} else {
			echo $POD->timesince(intval(time() - strtotime($user->get('lastVisit'))) / 60);		
		} ?>
	</td>	
	<td valign="top" align="left">
		<?php echo date('F d, Y',strtotime($user->get('memberSince'))); ?>
	</td>	
	<td valign="top" align="left">
		<?php if ($user->get('verificationKey')) { ?>Unverified<?php } else { ?>Verified<?php } ?>
	</td>	
</tr>