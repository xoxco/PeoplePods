<?

	include_once("../../PeoplePods.php");	
	$POD = new PeoplePod(array('lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));

	error_reporting(1);
	

	if ($_POST) { 

		$pod = $_POST['pod'];
		if (!$POD->libOptions('enable_'.$pod)) {
			$msg = "{$pod} is not enabled.";
		} else if (!$POD->libOptions('settings_'.$pod)) { 
			$msg = "No settings found for {$pod}";
		} else if (!function_exists($POD->libOptions('settings_'.$pod))) {
			$msg = "Settings function missing.  Should be " . $POD->libOptions('settings_'.$pod) . " in " . $POD->libOptions('include_'.$pod);
		} else {
			$func = $POD->libOptions('settings_'.$pod);
			$fields = $func($POD);			
			foreach ($fields as $field=>$label) { 
				$POD->setLibOptions($field,$_POST['libOption_'.$field]);
			}
			
			$POD->saveLibOptions();
			if (!$POD->success()) { 
				$msg = $POD->error();
			} else {
				$msg = "Settings updated.";
			}
		}			

	} else {
	
		$pod = $_GET['pod'];
		if (!$POD->libOptions('enable_'.$pod)) {
			$msg = "{$pod} is not enabled.";
		} else if (!$POD->libOptions('settings_'.$pod)) { 
			$msg = "No settings found for {$pod}";
		} else if (!function_exists($POD->libOptions('settings_'.$pod))) {
			$msg = "Settings function missing.  Should be " . $POD->libOptions('settings_'.$pod) . " in " . $POD->libOptions('include_'.$pod);
		} else {
			$func = $POD->libOptions('settings_'.$pod);
			$fields = $func($POD);
		}
		
	
	}
	


	$POD->changeTheme('admin');
	$POD->header();		
	$current_tab="pods";

	?>	
	<?php include_once("option_nav.php"); ?>
	<?php if ($msg) { ?>
		<div class="info">
		
			<?php echo $msg ?>
			
		</div>
	
	<?php } ?>
	<div class="panel">
	
		<h1>Settings for <?php echo $pod; ?></h1>
	
		<form method="post">
			<input type="hidden" name="pod" value="<?php echo $pod; ?>" />
		<?php foreach ($fields as $field=>$label) { ?>
		
			<p class="input">
				<label for="<?php echo $field; ?>"><?php echo $label; ?></label>
				<input name="libOption_<?php echo $field; ?>" id="<?php echo $field; ?>" class="text" value="<?php echo $POD->libOptions($field); ?>" />
			</p>
		
		<?php } ?>
		<p class="input">
			<input type="submit" value="Save Settings" />
		</p>
	
	</div>	
	<?php $POD->footer();	?>