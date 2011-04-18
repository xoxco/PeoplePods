<? 
	include_once("../../PeoplePods.php");	
	
	$POD = new PeoplePod(array('lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));
	$POD->changeTheme('admin');
	
	$conditions = array();

	if (isset($_GET['q']) && $_GET['q'] != 'Search') { 
		$conditions['groupname:like'] = '%' . $_GET['q'] . '%';	
	}	
	if (isset($_GET['type'])) {
		$conditions['type'] = $_GET['type'];
	}
	if (isset($_GET['userId'])) {
		$conditions['userId'] = $_GET['userId'];
	}

	$offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
	
	if (sizeof($conditions) > 0) {
		$groups = $POD->getGroups($conditions,'date DESC',20,$offset);	
	} else {	
		$groups = $POD->getGroups(array('1'=>1),'date DESC',20,$offset);
	}
	
	
	$message = isset($_GET['msg']) ? $_GET['msg'] : null;
		$POD->header();

		include_once("tools.php");

		if (isset($message)) { ?>
		
			<div class="info">
				<? echo $message; ?>
			</div>
		
		<? } ?>

		<div class="list_panel">
			<h1>Groups</h1>
			<? $groups->output('short','group_header','table_pager'); ?>
		
		</div>

	<?	$POD->footer(); ?>