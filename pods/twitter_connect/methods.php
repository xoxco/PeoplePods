<?

function twitter_connect_settings() { 

	return array(
		'twitter_api'=>'Twitter API Key',
		'twitter_secret'=>'Twitter API Secret',
	);

}


function postToTwitter($user,$message) {
	$POD = $user->POD;
	$user->success(false);
	$key = $POD->libOptions('twitter_api');
	$secret = $POD->libOptions('twitter_secret');
	
	
	if ($user->get('twitter_token')) { 	
		try {
		
			$oauth = new OAuth($key,$secret,OAUTH_SIG_METHOD_HMACSHA1);
			$oauth->setToken($user->get('twitter_token'),$user->get('twitter_secret'));
			$args = array('status'=>$message);
			$oauth->fetch('http://twitter.com/statuses/update.json',$args,OAUTH_HTTP_METHOD_POST);
			$json = json_decode($oauth->getLastResponse(),true);
			if(isset($json['id'])) {
				$user->success(true);
				return $json;
			} else {
				$user->throwError('twitter_error');
				return $json;			
			}
		} catch(OAuthException $E) {
			$user->throwError('exception ' . $E->getMessage());
			return $E;
		}
	
	} else {
	
		$user->throwError("no_twitter");
	
	}
	
	return $user->success();
	
}

function isTwitterFriend($user,$friend) {
	if ($friend->twitter_id) {
		$friends = $user->getTwitterFriendIds();
		return (in_array($friend->twitter_id,$friends));
	} else {
		return false;
	}	
}


function getTwitterFriendIds($user) { 
		$cacheExpire = 24*60*60;		
		$POD = $user->POD;
		
		$key = $POD->libOptions('twitter_api');
		$secret = $POD->libOptions('twitter_secret');
		
		$friends = array();
	
		if ($user->get('twitter_token')) { 

			if ($user->get('twitter_list')!='' && (time() - $user->get('twitter_list_generated') < $cacheExpire)) { 
				$twoots = json_decode($user->get('twitter_list'));
				foreach ($twoots as $f) { $friends[] = $f; }
			} else { 
	
				try {
					$oauth = new OAuth($key,$secret,OAUTH_SIG_METHOD_HMACSHA1,OAUTH_AUTH_TYPE_URI);
					$oauth->enableDebug();  // This will generate debug output in your error_log
					$oauth->setToken($user->get('twitter_token'),$user->get('twitter_secret'));
					$oauth->fetch('https://twitter.com/friends/ids.json?cursor=-1&user_id=' . $user->get('twitter_id')); 
					$json = json_decode($oauth->getLastResponse());
				} 
				catch (Exception $e) { 
				
				}
				// contains the first 5000 twitter friends
				
				foreach ($json->ids as $id) { 
					$friends[] = $id;
				}
				
				$user->addMeta('twitter_list',json_encode($friends));
				$user->addMeta('twitter_list_generated',time());
			}
			
		}
		return $friends;

}

function getTwitterFriends($user) { 
		$cacheExpire = 24*60*60;		
		$POD = $user->POD;
		
		if ($user->get('twitter_token')) { 
			$friends = $user->getTwitterFriendIds();
			if (sizeof($friends)>0) { 
				return $user->POD->getPeople(array('twitter_id'=>$friends));
			} else { 
				return new Stack($POD,'person');		
			}
				
		} else {
				return new Stack($POD,'person');		
		}

}

Person::registerMethod('getTwitterFriendIds');
Person::registerMethod('getTwitterFriends');
Person::registerMethod('postToTwitter');
Person::registerMethod('isTwitterFriend');



?>