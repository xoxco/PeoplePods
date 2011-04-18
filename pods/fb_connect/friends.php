<?

	include_once("../../PeoplePods.php");
	$POD = new PeoplePod(array('debug'=>0,'authSecret'=>@$_COOKIE['pp_auth']));

	// this page is only available to facebook users.
	if (!$POD->isAuthenticated() || !$POD->currentUser()->facebook_token) { 
		header("Location: /facebook");
		exit;
	}
	
	$POD->header("Facebook Friends");
	
	$friends = $POD->currentUser()->getFacebookFriends();
	$friends->output('short','header','footer','Facebook Friends','None of your Facebook friends are members of this site.');

	$POD->footer();