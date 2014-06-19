<?
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* core_feeds/list.php
* Handles requests to /lists/
	
		Generate lists of public documents by:
		
			user
			user's favorites
			tag
			type
			search keyword
			...and combinations of these as well!
	
			/lists/users/benbrown
			/lists/favorites/benbrown
			/lists/tags/foo
			/lists/user/benbrown/tags/foo
			/lists/type/post
			/lists/type/post/tag/foo
			/lists/search/keyword
			/lists/type/post/search/keyword


*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme
/**********************************************/



	include_once("../../PeoplePods.php");
	$POD = new PeoplePod(array('authSecret'=>@$_COOKIE['pp_auth']));
	if (!$POD->libOptions('enable_core_feeds')) { 
		header("Location: " . $POD->siteRoot(false));
		exit;
	}


	// lets parse the parameters we got passed in so we know what kind of feed to create
	
	$arguments = explode("/",$_GET['args']);
	
	$field = null;
	foreach ($arguments as $arg) { 
		if ($field) {
			$param[$field] = $arg;
			$field = null;
		} else {
			$field = $arg;
		}
	}


	// set up our fail indicator var
	$REDALERT = false;
	
	// set up getDocuments parameters based on the URL parameters
	$params = array();

	$scope = "All ";
	$type = " Posts ";
	$conditions = array();
	$baseUrl = $tBaseUrl = $pBaseUrl = $POD->siteRoot(false) . "/lists";
	$u = null;
	$t = null;
	if ($param) {
		foreach ($param as $key => $value) { 
	
		switch($key) {
		
			case 'person': 
				$u = $POD->getPerson(array('stub'=>$value));
				if ($u->success()) { 
					$baseUrl .= "/person/" . $u->get('stub');
					$tBaseUrl .= "/person/" . $u->get('stub');
					$params['userId'] = $u->get('id');
					$scope = $u->get('nick') . "'s ";
				} else {
					$REDALERT = true;
				}
				break;
			case 'favorites':
				$u = $POD->getPerson(array('stub'=>$value));
				if ($u->success()) { 
					$baseUrl .= "/favorites/" . $u->get('stub');
					$tBaseUrl .= "/favorites/" . $u->get('stub');
					$params['flag.name']='favorite';
					$params['flag.userId'] = $u->get('id');
					$scope = $u->get('nick') . "'s Favorites ";
				} else {
					$REDALERT = true;
				}
				break;			
			case 'tags':
				$params['t.value'] = $value;
				$baseUrl .= "/tags/" . $value;
				$pBaseUrl .= "/tags/" . $value;

				$t = $value;
				array_push($conditions,"Tagged '$value'");
				break;
			case 'search':
				$baseUrl .= "/search/" . $value;
				$tBaseUrl .= "/search/" . $value;
				$pBaseUrl .= "/search/" . $value;

				$params['or'] = array('headline:like' => "%$value%",'body:like' => "%$value%");
				array_push($conditions,"Matching '$value'");
				break;
			case 'type':
				$params['type'] = $value;
				$baseUrl .= "/type/" . $value;
				$tBaseUrl .= "/type/" . $value;
				$pBaseUrl .= "/type/" . $value;

				$type =  ucwords($value . 's');
				break;
			}
		}
	} else {
		// no parameters were specified, we should send this person back to the homepage
		header("Location: " . $POD->siteRoot(false));
		exit;
	}
	$count = 20;
	$offset = 0;
	if (isset($_GET['offset'])) {
		$offset = $_GET['offset'];
	}
	
	$docs = $POD->getContents($params,null,$count,$offset);
	
	$description = "$scope $type " . implode(" and ",$conditions);
	$feedurl = $POD->siteRoot(false) . '/feeds/' . $_GET['args'];

	$POD->header($description,$feedurl);

	$docs->output('short','2col_header','2col_pager',$description); 

	$POD->footer();

?>