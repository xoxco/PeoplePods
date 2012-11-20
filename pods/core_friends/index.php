<?php 
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* core_friends/index.php
* Handles requests to /friends 
* Handles requests to /friends/followers
* Handles requests to /friends/recommended
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme
/**********************************************/

	
		include_once("../../PeoplePods.php");
	$POD = new PeoplePod(array('lockdown'=>'login','authSecret'=>@$_COOKIE['pp_auth'],'debug'=>0));
	if (!$POD->libOptions('enable_core_friends')) { 
		header("Location: " . $POD->siteRoot(false));
		exit;
	}
	
	$max_friends = 20;
	
	$offset = 0;
	if (isset($_GET['offset'])) {
		$offset = $_GET['offset'];
	}

	$mode = "friends";
	if (isset($_GET['mode'])) {
		$mode = $_GET['mode'];
	}
	
	if ($mode == "friends") {
		$people = $POD->currentUser()->friends($max_friends,$offset);
		$title = "My Friends";
		$header = "You have " . $people->totalCount() . " " . $POD->pluralize($people->totalCount(),'friend','friends');
	}
	
	if ($mode=="followers") { 
		$people = $POD->currentUser()->followers($max_friends,$offset);
		$title = "My Followers";
		$header = "You have " . $people->totalCount() . " " . $POD->pluralize($people->totalCount(),'follower','followers');
	}
	
	if ($mode=="recommended") { 
		$people = $POD->currentUser()->recommendFriends(2);
		$title = "Recommended Friends";
		$header = "You might like these folks";

	}
	$POD->header($header);
	
	?>
		<div id="friends_actions">
			<h1><?php echo $header; ?></h1>
			
			<ul>
				<li <?php if ($mode=="friends") { ?>class="active"<?php } ?>><a href="<?php $POD->siteRoot(); ?>/friends">Friends</a></li>
				<li <?php if ($mode=="followers") { ?>class="active"<?php } ?>><a href="<?php $POD->siteRoot(); ?>/friends/followers">Followers</a></li>
				<li <?php if ($mode=="recommended") { ?>class="active"<?php } ?>><a href="<?php $POD->siteRoot(); ?>/friends/recommended">Recommended</a></li>
				<?php if ($POD->libOptions('enable_core_invite')) { ?>
					<li><a href="<?php $POD->siteRoot(); ?>/invite">Invite</a></li>
				<?php } ?>
				<li id="people_search">
					<form action="<?php $POD->siteRoot(); ?>/search" >
						<input id="people_search_p" name="p" class="repairField" data-default="search people" />
						<input type="submit" value="search" />
					</form>
				</li>
			</ul>
		</div>		

		<?php $people->output('short','header','pager',null,'No people found!'); ?>
	
<?php	$POD->footer(); ?>
