<? 
	include_once("../../PeoplePods.php");	
	
	$POD = new PeoplePod(array('lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));
	$POD->changeTheme('admin');
	
	if ($_GET['id'] && $_GET['action'] == "") { 
// load a Group for editing	
		$group = $POD->getGroup(array('id'=>$_GET['id']));
		if (!$group->success()) { 
			$message = $group->error();
		}	
	} else if ($_GET['id'] && $_GET['action'] == "delete") { 
// load a document for editing	
		$group = $POD->getGroup(array('id'=>$_GET['id']));
		if (!$group->success()) { 
			$message = $doc->error();
		} else {
			$group->delete();
			if (!$group->success()) { 
				$message = $doc->error();
			} else {
				header("Location: search.php?msg=Group+deleted!");
			}
		}
	} else if ($_POST['action']=="update") {

		if ($_POST['id']) { 	
			$group = $POD->getGroup(array('id'=>$_POST['id']));
		} else {
			$group = $POD->getGroup();
		}
		if (!$group->success()) {
			$message = "Could not save: " . $group->error();
		} else {
			
			$group->set('groupname',$_POST['groupname']);
			$group->set('description',$_POST['description']);				
			$group->set('type',$_POST['type']);
			$group->set('stub',$_POST['stub']);
			$group->set('date',$_POST['date']);
			
			$group->save();
			
			if (!$group->success()) { 
				$message = "Save Failed! " . $group->error();
			} else {

				$message = "Group Updated!";

				foreach ($_FILES as $filename=>$file) { 
					$group->addFile($filename,$file);
					if (!$group->success()) { 
						$message .= 'An error occured while attaching your file: ' . $group->error();
					}	
				}
				$group->files()->fill();

				$group->tagsFromString($_POST['tags']);
				foreach ($_POST as $field => $value) {

					if (preg_match("/meta_(.*)/",$field,$matches)) { 

						// if this field is a meta field, add it!
						$field = $matches[1];
						if (is_numeric($field)) { // this is a new meta field name (meta_1 meta_2 meta_3) 
						// we need to match this with its value (meta_value_1)
							$new_field = $value;
							$new_value = $_POST['meta_value_' . $field];
							if ($new_field) { 
								$group->addMeta($new_field,$new_value);
							}
						} else if (strpos($field,"value")===0) { // this is a new meta value... we don't need to do anything
							next;
						} else { // this is an existing field, it has its value with it.  	
							$group->addMeta($field,$value);
						}						
					}				
				}
			}
		}
	


	} else {
// create a new Group
		$group = $POD->getGroup();
		
	}
	$POD->header();
	include_once("tools.php");
	if ($message) { ?>
	
		<div class="info">
			<? echo $message; ?>
		</div>
	
	<? } ?>

	
<?	
	
	$group->output();	
	
	$POD->footer(); 

?>