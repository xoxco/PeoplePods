<?

	include_once("../../PeoplePods.php");	
	$POD = new PeoplePod(array('lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));

	error_reporting(1);

	$themesDir = opendir($POD->libOptions('installDir') . "/themes/");
	$themes = array();
	while ($theme = readdir($themesDir)) {
			if (file_exists($POD->libOptions('installDir') . "/themes/$theme/theme.info")) { 
			$themeinfo = file_get_contents($POD->libOptions('installDir') . "/themes/$theme/theme.info");
			$lines = explode("\n",$themeinfo);
			$info = array();
			$info['theme'] = $theme;
			foreach ($lines as $line) { 			
				list($key,$value) = explode("=",$line,2);
				$key = trim($key);
				$value = trim($value);
				$info[$key] = $value;
			}
			if ($info['name'] && $info['theme']) { 
				array_push($themes,$info);
			}
		}
	}	 
	
	if ($_GET['activate']) { 
			
		$POD->setLibOptions('currentTheme',$_GET['activate']);
		$POD->saveLibOptions();
		if (!$POD->success()) { 
			$message = $POD->error();
		} else {

		}
	}

	$currentTheme = $POD->currentTheme();
	$POD->changeTheme('admin');
	$POD->header();		
	$current_tab="themes";

	?>	
	<? include_once("option_nav.php"); ?>
	<? if ($message) { ?>
		<div class="info">
		
			<? echo $message ?>
			
		</div>
	
	<? } ?>
	<div class="list_panel">
	<h1>Themes</h1>

	<p>
		Themes are sets of templates that control how your site looks.
	</p>

	<p>
		New themes should be placed in <i><? echo $POD->libOptions('installDir'); ?>/themes</i>
	</p>	

	
	<div id="themes">
		<h2>Installed Themes</h2>
		<? foreach ($themes as $theme) { ?>
			<div class="theme <? if ($currentTheme==$theme['theme']) { ?>active_theme<? } ?>">
				<h3><? echo $theme['name']; ?></h3>
				<? echo $POD->formatText($theme['description']); ?>
				<? if ($currentTheme!=$theme['theme'] && $theme['theme']!='admin') { ?>
				<p><a href="?activate=<?= $theme['theme']; ?>">Activate</a></p>			
				<? } ?>
			</div>
		<? } ?>
	</div>
		
	</div>
	
	<? $POD->footer();	?>