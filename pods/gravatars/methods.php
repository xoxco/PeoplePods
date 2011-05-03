<?

	function gravatar($user,$width=null) { 
		if ($width==null) {
			echo "NULLLLL";
			$width = $user->POD->libOptions('peopleIconMaxWidth');
		}

		if ($img = $user->files()->contains('file_name','img')) {
			if ($width == $user->POD->libOptions('peopleIconMaxWidth')) {
				return $img->thumbnail;
			} else {
				return $img->src($width,true);
			}
		} else {
			$hash = md5( strtolower( trim( $user->email ) ) );
			return "http://www.gravatar.com/avatar/{$hash}?s={$width}";
		}	
	}
	
	Person::registerMethod('gravatar','avatar');