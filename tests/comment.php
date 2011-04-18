
<?php
/*

Functions Tested

->addComment()
->delete()
->author()
->parent()
->comments()
->getComments()
->getComment


/////////////
Fields tested

id	 
contentId	 
userId	 
id	 
comment	 
date	 
type	 
permalink	 
minutes	




*/
require_once(dirname(__FILE__) . '/simpletest/autorun.php');

include_once("../PeoplePods.php");


class TestOfComment extends UnitTestCase {


	function testCommentFields(){
	
		$POD = new PeoplePod();

		$user = $POD->getPerson();
		$user->email = 'ben+test@example.com';//create user
		$user->password = 'foo';
		$user->nick = 'test';
		$user->save(); 
		
		$POD->changeActor(array('id'=>$user->id));
		
		$content = $POD->getContent();
		$content->set('headline', 'this is the headline');
		$content->set('type', 'this is the type');
		$content->save();
		
		$comment= $content->addComment('amazing!');
		
		$this->assertIsA($comment, 'Comment');
		$this->assertNotNull($comment->get('id'));
		$this->assertEqual($comment->get('contentId'), $content->get('id'));
		$this->assertEqual($comment->get('userId'),$user->id);
		$this->assertEqual($comment->get('comment'), 'amazing!');
		$this->assertNotNull($comment->get('date'));
		$this->assertNull($comment->get('type'));
		
		$comment->type= 'rating';
		
		$this->assertEqual($comment->get('type'), 'rating');
		$this->assertNotNull($comment->get('permalink'));
		$this->assertNotNull($comment->get('minutes'));
		
		$comment->delete();
		$this->assertTrue($comment);
		
		$content->delete();
		$user->delete();	
	}

	function testCommentFunctions(){
	
		$POD = new PeoplePod();

		$user = $POD->getPerson();
		$user->email = 'ben+test@example.com';//create user
		$user->password = 'foo';
		$user->nick = 'test';
		$user->save(); 
		
		$POD->changeActor(array('id'=>$user->id));
		
		$content = $POD->getContent();
		$content->set('headline', 'this is the headline');
		$content->set('type', 'this is the type');
		$content->save();
		
		$comment= $content->addComment('amazing!');
		$comment->type= 'rating';
		
		$comment2= $content->addComment('blah');
		$comment->type= 'rating';
		
		$this->assertEqual($comment->author()->get('id'), $user->id);
		$this->assertEqual($comment->parent()->id, $content->id);
		
		$comment_stack= $content->comments();
		$this->assertEqual($comment_stack->count(), 2);
		
		$next_comm= $comment_stack->getNext();
		$this->assertIsA($next_comm, 'Comment');
		
		$pod_comm_stack= $POD->getComments();
		$pod_comm= $pod_comm_stack->getNext();
		$this->assertIsA($pod_comm, 'Comment');
		
		//test getComment to return a specific comment
		$pull_comm= $POD->getComment(array('id'=>$comment2->id));
		$this->assertEqual($pull_comm->comment, 'blah');
		
		//test getComment to create a comment and associate it with a person and content obj
		$new_comment= $POD->getComment();
		$new_comment->set('comment','This comment sucks!');
		$new_comment->set('userId', $user->id); 
		$new_comment->set('contentId',$content->get('id')); 
		$new_comment->save(); 
		
		$comment_stack= $POD->getComments(array('userId'=>$user->id));
		$this->assertEqual($comment_stack->count(), 3);
		
		$comment->delete();
		$content->delete();
		$user->delete();	
	
	}
}
?>


