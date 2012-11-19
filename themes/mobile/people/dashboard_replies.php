<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/people/dashboard_replies.php
* Used by the dashboard module to create /replies
* Displays a list of content the current user has in his watched() list 
* using the theme/content/new_comments template
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/person-object
/**********************************************/
?>

<?	

	$offset = 0;	
	if (isset($_GET['offset'])) {
		$offset = $_GET['offset'];
	}

	// load up a list of groups that this user is a member of
	$groups = $POD->getGroups(array('mem.type:!='=>'invitee','mem.userId'=>$user->get('id')));
	
	?>
	
	
	<div class="column_8">
		<? if (isset($msg)) { ?>	
			<div class="info">
				<? echo $msg; ?>
			</div>
		<? } ?>
		<!-- this is where posts with new comments show up -->
		<div id="replies">
			<? 
				$watched = $user->watched()->getOtherPage($offset);
				// since the watched list results might be cached, 
				// we should resort the list by the commentDate, 
				// which should have been updated in the cache
				$watched->sortBy('commentDate');
				$watched->output('new_comments','header','pager','New Comments','There are no new comments yet!'); 
			?>
		</div>	
	</div>
