<?


	function gravatar($user,$width=null) { 
		if ($width==null) {
			$width = $user->POD->libOptions('peopleIconMaxWidth');
		}

		if ($img = $user->files()->contains('file_name','img')) {
			return $img->thumbnail;
		} else {
			$hash = md5( strtolower( trim( $user->email ) ) );
			return "http://www.gravatar.com/avatar/{$hash}?s={$width}";
		}	
	}
	
	Person::registerMethod('gravatar','avatar');