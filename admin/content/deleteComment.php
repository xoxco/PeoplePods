<?		include_once("../../PeoplePods.php");	

	$POD = new PeoplePod(array('lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));
	$POD->changeTheme('admin');


	$commentId = $_GET['id'];
	if ($commentId != '') { 
		$comment = $POD->getComment(array('id'=>$commentId));
		if ($comment->success()) {
		
			// get parent so we can output the comment list
			$parent = $comment->parent();
			$comment->delete();
			if ($comment->success()) {
				
				// now we can just iterate through the parent comment loop.  we haven't accessed it yet,
				// so it will load fresh and NOT include the deleted comment.
				if ($parent->comments()->count() > 0) { 
					while ($c = $parent->comments()->getNext()) {
						$c->output('comment_edit');
					}
				} else {
					echo '<p class="column_padding">This post has no comments</p>';
				}
							
			} else {
				// comment failed to delete
				echo '<p class="column_padding">Error: ' . $comment->error .'</p>';

			}
				
		} else {
			// comment failed to load	
			echo '<p class="column_padding">Error: ' . $comment->error . '</p>';
		}

	} else {
		// no comment id passed in	
		echo '<p class="column_padding">Error: No comment specified.</p>';
	
	}
?>