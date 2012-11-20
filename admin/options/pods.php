<?

	include_once("../../PeoplePods.php");	
	$POD = new PeoplePod(array('lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));

	error_reporting(1);
	
	$htaccessPath = realpath("../../../");

	$message = '';

/*
	// find all the PODS that are installed on this server.
	// each POD has a settings.php that calls $POD->registerPOD with options.
	$podInstallDir = opendir($POD->libOptions('installDir') . "/pods/");
	while ($pod = readdir($podInstallDir)) {
		if (file_exists($POD->libOptions('installDir') . "/pods/$pod/settings.php")) { 
			require($POD->libOptions('installDir') . "/pods/$pod/settings.php");
		}
	}	 
*/

	$POD->loadAvailablePods();
	
	if ($_GET || $_POST) {
		if ($_GET) { $form = $_GET; } else { $form = $_POST; }
	}
		
	// if there is a POST coming in, this means I'm updating my pod preferences.
	if ($form) { 
	
		ksort($POD->PODS);

		// iterate through each pod we know about.
	 	foreach ($POD->PODS as $name => $podling) {
	
			// if it was checked, enable it.  if not, disable it.
			if ($form[$name]) {
				$message .= $POD->enablePOD($name);
			} else {
				if ($POD->isEnabled($name)) {
					$message .=  $POD->disablePOD($name);
				}
			}
		}

		// save everything to lib/etc/options.php
		$POD->saveLibOptions();
		if (!$POD->success()) { 
			$message .= $POD->error();
		} else {

			$POD->processIncludes();
			$message .= $POD->writeHTACCESS($htaccessPath);
		
		}
	}



	$POD->changeTheme('admin');
	$POD->header();		
	$current_tab="pods";

	?>	
	<?php include_once("option_nav.php"); ?>
	<?php if ($message!='') { ?>
		<div class="info">
		
			<?php echo $message ?>
			
		</div>
	
	<?php } ?>
	<div class="panel">
	<h1>Plugin Pods</h1>

	<p>
		Plugin Pods are sets of PeoplePods functionality that can be easily turned on and off.
		Choose Pods from the list below to customize the features present on your site.
	</p>
	
	<p>
		New Pods should be placed in <i><?php echo $POD->libOptions('installDir'); ?>/pods</i>
	</p>
	
	<form method="post" action="<?php $POD->podRoot(); ?>/admin/options/pods.php">
	<input name="go" type="hidden" value="foo" />
	<table cellspacing="0" cellpadding="0" class="stack_output">
		<tr>
			<th align="left">
				POD Name
			</th>
			<th align="left">
				Description
			</th>
			<th>&nbsp;</th>
			<th align="right">
				<input type="checkbox" onchange="selectAll(this);" />
			</th>
		</tr>
		<?php 
			$count = 0;
			ksort($POD->PODS);

			foreach ($POD->PODS as $name => $podling) { $count++; ?>
			<tr  class="<?php if ($count % 2 ==0) {?>even<?php } else { ?>odd<?php } ?>">
				<td valign="top" align="left">			
					<B><?php echo $name; ?></B>
				</td>
				<td valign="top" align="left">
					<?php echo $podling['description'] ?>
				</td>
				<td valign="top">
					<?php if ($POD->isEnabled($name) && $POD->libOptions('settings_'.$name)) { ?>
						<a href="podsettings.php?pod=<?php echo $name; ?>">settings</a>
					<?php } ?>
				</td>
				<td valign="top" align="right">
					<input type="checkbox" class="enabler" name="<?php echo $podling['name']; ?>" <?php if ($POD->isEnabled($name)) {?>checked<?php } ?> />				
				</td>				
			</tr>
		<?php } ?>
		<tr>
			<td colspan="4" align="right">
				<input type="submit" value="Update" />
			</td>
		</tr>
	</table>
	</form>

	<?php if ($newrules) { ?>
		<p>The following lines should appear in <i><?php echo $htaccessPath; ?>/.htaccess</i></p>
		<textarea rows="15" cols="100"><?php echo $newrules ?></textarea>
	<?php } ?>			
	</div>
	
	<?php $POD->footer();	?>