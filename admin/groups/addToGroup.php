<?		include_once("../../PeoplePods.php");	
	
	$POD = new PeoplePod(array('lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));
	$POD->changeTheme('admin');

	$group = $_GET['group'];
	$doc = $_GET['doc'];
	$action = 'add';
	if ($_GET['action']) {
		$action = $_GET['action'];
	}
	// load parent
	$parent = $POD->getGroup(array('id'=>$group));
	if ($parent->success()) { 
	
		// load child
		$child = $POD->getContent(array('id'=>$doc));
		if ($child->success()) { 
		
			if ($action == 'add') { 
				$parent->addContent($child);
			} else  if ($action == 'remove') {
				$parent->removeContent($child);
			}
			if ($parent->success()) { 
				// refill the children stack
				$parent->content()->fill();
			} else {
				echo "Could not add to group! " . $parent->error();
			}
		
		} else {
			echo "Error with content! " . $child->error();
		}
		
		
	} else {
		echo "Error with group! " . $parent->error();
	}	
	
	while ($child = $parent->content()->getNext()) { 
		$child->output('child');
	}
	
	if ($parent->content()->count() == 0) { 
		echo '<p class="column_padding">This group has no posts.</p>';
	}
	
	
?>
