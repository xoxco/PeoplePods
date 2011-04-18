<?
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* core_feeds/feed.php
* Handles requests to /feeds/
	
		Generate lists of public documents by:
		
			user
			user's favorites
			tag
			type
			search keyword
			...and combinations of these as well!
			
			/feeds
			/feeds/users/benbrown
			/feeds/favorites/benbrown
			/feeds/tags/foo
			/feeds/user/benbrown/tags/foo
			/feeds/type/post
			/feeds/type/post/tag/foo
			/feeds/search/keyword
			/feeds/type/post/search/keyword


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



	require_once("class.rss.php");

	// ****************************************************************************//
	// FEED HELPER FUNCTIONS
	// ****************************************************************************//

	$asc2uni = Array();
	for($i=128;$i<256;$i++){
	  $asc2uni[chr($i)] = "&#x".dechex($i).";";   
	}
	
	function xmlformat($str){
		global $asc2uni;
		$str = str_replace("&", "&amp;", $str);
		$str = str_replace("<", "&lt;", $str); 
		$str = str_replace(">", "&gt;", $str); 
		$str = str_replace("'", "&apos;", $str);  
		$str = str_replace("\"", "&quot;", $str); 
		$str = str_replace("\r", "", $str);
		$str = strtr($str,$asc2uni);
		return $str;
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
	if ($param) {
		foreach ($param as $key => $value) { 
		
			switch($key) {
			
				case 'person': 
					$u = $POD->getPerson(array('stub'=>$value));
					if ($u->success()) { 
						$params['userId'] = $u->get('id');
						$scope = $u->get('nick') . "'s ";
					} else {
						$REDALERT = true;
					}
					break;
				case 'favorites':
					$u = $POD->getPerson(array('stub'=>$value));
					if ($u->success()) { 
						$params['flag.name'] = 'favorite';
						$params['flag.userId'] = $u->get('id');
						$scope = $u->get('nick') . "'s Favorites ";
					} else {
						$REDALERT = true;
					}
					break;			
				case 'tags':
					$params['t.value'] = $value;
					array_push($conditions,"Tagged '$value'");
					break;
				case 'search':
					$params['or'] = array('headline:like' => "%$value%",'body:like' => "%$value%");

					array_push($conditions,"Matching '$value'");
					break;
				case 'type':
					$params['type'] = $value;
					$type =  ucwords($value . 's');
					break;
				}
		}
	} else {
		$params['1']='1';
	}

	$count = 20;

	$DOCS = $POD->getContents($params);
	
	$description = "$scope $type " . implode(" and ",$conditions) . " from " . $POD->siteName(false);

		$year = date("Y");
		
		$rss = new rss('utf-8');
		$rss->channel(xmlformat($description),$POD->siteRoot(false),xmlformat($description));

		$rss->language('en-us');
		$rss->copyright('Copyright '.$year . ' ' . $POD->siteName(false));
	
		$rss->startRSS();	

		while ($doc = $DOCS->getNext()) {
		
			$rss->itemTitle(xmlformat($doc->get('headline')));
			if ($doc->get('link')) { 
				$rss->itemLink($doc->get('link'));			
			} else {
				$rss->itemLink($doc->get('permalink'));
			}
			$nTimestamp = strtotime($doc->get('date'));
			$sISO8601=date('Y-m-d\Th:i:s',$nTimestamp). substr_replace(date('O',$nTimestamp),':',3,0);

			$rss->itemPubDate( $sISO8601);
	
			if ($doc->get('img')) {
				$rss->itemDescription(xmlformat('<img src="' . $doc->get('img') . '" /><br />' . $doc->get('body')));

			} else {
				$rss->itemDescription(xmlformat($doc->get('body')));
			}
			$rss->itemAuthor(xmlformat($doc->author('nick') . "<" . $doc->author('permalink') .">"));
			$rss->itemGuid($doc->get('permalink'));
			$rss->itemSource($POD->siteName(false),$POD->siteRoot(false));
			$rss->addItem();
		}
		
		header("Content-type: text/xml");
		echo $rss->RSSdone();

?>