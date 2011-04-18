<?		include_once("../../PeoplePods.php");	
	
	$POD = new PeoplePod(array('lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));
	$POD->changeTheme('admin');

	$id = $_GET['parent'];
	$child = $_GET['child'];
	$action = 'addChild';
	if (isset($_GET['action'])) {
		$action = $_GET['action'];
	}
	// load parent
	$parent = $POD->getContent(array('id'=>$id));
	if ($parent->success()) { 
	
		// load child
		$child = $POD->getContent(array('id'=>$child));
		if ($child->success()) { 
		
			if ($action == 'addChild') { 
//				echo "Setting documentId to " . $parent->get('id');
				$child->set('parentId',$parent->get('id'));
			} else  if ($action == 'removeChild') {
//				echo "Clearing documentId";
				$child->set('parentId',null);
			}
			$child->save();
			if ($child->success()) { 
				// refill the children stack
				$parent->children()->fill();
			} else {
				echo "Could not add child! " . $child->error();
			}
		
		} else {
			echo "Error with child document! " . $child->error();
		}
		
		
	} else {
		echo "Error with parent document! " . $parent->error();
	}	
	
	while ($child = $parent->children()->getNext()) { 
		$child->output('child');
	}
	
	if ($parent->children()->count() == 0) { 
		echo '<p class="column_padding">This content has no child content.</p>';
	}
	
	
?>
