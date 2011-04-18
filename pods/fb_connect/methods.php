<?

function fb_connect_settings() { 

	return array(
		'fb_connect_api'=>'FB API Key',
		'fb_connect_secret'=>'FB API Secret',
	);

}


function postToFacebook($user,$message,$title,$link,$picture=null,$description=null) {
	
	
		if ($user->get('facebook_token')) { 
			$fields['access_token'] = $user->get('facebook_token');
			$fields['name'] = $title;
			$fields['message'] = $message;
			$fields['link'] = $link;
			$fields['picture'] = $picture;
			$fields['description'] = $description;
			$url = 'https://graph.facebook.com/' . $user->get('fbuid') . '/feed';
			
			
			foreach($fields as $key=>$value) { $fields_string .= '&' . $key.'='.urlencode(stripslashes($value)); } 		
			//open connection 
			$ch = curl_init();
			//set the url, number of POST vars, POST data 
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_POST,count($fields)+1);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			//execute post 
			$result = curl_exec($ch);
			//close connection 
			curl_close($ch);
		} else {
			$result = false;
		}
		
		return $result;
	
}


function getFacebookFriendIds($user) { 
		$cacheExpire = 24* 60*60;
		$POD = $user->POD;

		$friends = array();
	
		if ($user->get('facebook_token')) { 
			
			if ($user->get('facebook_list')!='' && (time() - $user->get('facebook_list_generated') < $cacheExpire)) { 
				//error_log("Loading cached facebook list");
				$twoots = json_decode($user->get('facebook_list'));
				foreach ($twoots as $f) { $friends[] = $f; }
			} else {
				//error_log("Loading Facebook list from API");
				try { 
	
					$info = json_decode(file_get_contents("https://graph.facebook.com/me/friends?access_token=" . $user->facebook_token));
	
					if ($info) { 
						foreach ($info->data as $person) { 			
							$friends[] = $person->id;
						} 
					} else {
						$user->throwError("Facebook API call failed!");

					}
				} catch (Exception $e) {
					$show_fb_connect = true;
				}
				$user->addMeta('facebook_list',json_encode($friends));
				$user->addMeta('facebook_list_generated',time());
			}	
		}
			
		return $friends;
}



function getFacebookFriends($user) {

		if ($user->get('facebook_token')) { 

			$friends = $user->getFacebookFriendIds();
			if (sizeof($friends)>0) { 
				return $user->POD->getPeople(array('fbuid'=>$friends),null,1000);
			} else {
				return new Stack($user->POD,'person');		
			}		
			
		} else {
			$user->throwError("Can't get Facebook friends for user without facebook token!");
			return new Stack($user->POD,'person');		
		}		
}



function getFacebookInfo($user) {

	if ($user->facebook_token) { 
		$json = json_decode(file_get_contents("https://graph.facebook.com/me?access_token=" . $user->facebook_token));
		return $json;
	} else {
		$user->throwEror("Can't get Facebook info for user without facebook token!");
		return null;
	}
}


function isFacebookFriend($user,$friend) {
	if ($friend->fbuid) {
		$friends = $user->getFacebookFriendIds();
		return (in_array($friend->fbuid,$friends));
	
	} else {
		return false;
	}	

}


Person::registerMethod('getFacebookFriendIds');
Person::registerMethod('getFacebookFriends');
Person::registerMethod('getFacebookInfo');
Person::registerMethod('postToFacebook');
Person::registerMethod('isFacebookFriend');

?>