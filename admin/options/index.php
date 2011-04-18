<? 
	include_once("../../PeoplePods.php");	
	$POD = new PeoplePod(array('lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));

	if ($_POST) {
	
		
		
		$siteName = $_POST['siteName'];
		$installDir = $_POST['installDir'];
		$podRoot = $_POST['podRoot'];
		$siteRoot = $_POST['siteRoot'];
		$server = $_POST['server'];
		$peoplepods_api = strip_tags($_POST['peoplepods_api']);

		// strip off trailing slashes		
		$installDir = preg_replace("/\/$/","",$installDir);
		$siteRoot = preg_replace("/\/$/","",$siteRoot);		
		$podRoot = preg_replace("/\/$/","",$podRoot);		
		$server = preg_replace("/\/$/","",$server);
	
		if (!preg_match("/^http/",$server)) {
			$server = "http://" . $server;
		}

		// add leading slashes if necessary
		if (!preg_match("/^\//",$podRoot)) { 
			$podRoot = "/$podRoot";
		}
		if (!preg_match("/^\//",$siteRoot) && $siteRoot != '') { 
			$siteRoot = "/$siteRoot";
		}

		// lets verify this shit before saving it!
		
		$res = (file_exists("$installDir/PeoplePods.php"));
		if ($res) { 
	
			$POD->setLibOptions('siteName',$siteName);
			$POD->setLibOptions('installDir',$installDir);
			$POD->setLibOptions('server',$server);
			$POD->setLibOptions('podRoot',$podRoot);
			$POD->setLibOptions('siteRoot',$siteRoot);
			$POD->setLibOptions('peoplepods_api',$peoplepods_api);
			
			// recreate automatically generated values
			$POD->setLibOptions('etcPath',$installDir . "/lib/etc");
	
	
			$POD->saveLibOptions();
			if ($POD->success()) { 
					$message = "Config updated.";
			} else {
				$message = $POD->error();
			}
		} else {
			$message = "Could not find PeoplePods libraries at $installDir/PeoplePods.php!  Config not updated!";
		}
	} else if (isset($_GET['regenerate'])) {
	
		$installDir = realpath("../../");
	
		if ($_POST['installDir']) { 
			$installDir = $_POST['installDir'];
		}			

		$res = (file_exists("$installDir/PeoplePods.php"));
		if ($res) { 

			$path = $_SERVER['REQUEST_URI'];
			preg_match("/(.*)\/(.*?)\/admin\/options\/.*/",$path,$matches);
			$serverRoot = $matches[1];
			$appRoot = "/".$matches[2];

			$POD->setLibOptions('installDir',$installDir);
			$POD->setLibOptions('installDir',$installDir);
			$POD->setLibOptions('etcPath',$installDir . "/lib/etc");
			$POD->setLibOptions('podRoot',$appRoot);
			$POD->setLibOptions('siteRoot',$serverRoot);
			$POD->setLibOptions('server','http://'.$_SERVER['SERVER_NAME']);
			$POD->setLibOptions('imgDir',null);
			$POD->setLibOptions('imgPath',null);
			$POD->setLibOptions('docDir',null);
			$POD->setLibOptions('docPath',null);
			$POD->setLibOptions('cacheDir',null);
			$POD->saveLibOptions();
			if ($POD->success()) { 
					$message = "Paths reset.";
			} else {
				$message = $POD->error();
			}
		} else {
			$message = "Could not find PeoplePods library file as expected at $installDir/PeoplePods.php";
		}
	}

	$POD->changeTheme('admin');
	$POD->header();
	$current_tab="options";
?>
<? include_once("option_nav.php"); ?>
<? if (isset($message)) { ?>
	<div class="info">
	
		<? echo $message ?>
		
	</div>

<? } ?>
<div class="panel">

	<h1>Site Options</h1>



	<form method="post" id="site_options" class="valid">
	
	<p class="input"><label for="siteName">What do you want to call your site?</label><input name="siteName" id="siteName" class="text required" type="text" value="<? echo htmlspecialchars($POD->libOptions('siteName')); ?>" /></p>

	<p class="input"><label for="siteName">What is your <a href="http://peoplepods.net/">PeoplePods.net API Key</a></label><input name="peoplepods_api" id="peoplepods_api" class="text" type="text" value="<? echo htmlspecialchars($POD->libOptions('peoplepods_api')); ?>" /></p>
	
	<p class="input"><label for="installDir">What is the path to your PeoplePods installation?</label><input name="installDir" id="installDir" class="text required" type="text" value="<? echo htmlspecialchars($POD->libOptions('installDir')); ?>" />
	<span class="field_explain">Tells PeoplePods where to look for itself.</span></p>

	<p class="input"><label for="server">What is the URL for this PeoplePods website?</label><input name="server" id="server" class="text required" type="text" value="<? echo htmlspecialchars($POD->libOptions('server')); ?>" />
	<span class="field_explain">Mine is <a href="http://xoxco.com/">http://xoxco.com</a>.  PeoplePods uses it to build navigational links.</span></p>

	<p class="input"><label for="siteRoot">What is the relative URL this PeoplePods app?</label><input name="siteRoot" id="siteRoot" class="text" type="text" value="<? echo htmlspecialchars($POD->libOptions('siteRoot')); ?>" />
	<span class="field_explain">This should be blank if you want PeoplePods to control the root of your site, or the path to the sub-folder where your site will live if not.</span></p>

	<p class="input"><label for="podRoot">What is the URL to the PeoplePods install?</label><input name="podRoot" id="podRoot" class="text required" type="text" value="<? echo htmlspecialchars($POD->libOptions('podRoot')); ?>" />
	<span class="field_explain">This is probably "/peoplepods" - this is the relative URL to the PeoplePods install. PeoplePods uses it to build links to templates and admin tools.</span></p>

	<p><a href="?regenerate=yes">Regenerate paths</a> to automatically detect the correct settings and reset cache and image paths.</p>				
	
	<P><input type="submit" class="button" value="Update Settings" /></p>
	
	</form>
</div>


<? $POD->footer(); ?>