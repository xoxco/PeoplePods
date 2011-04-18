<? 
	include_once("../../PeoplePods.php");	
	$POD = new PeoplePod(array('lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));
	
	if (isset($_GET['id'])  && !isset($_GET['action'])) {
	
		$user = $POD->getPerson(array('id'=>$_GET['id']));		
		if (!$user->success()) { 
			$message = "ERROR: " . $user->error();
		}	
	} else if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == "verify") {

		$user = $POD->getPerson(array('id'=>$_GET['id']));		
		if ($user->success()) {
		
			$user->set('verificationKey',null);
			$user->save();
			$message = $user->get('nick') . " has been manually verified.";
		} else {
			$message = "PeoplePods could not find the user you specified.";
		}	
		
	} else if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == "welcome") {
	
		$user = $POD->getPerson(array('id'=>$_GET['id']));		
		if ($user->success()) {
		
			$user->welcomeEmail();
			$message = "Welcome email resent to " . $user->get('nick');
		} else {
			$message = "PeoplePods could not find the user you specified.";
		}	
		
	} else if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
	
		$user = $POD->getPerson(array('id'=>$_GET['id']));		
		if ($user->success()) {
			$message = $user->get('nick') .  " has been deleted!";		
			$user->delete();
		} else {
			$message = "PeoplePods could not find the user you specified.";
		}	
	
	} else if (!isset($_POST['id']) && isset($_POST['action']) && $_POST['action'] == "update") {	
// create a new user!

		$user = $POD->getPerson();
		$user->set('nick',$_POST['nick']);
		$user->set('email',$_POST['email']);
		$user->set('password',$_POST['password']);
		$user->set('stub',$_POST['stub']);
		$user->set('fullname',$_POST['fullname']);
		
		$user->save(true);
		if (!$user->success()) { 
				$message = "SAVE FAILED! Error: " . $user->error();
			} else {
				$user->verify($user->get('verificationKey'));
				$message = "Person Created.  ";

				foreach ($_FILES as $filename=>$file) { 
					$user->addFile($filename,$file);
					if (!$user->success()) { 
						$message .= 'An error occured while attaching your file: ' . $user->error();
					}	
				}

				
				$user->files()->fill();

			
				// add meta fields.
				$user->tagsFromString($_POST['tags']);


				
				foreach ($_POST as $field => $value) {

					if (preg_match("/meta_(.*)/",$field,$matches)) { 

						// if this field is a meta field, add it!
						$field = $matches[1];
						if (is_numeric($field)) { // this is a new meta field name (meta_1 meta_2 meta_3) 
						// we need to match this with its value (meta_value_1)
							$new_field = $value;
							$new_value = $_POST['meta_value_' . $field];
							if ($new_field) { 
								$user->addMeta($new_field,$new_value);
							}

						} else if (strpos($field,"value")===0) { // this is a new meta value... we don't need to do anything
							next;
						} else { // this is an existing field, it has its value with it.  					
							$user->addMeta($field,$value);
						}						
						
					}				
				
				}
			
				if ($_POST['adminUser']) { 
					$user->addMeta('adminUser',1);
				} else {
					$user->addMeta('adminUser',null);
				}



			}
	
		
	} else if (isset($_POST['id']) && isset($_POST['action']) && $_POST['action']=='update') {
// save updates to existing user

		$user = $POD->getPerson(array('id'=>$_POST['id']));
		if ($user->success()) { 
			$user->set('nick',$_POST['nick']);
			$user->set('email',$_POST['email']);
			$user->set('stub',$_POST['stub']);
			$user->set('fullname',$_POST['fullname']);

			$user->save();
			if (!$user->success()) { 
				$message = "SAVE FAILED! Error: " . $user->error();
			} else {

				$message = "Account Info Updated.  ";
				foreach ($_FILES as $filename=>$file) { 
					$user->addFile($filename,$file);
					if (!$user->success()) { 
						$message .= 'An error occured while attaching your file: ' . $user->error();
					}	
				}				
				$user->files()->fill();
			
				// add meta fields.
				$user->tagsFromString($_POST['tags']);
				
				foreach ($_POST as $field => $value) {

					if (preg_match("/meta_(.*)/",$field,$matches)) { 

						// if this field is a meta field, add it!
						$field = $matches[1];
						if (is_numeric($field)) { // this is a new meta field name (meta_1 meta_2 meta_3) 
						// we need to match this with its value (meta_value_1)
							$new_field = $value;
							$new_value = $_POST['meta_value_' . $field];
							if ($new_field) { 
								$user->addMeta($new_field,$new_value);
							}

						} else if (strpos($field,"value")===0) { // this is a new meta value... we don't need to do anything
							next;
						} else { // this is an existing field, it has its value with it.  					
							$user->addMeta($field,$value);
						}						
						
					}				
				
				}
			
				if ($_POST['adminUser']) { 
					$user->addMeta('adminUser',1);
				} else {
					$user->addMeta('adminUser',null);
				}



			}


		} else { 
			$message = "PERSON NOT FOUND";
		}
		
	} else if ($_POST && $_POST['action'] == "password") {


		$user = $POD->getPerson(array('id'=>$_POST['id']));
		if ($user->success()) { 
			$user->set('password',$_POST['password']);
			$user->save();
			if (!$user->success()) { 
				$message = "Password Change FAILED - " . $user->error();
			} else {
				$message = "Password Changed";
			}
		} else {
			$message = "PERSON NOT FOUND";
		}

	} else {
// create empty new user	
		$user = $POD->getPerson();
	}


	$POD->changeTheme('admin');
	$POD->header();
	include("tools.php");

	if (isset($message)) { ?>
	
		<div class="info">
			<? echo $message; ?>
		</div>
	
	<? } 
	
	$user->output();

	$POD->footer();	


?>

