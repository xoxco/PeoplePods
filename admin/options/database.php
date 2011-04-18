<? 
	include_once("../../PeoplePods.php");	
	error_reporting(1);

	$POD = new PeoplePod(array('lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));
	if ($_POST) {
	
		
		
		// lets verify this shit before saving it!
		
		$newDb = $POD->connectToDatabase($_POST['mysql_server'],$_POST['mysql_user'],$_POST['mysql_pass'],$_POST['mysql_db']);

		if ($newDb) { 
	
			$POD->setLibOptions('mysql_server',$_POST['mysql_server']);
			$POD->setLibOptions('mysql_user',$_POST['mysql_user']);
			$POD->setLibOptions('mysql_pass',$_POST['mysql_pass']);
			$POD->setLibOptions('mysql_db',$_POST['mysql_db']);
	
			$POD->saveLibOptions();
			if ($POD->success()) { 
					$message = "Config updated.";
			} else {
				$message = $POD->error();
			}
		} else {
			$message = "Could not connect to the database specified!  Config not updated!";
		}
	}

	$POD->changeTheme('admin');
	$POD->header();
	$current_tab="database";
?>
<? include_once("option_nav.php"); ?>
<? if ($message) { ?>
	<div class="info">
	
		<? echo $message ?>
		
	</div>

<? } ?>
<div class="panel">

	<h1>Database Options</h1>


	<p>Connect to this database via the command line MySQL tool:</p>

	<p>mysql -u <?= $POD->libOptions('mysql_user'); ?> -p -h <?= $POD->libOptions('mysql_server'); ?> <?= $POD->libOptions('mysql_db'); ?></p>

	<form method="post" id="database_options" class="valid">
	
	<p class="input"><label for="siteName">MySQL Server:</label><input name="mysql_server" id="mysql_server" class="text required" type="text" value="<? echo htmlspecialchars($POD->libOptions('mysql_server')); ?>" /></p>
	
	<p class="input"><label for="siteName">MySQL User:</label><input name="mysql_user" id="mysql_user" class="text required" type="text" value="<? echo htmlspecialchars($POD->libOptions('mysql_user')); ?>" /></p>

	<p class="input"><label for="siteName">MySQL Password:</label><input name="mysql_pass" id="mysql_pass" class="text required" type="text" value="<? echo htmlspecialchars($POD->libOptions('mysql_pass')); ?>" /></p>

	<p class="input"><label for="siteName">MySQL Database:</label><input name="mysql_db" id="mysql_db" class="text required" type="text" value="<? echo htmlspecialchars($POD->libOptions('mysql_db')); ?>" /></p>

				
	
	<P><input type="submit" class="button" value="Update Settings" />
	
	</form>

</div>


<? $POD->footer(); ?>