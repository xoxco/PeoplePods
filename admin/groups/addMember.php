<?		include_once("../../PeoplePods.php");	
	
	$POD = new PeoplePod(array('lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth'],'debug'=>0));
	$POD->changeTheme('admin');

	$group = $_GET['group'];
	$person = $_GET['person'];
	$type = $_GET['type'];
	$action = 'add';
	
	if (@$_GET['action']) {
		$action = $_GET['action'];
	}
	// load parent
	$parent = $POD->getGroup(array('id'=>$group));
	if ($parent->success()) { 
	
		// load child
		$child = $POD->getPerson(array('id'=>$person));
		if ($child->success()) { 
		
			if ($action == 'add') { 
				$parent->addMember($child,$type);
			} else  if ($action == 'remove') {
				$parent->removeMember($child);
			}
			if ($parent->success()) { 
				// refill the children stack
				$parent->allmembers()->fill();
			} else {
				echo "Could not make change to membership! " . $parent->error();
			}
		
		} else {
			echo "Error with person! " . $child->error();
		}
		
		
	} else {
		echo "Error with group! " . $parent->error();
	}	
	
	
	while ($child = $parent->allmembers()->getNext()) { 
		$child->set('membership',$parent->isMember($child));
		$child->output('group');
	}
	
	if ($parent->allmembers()->count() == 0) { 
		echo '<p class="column_padding">This group has no members.</p>';
	}
	
	
?>
