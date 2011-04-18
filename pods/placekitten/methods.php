<?


	// 
	function placekitten($user,$width=null) { 
		if ($width==null) {
			$width = $user->POD->libOptions('peopleIconMaxWidth');
		}

		if ($img = $user->files()->contains('file_name','img')) {
			return $img->thumbnail;
		} else {
			return "http://placekitten.com/{$width}/{$width}";
		}	
	}
	
	Person::registerMethod('placekitten','avatar');