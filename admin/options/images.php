<? 
	include_once("../../PeoplePods.php");	
	$POD = new PeoplePod(array('lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));

	if (@$_POST) {
	
		$POD->setLibOptions('peopleImageResize',$_POST['peopleImageResize']);
		$POD->setLibOptions('peopleImageMaxWidth',$_POST['peopleImageMaxWidth']);
		$POD->setLibOptions('peopleIconSquare',$_POST['peopleIconSquare']);
		$POD->setLibOptions('peopleIconMaxWidth',$_POST['peopleIconMaxWidth']);
	
		$POD->setLibOptions('documentImageResize',$_POST['documentImageResize']);
		$POD->setLibOptions('documentImageMaxWidth',$_POST['documentImageMaxWidth']);
		$POD->setLibOptions('documentIconSquare',$_POST['documentIconSquare']);
		$POD->setLibOptions('documentIconMaxWidth',$_POST['documentIconMaxWidth']);

		$POD->saveLibOptions();
		if ($POD->success()) { 
				$message = "Config updated.";
		} else {
			$message = $POD->error();
		}

	}
	$POD->changeTheme('admin');
	$POD->header();
	$current_tab="images";
	
?>
	<? include_once("option_nav.php"); ?>

	<? if (isset($message)) { ?>
		<div class="info">
		
			<? echo $message ?>
			
		</div>
	
	<? } ?>
	
<div class="panel">

	<h1>Images & Icons</h1>
	
	<p>
		PeoplePods can automatically resize and create thumbnail versions of uploaded photos.
		Customize the size of these images below.
	</p>

	<h2>People Images</h2>
	
	<form method="post">
		<p><input type="checkbox" value="peopleImageResize" name="peopleImageResize" <? if ($POD->libOptions('peopleImageResize')) { ?>checked<?}?> /> Resize profile images.
		<div class="more_info">
			<P>Resize images to a maximum width of:
			<input type="text" class="text" name="peopleImageMaxWidth" value="<? echo $POD->libOptions('peopleImageMaxWidth'); ?>" />
			</p>
		</div></p>
		
		<P>Resize icons to a maximum width of:
		<input type="text" class="text" name="peopleIconMaxWidth" value="<? echo $POD->libOptions('peopleIconMaxWidth'); ?>" />
		<div class="more_info">
			<input type="checkbox" value="peopleIconSquare" name="peopleIconSquare" <? if ($POD->libOptions('peopleIconSquare')) { ?>checked<?}?> /> Crop to a square
		</div>
		</p>
	
		
		<h2>Document Images</h2>
		<p><input type="checkbox" value="documentImageResize" name="documentImageResize" <? if ($POD->libOptions('documentImageResize')) { ?>checked<?}?> /> Resize document images.
		<div class="more_info">
			<P>Resize images to a maximum width of:
			<input type="text" class="text" name="documentImageMaxWidth" value="<? echo $POD->libOptions('documentImageMaxWidth'); ?>" />
			</p>
		</div></p>
		
		<P>Resize icons to a maximum width of:
		<input type="text" class="text" name="documentIconMaxWidth" value="<? echo $POD->libOptions('documentIconMaxWidth'); ?>" />
		<div class="more_info">
			<input type="checkbox" value="documentIconSquare" name="documentIconSquare" <? if ($POD->libOptions('documentIconSquare')) { ?>checked<?}?> /> Crop to a square
		</div>
		</p>
		
		<p><input type="submit" value="Save" class="button"></p>
	</form>

</div>
<? $POD->footer(); ?>