<? 

	// include core library
	include("../PeoplePods.php");

	$POD = new PeoplePod(array('debug'=>0,'authSecret'=>@$_COOKIE['pp_auth'],'cache'=>false));
		

	$required_pods = array(
		'core_api_simple',
		'contenttype_document_add',
		'contenttype_document_list',
		'contenttype_document_view',
		'core_authentication_login',
		'core_authentication_creation',
		'core_feeds',
		'core_friends',
		'core_groups',
		'core_invite',
		'core_private_messaging',
		'core_profiles',
		'core_search',
		'core_usercontent',
		'core_dashboard',	
	);


?>

<html>
	<head>
		<title>PeoplePods Installer</title>
		<style>
			
			body { background: #F0F0F0; color: #3A3A3A;font-family: "Trebuchet MS"; }
			div#installer { margin: auto; width: 500px; background: #FFF; border-right: 3px solid #CCC; border-bottom: 3px solid #CCC; font-size: 24px; padding: 20px; }
			p { margin-top: 0px; }
			p.error { background: #FFFF99; color: #F00; } 
			p.footer { font-size: 12px; } 
			label { font-weight: bold; display: block; }
			input.text { width: 100%;font-size: 20px;}
			div.info { background: #FFFF99; padding: 10px; margin-bottom: 20px; } 
		</style>
		<script type="text/javascript">
		
			function setCookie(value) {
				var exdate=new Date();
				exdate.setDate(exdate.getDate()+30);
				document.cookie="pp_auth=" +escape(value)+";path=/;expires="+exdate.toGMTString();
			}
		
		</script>
		</head>
	<body>
	
		<div id="installer">

<?php

	// this is the install script, it should set shit up for you.

			
		if ($POD->success() && $POD->hasAdminUser() &&  $POD->isAuthenticated() && $POD->currentUser()->get('adminUser')) { 
			// all is good, redirect to login??>
			
			<P><b>PeoplePods is ready to go!</b></p>
			
			<p><a href="<? $POD->podRoot(); ?>/admin/options/pods.php">Login to the Control Center to turn on site features.</a></b></P>
		
		<? } else {
			
			$ok = @$_GET['ok'];
						
			if (!$ok && $POD->errorCode()==100) { ?>
				
				<p>Welcome to PeoplePods!</p>
				
				<p>I am an install script that will attempt to set up your PeoplePods application and programming library.</p>
				
				<p>When I'm done, you should be able to login to PeoplePods and start personalizing the site.</p>
				
				<p>Before you continue, make sure you...</p>
				<ul>
					<li>Upload the PeoplePods folder to your web server and put it inside your site's root directory.</li>
					<li>Create a MySQL database for PeoplePods to use</li>
				</ul>
				
				<p>All set? <a href="index.php?ok=1">Continue &#187;</a></p>
				
			<? } else if ($POD->errorCode()==100) {
			// the library doesn't know where it is!Let's see if we can figure it out.
			
				echo "<p><b>Step 1: Find PeoplePods</b></p>";
				echo "<p>I will now try to set up some default PeoplePods options, such as where on your server the libraries are located.</p>";
				echo "<hr />";
				
				$installDir = realpath("../");
	
				if (@$_POST['installDir']) { 
					$installDir = $_POST['installDir'];
				}			
				
				$res = (file_exists("$installDir/PeoplePods.php"));
						
				if ($res != 1) {
				// we need to collect the install dir from the user.
					?>
					
					<p class="error">Uhoh! Could not find PeoplePods.php in $installDir.</p>
					<form method="post">
						
						<p><label for="installDir">Where is PeoplePods installed?</label><input name="installDir" class="text" id="installDir" value="<? echo htmlspecialchars($installDir); ?>" /></p>
						<p><input type="submit" value="Continue &#187;" /></p>	
									
					</form>
					
					<?
					
				} else {
	
					$path = $_SERVER['REQUEST_URI'];
					preg_match("/(.*)\/(.*?)\/install.*/",$path,$matches);
					$serverRoot = $matches[1];
					$appRoot = "/".$matches[2];
	
				
					echo "<p>Found PeoplePods at <b>$installDir!</b></p>";
					echo "<p>Writing config file...</p>";
					$siteName = 'My PeoplePods Site';
					
	
					// set up the install directory and other paths used within the library
					$POD->setLibOptions('last_database_update',$POD->VERSION);

					$POD->setLibOptions('installDir',$installDir);
					$POD->setLibOptions('etcPath',$installDir . "/lib/etc");
	
					// set up some default values for use in templates and other parts of the library
					$POD->setLibOptions('siteName',$siteName);
					$POD->setLibOptions('podRoot',$appRoot);
					$POD->setLibOptions('siteRoot',$serverRoot);
					$POD->setLibOptions('currentTheme','default');
					$POD->setLibOptions('server','http://'.$_SERVER['SERVER_NAME']);
					$POD->setLibOptions('documentIconMaxWidth','100');
					$POD->setLibOptions('documentIconSquare','documentIconSquare');
					$POD->setLibOptions('documentImageMaxWidth','530');
					$POD->setLibOptions('documentImageResize','documentImageResize');
					$POD->setLibOptions('peopleIconMaxWidth','60');
					$POD->setLibOptions('peopleIconSquare','peopleIconSquare');
					$POD->setLibOptions('peopleImageMaxWidth','300');
					$POD->setLibOptions('peopleImageResize','peopleImageResize');
					
	
					// FIX THIS
					// set other default options!!
				
					$error = false;
					$POD->saveLibOptions(true);
					if ($POD->success()) { 
						echo "<p>Defaults written to " . $POD->libOptions('etcPath') . '/options.php</p>';
					
						$POD->loadAvailablePods();
					 	foreach ($POD->PODS as $name => $podling) {
							if (in_array($name,$required_pods)) { 
								$POD->enablePOD($name);
							}
						}
						
						$message = $POD->writeHTACCESS($installDir."/..");
						if (!$POD->success()) { 
							$error = true;
							unlink($POD->libOptions('etcPath') . "/options.php");
							echo '<p class="error">Could not write to .htaccess file! ' . $POD->error() . "</p>";
						} else {
							$POD->saveLibOptions(true);

							echo "<p>$message</p>";
						}
					
					} else {
						echo '<p class="error">Could not write to config file! ' . $POD->error() . "</p>";
						$error = true;
					}
					
					if (!$error) {
						echo '<p><a href="index.php">Continue &#187;</a></p>';
					}

	
				}
			} else if ($POD->errorCode()==101) {
			
				echo "<p><b>Step 2: Connect to the Database</b></p>";
				echo "<p>Now I need to figure out how to connect to the MySQL database you created for me.</p>";
				echo "<hr />";
			
				if (@$_POST['mysql_server'] && @$_POST['mysql_db'] && @$_POST['mysql_user'] && @$_POST['mysql_pass']) {
					
					$newDb = $POD->connectToDatabase($_POST['mysql_server'],$_POST['mysql_user'],$_POST['mysql_pass'],$_POST['mysql_db']);
					if ($newDb) { 
						$POD->setLibOptions('mysql_server',$_POST['mysql_server']);
						$POD->setLibOptions('mysql_db',$_POST['mysql_db']);
						$POD->setLibOptions('mysql_user',$_POST['mysql_user']);
						$POD->setLibOptions('mysql_pass',$_POST['mysql_pass']);
					
						$POD->saveLibOptions(true);
						if ($POD->success()) { 
							echo '<p>Database options saved!</p>';
							echo '<p>Creating Database...</p>';				
										

							// here is all the juicy SQL that creates the PeoplePods tables.
							include("SQL.php");
									
							foreach ($tables as $table=>$sql) { 
							
								$test = "SELECT count(1) FROM $table";
								$res = mysql_query($test,$newDb);
								if ($res) { 
									echo "<p>Table $table Already found!</p>";
								} else {
									$res = mysql_query($sql,$newDb);
									if ($res) { 
										echo "<p>Table $table created!</p>";
									} else {
										echo "<p class=\"error\">Table $table could not be created!You should run the following SQL via the MySQL command line: $sql</p>";
									}
								}
							
							}							
							
							echo '<p><a href="index.php">Continue &#187;</a></p>';
						} else {
							echo '<p class="error">Could not write to config file! '. $POD->error() . "</p>";
						}
					} else {
						echo '<p class="error">Those database options did not work!</p>';
						echo '<p><a href="index.php">Continue &#187;</a></p>';
					}			
				} else {
							
					if ($POD->libOptions('mysql_server')) { echo "<p class=\"error\">The current settings aren't working. :(</p>"; }
					echo '<form method="post">';
					echo '<p><label for="mysql_server">MySQL Server:</label><input name="mysql_server"class="text" value="' . $POD->libOptions('mysql_server') . '" /></p>';
					echo '<p><label for="mysql_db">Database Name:</label><input name="mysql_db"class="text" value="' . $POD->libOptions('mysql_db') . '" /></p>';
					echo '<p><label for="mysql_user">Username:</label><input name="mysql_user"class="text" value="' . $POD->libOptions('mysql_user') . '"/></p>';
					echo '<p><label for="mysql_pass">Password:</label><input name="mysql_pass"class="text" value="' . $POD->libOptions('mysql_pass') . '"/></p>';
					echo '<p><input type="submit" value="Continue &#187;" /></p>';
					echo '</form>';
				}
			
			} else if (!$POD->hasAdminUser()) { 
			// create first user
	
	
				if ($_POST['nick'] && $_POST['email'] && $_POST['password']) {
					$NEWUSER = $POD->getPerson(array('nick'=>$_POST['nick'],'email'=>$_POST['email'],'password'=>$_POST['password']));
					$NEWUSER->save(true);
					if ($NEWUSER->success()) {
							$NEWUSER->verify($NEWUSER->get('verificationKey'));
							$NEWUSER->addMeta('adminUser',1);
							$POD = new PeoplePod(array('debug'=>0,'authSecret'=>$NEWUSER->get('authSecret')));
							// set the from address for site emails
							
							$POD->setLibOptions('fromAddress',$NEWUSER->get('nick') . "<".$NEWUSER->get('email').">");
							$POD->saveLibOptions(true);
					} else {
						$error_msg = $NEWUSER->error();		
					}	
				
				}


					
				if ($POD->isAuthenticated()) { ?>
				
					<script type="text/javascript">
						setCookie('<? $NEWUSER->write('authSecret'); ?>');
					</script>
					<p><b>You have been upgraded to SUPER USER!</b></p>
					<p><a href="index.php">Continue &#187;</a></p>
	
				<? } else { ?>
	
				<p><B>Step 3: Create the first Super User</b></p>
				<p>Soon, you will become the all-powerful, all-seeing super user of this site! Remember: with great power comes great responsibility!</p>
				<hr />
			
				<? if (isset($error_msg)) { ?>
					<div class="info">
						<? echo $error_msg; ?>
					</div>
				<? } ?>
					
				<form method="post">
					<p>Name: <input name="nick"class="text" value="admin" /></p>
					<p>Your Email: <input name="email"class="text" value="" /></p>
					<p>Password: <input name="password"class="text" value="" /></p>
					<p><input type="submit" value="Create" /></p>
				</form>		
	
			<?	}	
			
			} else {
			
				echo '<p>Uncaught Configuration Error!</p>';
				echo "<P>POD: "; 
				if ($POD->success()) { echo "Success"; } else { echo "Fail " . $POD->error(); }
				echo "</p>";
				
				if ($POD->success()) { 
					echo "<p>Has Super User: ";
					if ($POD->hasAdminUser()) { echo "Yes"; } else { echo "No"; }
					echo "</p>";
					
					echo "<P>Authentication: ";
					if ($POD->isAuthenticated()) { echo "Success"; } else { echo "Fail"; }
					echo "</p>";
					
					if ($POD->isAuthenticated()) { 
						echo "<P>Super User: ";
						if ($POD->currentUser()->get('adminUser')) { echo "Success"; } else { echo "Fail"; }
						echo "</p>";
					}
					
				}								
				echo 'Please refer to the <a href="http://peoplepods.net/readme">PeoplePods documentation.</a>';
			} 
			
		} 

?>

		<hr />
		
		<p class="footer">Need help? Visit the <a href="http://peoplepods.net/">Official PeoplePods HQ</a></p>
	
		</div>
	</body>
</html>