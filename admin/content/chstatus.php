<? 
	include_once("../../PeoplePods.php");	

	$POD = new PeoplePod(array('lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));
	
	if (@$_POST['id']) {
		$CGI = $_POST;
	} else if (@$_GET['id']) { 
	
		$CGI= $_GET;
	}

	if ($POD->currentUser()->get('adminUser')) {
		$doc = $POD->getContent(array('id'=>$CGI['id']));
		$doc->changeStatus($CGI['status']);
		if ($doc->success()) {
			header("Location: " . $_SERVER['HTTP_REFERER']);
		} else {
			echo "Status change failed! " . $doc->error();
		}
	}
?>

