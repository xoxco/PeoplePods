<? 

	include_once("../../PeoplePods.php");	
	
	$POD = new PeoplePod(array('debug'=>0,'lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));
	$POD->changeTheme('admin');
	
	
	if (@$_GET['id'] && @$_GET['action'] == "") { 
// load a document for editing	
		$doc = $POD->getContent(array('id'=>$_GET['id']));
		if (!$doc->success()) { 
			$message = $doc->error();
		}	
	} else if (@$_GET['id'] && $_GET['action'] == "delete") { 
// load a document for editing	
		$doc = $POD->getContent(array('id'=>$_GET['id']));
		if (!$doc->success()) { 
			$message = $doc->error();
		} else {
			$doc->delete();
			if (!$doc->success()) { 
				$message = $doc->error();
			} else {
				header("Location: search.php?msg=Document+deleted!");
			}
		}
	} else if ($_POST['id'] && isset($_POST['notspam'])) {
	
		
		$doc = $POD->getContent(array('id'=>$_POST['id']));
		if (!$doc->success()) {
			$message = "Could not unspam: " . $doc->error();
		} else {
			$message = 'Marked as not spam';
			$doc->notSpam();
		}

	} else if ($_POST['id'] && $_POST['action']=="update") {
// update an existing doc	
	
		$doc = $POD->getContent(array('id'=>$_POST['id']));
		if (!$doc->success()) {
			$message = "Could not save: " . $doc->error();
		} else {
			
			$doc->set('headline',$_POST['headline']);
			$doc->set('link',$_POST['link']);
			$doc->set('body',$_POST['body']);

			
			$doc->set('privacy',$_POST['privacy']);
			$doc->set('type',$_POST['type']);
			$doc->set('status',$_POST['status']);
			$doc->set('stub',$_POST['stub']);
			$doc->set('date',$_POST['date']);
			
			if ($_POST['userId'] != '') { 
				$doc->set('userId',$_POST['userId']);
			}
			
			$doc->save();
			
			if (!$doc->success()) { 
				$message = "Save Failed! " . $doc->error();
			} else {
	
				$message = "Content Updated!  ";

				foreach ($_FILES as $filename=>$file) { 
					$doc->addFile($filename,$file);
					if (!$doc->success()) { 
						$message .= 'An error occured while attaching your file: ' . $doc->error();
					}	
				}
				$doc->files()->fill();
				
				// add meta fields.
				
				$doc->tagsFromString($_POST['tags']);
			
				foreach ($_POST as $field => $value) {

					if (preg_match("/meta_(.*)/",$field,$matches)) { 

						// if this field is a meta field, add it!
						$field = $matches[1];
						if (is_numeric($field)) { // this is a new meta field name (meta_1 meta_2 meta_3) 
						// we need to match this with its value (meta_value_1)
							$new_field = $value;
							$new_value = $_POST['meta_value_' . $field];
							if ($new_field) { 
								$doc->addMeta($new_field,$new_value);
							}

						} else if (strpos($field,"value")===0) { // this is a new meta value... we don't need to do anything
							next;
						} else { // this is an existing field, it has its value with it.  					
							$doc->addMeta($field,$value);
						}						
						
					}				
				
				}



			}
		
		}
	
	
	
	} else if ($_POST['action']=="update" && $_POST['id']=='') {
// save a new document.

		$doc = $POD->getContent();
		$doc->set('headline',$_POST['headline']);
		$doc->set('link',$_POST['link']);
		$doc->set('body',$_POST['body']);
		


		$doc->set('privacy',$_POST['privacy']);		
		$doc->set('type',$_POST['type']);
		$doc->set('status',$_POST['status']);
		$doc->set('stub',$_POST['stub']);
		$doc->set('date',$_POST['date']);					
		$doc->save();
		
		if (!$doc->success()) { 
			$message = "Save Failed! " . $doc->error();
		} else {
			$message = "Post Created!";
			$doc->tagsFromString($_POST['tags']);
			foreach ($_FILES as $filename=>$file) { 
				$doc->addFile($filename,$file);
				if (!$doc->success()) { 
					$message .= 'An error occured while attaching your file: ' . $doc->error();
				}	
			}
			$doc->files()->fill();


			// add meta fields.				
			foreach ($_POST as $field => $value) {

				if (preg_match("/meta_(.*)/",$field,$matches)) { 

					// if this field is a meta field, add it!
					$field = $matches[1];
					if (is_numeric($field)) { // this is a new meta field name (meta_1 meta_2 meta_3) 
					// we need to match this with its value (meta_value_1)
						$new_field = $value;
						$new_value = $_POST['meta_value_' . $field];
						if ($new_field) { 
							$doc->addMeta($new_field,$new_value);
						}

					} else if (strpos($field,"value")===0) { // this is a new meta value... we don't need to do anything
						next;
					} else { // this is an existing field, it has its value with it.  					
						$doc->addMeta($field,$value);
					}						
					
				}				
			
			}



		}
	

	} else {
// create a new document

		$doc = $POD->getContent();
		if ($_GET['type']) {
			$doc->type = strtolower($_GET['type']);
		}
		
	}
	$POD->header();
	include_once("tools.php");
	if (@$message) { ?>
	
		<div class="info">
			<? echo $message; ?>
		</div>
	
	<? } ?>
<?	
	
	if ($doc->type) { 
		$doc->output();	
	} else {
		$doc->output('type.chooser');
	}	
	$POD->footer(); 

?>