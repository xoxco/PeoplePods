<?

	include_once("../../PeoplePods.php");
	$POD = new PeoplePod(array('debug'=>0,'authSecret'=>@$_COOKIE['pp_auth']));

	// this page is only available to facebook users.
	if (!$POD->isAuthenticated() || !$POD->currentUser()->twitter_token) { 
		header("Location: /twitter");
		exit;
	}
	
	$POD->header("Twitter Friends");
	
	$friends = $POD->currentUser()->getTwitterFriends();
	$friends->output('short','header','footer','Twitter Friends','None of your Twitter friends are members of this site.');

	$POD->footer();