<? 
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
			<h1><? echo $header; ?></h1>
			
			<ul>
				<li <? if ($mode=="friends") { ?>class="active"<? } ?>><a href="<? $POD->siteRoot(); ?>/friends">Friends</a></li>
				<li <? if ($mode=="followers") { ?>class="active"<? } ?>><a href="<? $POD->siteRoot(); ?>/friends/followers">Followers</a></li>
				<li <? if ($mode=="recommended") { ?>class="active"<? } ?>><a href="<? $POD->siteRoot(); ?>/friends/recommended">Recommended</a></li>
				<? if ($POD->libOptions('enable_core_invite')) { ?>
					<li><a href="<? $POD->siteRoot(); ?>/invite">Invite</a></li>
				<? } ?>
				<li id="people_search">
					<form action="<? $POD->siteRoot(); ?>/search" >
						<input id="people_search_p" name="p" class="repairField" data-default="search people" />
						<input type="submit" value="search" />
					</form>
				</li>
			</ul>
		</div>		

		<? $people->output('short','header','pager',null,'No people found!'); ?>
	
<?	$POD->footer(); ?>
