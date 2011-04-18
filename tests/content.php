<?php
/*
///////////////////////////

Tested Functions

//////////////////////////

$Content->get()
$Content->set()
$Content->author() - returns a person object defined by $content's userId field.
$Content->creator() - returns a person defined by $content's createdBy field.

$Content->addMeta()
$Content->removeMeta()
$Content->getMeta()
$Content->addFlag()
$Content->removeFlag()
$Content->toggleFlag()
$Content->hasFlag()
$Content->success()
$Content->error()


$Content->children() 
$Content->parent()
$Content->comments()
$Content->files()
$Content->tags()

$Content->isEditable()
$Content->save()
$Content->delete()
$Content->addComment()
$Content->markCommentsAsRead()
$Content->hasTag()
$Content->addTag()
$Content->removeTag()
$Content->tagsFromString()
$Content->tagsAsString()
$Content->group()
$Content->setGroup()
$Content->changeStatus()
$Content->vote()
$Content->unvote()


///////////////////////////////

Untested Functions

//////////////////////////////



$Content->write()
$Content->writeFormatted()
$Content->htmlspecialwrite()
$Content->errorCode()
$Content->asArray()

$Content->output()
$Content->goToFirstUnreadComment()
$Content->permalink()

*/
require_once(dirname(__FILE__) . '/simpletest/autorun.php');


include_once("../PeoplePods.php");

class TestOfContent extends UnitTestCase {
    
    function testContentCreate() { 
    
    	$POD = new PeoplePod(array('authSecret'=>'234234234'));

		$content = $POD->getContent();
		
		$this->assertIsA($content,'Content');
		$this->assertNull($content->id);

		// this should fail, because the object doesn't have any data
		$content->save();
		
		$this->assertFalse($content->success());
		$this->assertNull($content->id);

		
		$content->headline = 'test document';

		// this should fail, because the object doesn't have any data
		$content->save();
		$error_message = $content->error();
		$this->assertNotNull($error_message);
		
		$this->assertFalse($content->success());
		$this->assertNull($content->id);
		
		$content->type = 'test';

		// this should fail, because the pod is not authenticated
		$content->save();
		
		$this->assertFalse($content->success());
		$this->assertNull($content->id);
		
    	$user = $POD->getPerson();
		$user->email = 'ben+test@example.com';
		$user->password = 'foo';
		$user->nick = 'test';
		$user->save();
		
		$POD->changeActor(array('id'=>$user->id));
		
		$content = $POD->getContent();
		
		$this->assertIsA($content,'Content');
		$this->assertNull($content->id);
		
		$content->headline = 'test document';
		$content->type = 'test';

		$content->save();
		
		$this->assertTrue($content->success());
		
		$this->assertNotNull($content->id);
		$this->assertNotNull($content->date);
		$this->assertNotNull($content->userId);
		$this->assertEqual($content->get('userId'),$user->id);
		$this->assertEqual($content->userId,$user->id);
		$this->assertEqual($content->author()->id,$user->id);
		$this->assertEqual($content->owner()->id,$user->id);
		$this->assertEqual($content->creator()->id,$user->id);
		
		$content->delete();
		$user->delete();
	    
    }

	//TestContentStausField: test's for all standard status settings, and one non-standard setting
	function TestContentStatusField(){
		
		$POD = new PeoplePod();

		$user = $POD->getPerson();
		$user->email = 'ben+test@example.com';//create user
		$user->password = 'foo';
		$user->set('nick','test');
		$user->save(); 
		
		
		
		$POD->changeActor(array('id'=>$user->id));//log in as user
		
		$content = $POD->getContent();
		$content->set('headline', 'this is the headline');
		$content->set('type', 'this is the type');
		$content->save();
		
		$nick = $user->get('nick');
		$this->assertIdentical($nick, 'test');
		
		$this->assertIdentical($content->status, "new");
		
		$content->changeStatus("featured");
		$this->assertIdentical($content->status, "featured");
		
		$content->changeStatus("approved");
		$this->assertIdentical($content->status, "approved");
		
		$content->changeStatus("something");
		$this->assertIdentical($content->status, "something");	
		
		
		$content->delete();
		$user->delete(); 
	}
	
	
	//tests initialized votes, increment/decrement votes, and correct voting percentage
	function TestVoting(){
		
		$POD = new PeoplePod();
		$POD->debug(1);
		$user = $POD->getPerson();
		$user->email = 'ben+test@example.com';//create user
		$user->password = 'foo';
		$user->nick = 'test';
		$user->save(); 
		
		$POD->changeActor(array('id'=>$user->id));//log in as user
		
		$content = $POD->getContent();
		$content->set('headline', 'this is the headline');
		$content->set('type', 'this is the type');
		$content->save();
	
		for ($i = 0; $i < 3; $i++) { 
			
			//Test all vote fields initialized to zero
			$this->assertEqual($content->yes_votes, 0);
			$this->assertEqual($content->no_votes, 0);
			$this->assertEqual($content->yes_percent,0);
			$this->assertEqual($content->no_percent,0);
			
			////// begin yes_vote tests //////////
	
	
			//upvote
			$content->vote('y');
			$this->assertEqual($content->yes_votes, 1);
	
			
			//attempts to upvote twice
			$content->vote('y');
			$this->assertEqual($content->yes_votes, 1);
			
	
	
			//Test upvote percent
			$this->assertEqual($content->yes_percent, 100);
	
			
			//vote('n')should return false because it was not unvoted. There cannot be both a yes & no vote on one 				contentObj
			// yes_vote should be unaffected
			$content->vote('n');
			$this->assertEqual($content->no_votes,1);        //downvote should not count
			$this->assertEqual($content->yes_votes, 0);    //upvote should remain unchanged
			$this->assertEqual($content->yes_percent, 0);//upvote % should be unchanged
			$this->assertEqual($content->no_percent, 100);//upvote % should be unchanged
	
			
			//test unvote
			$content->unvote();                           
			$this->assertEqual($content->yes_votes, 0);   //upvote should clear
			$this->assertEqual($content->yes_percent, 0); //percent should return to 0
			$this->assertEqual($content->no_votes, 0);       //no_votes should stil be 0      
			$this->assertEqual($content->no_percent, 0);  //no percent should be 0
			
			
			////// begin no_vote tests
			// same test sequence as above, but using no_votes
			$content->vote('n');
			$this->assertEqual($content->no_votes, 1);
			
			//attempt to add two no_votes
			$content->vote('n');
			$this->assertEqual($content->no_votes, 1);
			$this->assertEqual($content->no_percent, 100);
			
			//attempt to add upvote on top of no_vote. Should not be allowed
			$content->vote('y');
			$this->assertEqual($content->yes_votes, 1);
			$this->assertEqual($content->yes_percent, 100);
			$this->assertEqual($content->no_percent, 0);
			
			//remove vote
			$content->unvote();
			$this->assertEqual($content->yes_votes, 0); 
			$this->assertEqual($content->yes_percent, 0);
			$this->assertEqual($content->no_votes, 0);          
			$this->assertEqual($content->no_percent, 0); 
			
			//unvote empty vote set
			$content->unvote();
			$this->assertEqual($content->yes_votes, 0); 
			$this->assertEqual($content->yes_percent, 0);
			$this->assertEqual($content->no_votes, 0);          
			$this->assertEqual($content->no_percent, 0); 
		
		}
		
		/// content-votes are all set to zero, now users will be added to vote and test %'s
		$content->vote('y'); //start with one upvote
		
		
		//create a new user
		$user2 = $POD->getPerson();
		$user2->email = '2+test@example.com';//create user
		$user2->password = 'foo';
		$user2->nick = 'test2';
		$user2->save();
		
		$this->assertTrue($user2->success());
		$this->assertNotNull($user2->id);
		
		$POD->changeActor(array('id'=>$user2->id));
		
		$this->assertTrue($POD->isAuthenticated());
		$this->assertEqual($POD->currentUser()->nick, $user2->nick);
		
		$content->vote('y');
		$this->assertEqual($content->yes_votes, 2);    //check to make sure up_votes are incremented
		$this->assertEqual($content->yes_percent, 100);
		
		$content->unvote();
		$content->vote('n');
		$this->assertEqual($content->yes_percent, 50);
		$this->assertEqual($content->no_percent, 50);
		
		//create yet another user to test %'s with a decimal value
		
		$user3 = $POD->getPerson();
		$user3->email = '3+test@example.com';//create user
		$user3->password = 'foo';
		$user3->nick = 'test3';
		$user3->save();
		
		$POD->changeActor(array('id'=>$user3->id));
		$this->assertTrue($POD->isAuthenticated());
		$this->assertEqual($POD->currentUser()->nick, $user3->nick);
		
		$content->vote('y');//there should now be 2 upvotes and 1 down
	
		$this->assertEqual($content->yes_votes, 2);
		$this->assertEqual($content->no_votes, 1);
		$this->assertEqual($content->yes_percent, 66);
		$this->assertEqual($content->no_percent, 33);
		

		error_log("------------------------------------------------------");
		error_log("END VOTE TESTS");
	

		
		// clean up
		$content->delete(); 
		
		$user3->delete();
		
		$POD->changeActor(array('id'=>$user2->id));
		$user2->delete();
		
		$POD->changeActor(array('id'=>$user->id));
		$user->delete();
	}
	
	function Test_Link_CreatedBy_Privacy(){
	
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
		
		
		//test link field initialized to null, fill field, test to check for link
		
		//$this->assertNull($content->link);**** Link is not currently initialized by NULL 9/3/10
		$this->assertEqual($content->link, 0);
		$this->assertEqual($content->link, "");
		
		$content->link = "https://www.google.com/";
		$this->assertEqual($content->link, "https://www.google.com/");
		
		//test created by
		$this->assertEqual($content->createdBy, $user->id);
		
		//test privacy settings
		$this->assertEqual($content->privacy, "public");
		
		$content->privacy= "friends_only";
		$this->assertEqual($content->privacy, "friends_only");
		
		$content->privacy= "group_only"; 
		$this->assertEqual($content->privacy, "group_only");
		
		$content->privacy= "owner_only";
		$this->assertEqual($content->privacy, "owner_only");
		
		$content->privacy= "arbitrary";
		$this->assertEqual($content->privacy, "arbitrary");		
		
		$content->delete();
		$user->delete();
		
	}
	
	//test date functions related to content objects
	function TestDate(){
	
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
		
		$format= 'Y-m-d H:i:s';
        $today = date($format, strtotime("now"));
		
		$this->assertNotNull($content->date);
		$this->assertEqual($content->date, $today);
		$this->assertEqual($content->editDate, $today);
		$this->assertEqual($content->changeDate, $today);
		
		
		//add comment then check to see if commentDate field updates
		$this->assertNull($content->commentDate);
		$content->addComment('New comments');
		$this->assertNotNull($content->commentDate);

		//check flagDate, update status, check to see if flagDate updates
		$this->assertNull($content->flagDate);
		$content->changeStatus("approved");
		$this->assertNotNull($content->flagDate);
				
		$content->delete();
		$user->delete();		
	}

	function Test_stub(){
	
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
		
		$this->assertNotNull($content->stub);
		$this->assertNotNull($content->permalink('headline',true));
		
		$content->delete();
		$user->delete();
	}
	
	//Below tests addMeta, getMeta, and removeMeta
	function Test_content_meta(){
	
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
		
		//addMeta
		$content->addMeta('new_meta', 'new_meta_value');
		$new_meta= $POD->getContents(array('type'=>'new_meta'));
		$this->assertIsA($new_meta, 'stack');
		
		//getMeta
		$meta = $content->getMeta();
		$test_this = $meta['new_meta'];
		$this->assertEqual($test_this, 'new_meta_value');
		
		// remove meta
		$content->removeMeta('new_meta');
		$test_meta = $content->get('new_meta');
		$this->assertNull($test_meta);
		
		$content->delete();
		$user->delete();

	}
	
	//Test addFlag, hasFlag, toggleFlag, removeFlag
	function Test_flags(){
	
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
		
		$flag = 'awesome';
		
		//test addFlag, hasFlag
		$this->assertFalse($POD->currentUser()->hasFlag($flag, $user));
		$user->addFlag($flag, $user);
		$this->assertTrue($POD->currentUser()->hasFlag($flag, $user));
		
		//test toggleFlag
		$flag= FALSE;
		$this->assertFalse($content->hasFlag($flag, $user));
		$content->toggleFlag($flag, $user);
		$this->assertTrue($content->hasFlag($flag, $user));
		$content->toggleFlag($flag, $user);
		$this->assertFalse($content->hasFlag($flag, $user));
		$content->toggleFlag($flag, $user);
		$this->assertTrue($content->hasFlag($flag, $user));
		
		//test RemoveFlag, both boolean flag and string flag
		$content->removeFlag($flag, $user);
		$this->assertFalse($content->hasFlag($flag, $user));
		$flag = 'awesome';
		$user->removeFlag($flag, $user);
		$this->assertFalse($POD->currentUser()->hasFlag($flag, $user));
		
		$content->delete();
		$user->delete();
	}
	
	//Tests addTag, removeTag, hasTag, tagsFromString, tagsAsString
	//also tests changeStatus()
	function Test_tags(){

		$POD = new PeoplePod();

		$user = $POD->getPerson();
		$user->email = 'ben+test@example.com';//create user
		$user->password = 'foo';
		$user->nick = 'test';
		$user->save();
		
		$POD->changeActor(array('id'=>$user->id)); 
		
		$content = $POD->getContent();
		$content->set('headline', 'this is the test_tags headline');
		$content->set('type', 'this is the type');
		$content->save();

		$this->assertTrue($content->isEditable());
		$content->addComment('new comment');
		
		$content->addTag('foo');
  		
  		$this->assertTrue($content->hasTag('foo'));
  		
  		$content->removeTag('foo');
  		$this->assertFalse($content->hasTag('foo'));
  		
  		
  		$content->addTag('foo1');
  		$content->addTag('foo2');
  		
  		$string = "foo bar baz";
		$content->tagsFromString($string);
		
		$this->assertFalse($content->hasTag('foo1'));//'foo1 & foo2 should be removed
		
		$this->assertTrue($content->hasTag('foo'));
		$this->assertTrue($content->hasTag('bar'));
		$this->assertTrue($content->hasTag('baz'));
  		
  		$string_tags = $content->tagsAsString();
  		
  		$this->assertEqual($string, $string_tags);
  		
  		$content->changeStatus('featured');
  		
  		$this->assertEqual($content->status, 'featured');
  		
  		//test to ensure that changeStatues also changed the $content->date
  		//$format= 'Y-m-d H:i:s';
        //$today = date($format, strtotime("now"));
		
		$this->assertNotNull($content->date);
		$this->assertNotNull($content->date);
  	
  		$content->delete();
		$user->delete();
	}
	
	function Test_relationship_functions(){
	
		$POD = new PeoplePod();
			
		$user = $POD->getPerson();
		$user->email = 'ben+test@example.com';//create user
		$user->password = 'foo';
		$user->nick = 'test';
		$user->save();
		
		$POD->changeActor(array('id'=>$user->id)); 
		
		$content = $POD->getContent();
		$content->set('headline', 'this is the test_tags headline');
		$content->set('type', 'this is the type');
		$content->save();
		
		$new_content = $POD->getContent();
		$new_content->set('headline','new child document');
		$new_content->set('type','post');
		
		$new_content->set('parentId',$content->get('id'));
		$new_content->save();
		
		//Below tests parent(), and  child()
		
		//test that the new content's id is set to it's parent's id
		$this->assertIdentical($new_content->get('parentId'), $content->get('id'));
		$this->assertIsA($content->children(), 'Stack');
		
		//$this->assertIdentical($new_content->parent()->get('id'), $content->get('id'));
		//$this->assertFalse(is_string($new_content->parent()->get('id')));
		
		//**Note** $new_content->'id' is either getting set, cashed, or returned as a string. While the parent 		  content is set to an integer
		$this->assertEqual($new_content->parent()->get('id'), $content->get('id'));
		$this->assertFalse(is_string($content->get('id')));
		
		//below tests comments()
		
		$content->addComment('first comment');
		$content->addComment('second comment');
		$content->addComment('third comment');
		
		$content_comments= $content->comments();
		
		$this->assertIsA($content_comments, 'Stack');
		
		$first = $content_comments->getNext();
		
		$this->assertEqual($first->comment, 'first comment');
		
		$first = $content_comments->getNext();
		$first = $content_comments->getNext();// this should be the 'third comment'
		
		$this->assertEqual($first->comment, 'third comment');
		$first = $content_comments->getNext();// this should return null
		
		$this->assertNull($first);
		
		$content_comments->reset();
		$first = $content_comments->getNext();
		$this->assertEqual($first->comment, 'first comment');
		
		//below tests files()

		$img_file = $content->files();	
		$this->assertNotNull($img_file);
	
		//below tests group()
		$group = $POD->getGroup();
		$group->set('groupname', 'group 1');
		$group->set('description', 'this is the group');
		$group->save();

		$this->assertTrue($group->success());
		
		$content->setGroup($group->get('id'));
		$this->assertTrue($content->success());
		$this->assertEqual($group->get('id'), $content->get('groupId'));
		$this->assertIsA($content->group(), 'Group');
		
		// below tests tag()
		$string = "foo bar baz";
		$content->tagsFromString($string);
		
		$tag_stack= $content->tags();
		
		$this->assertIsA($tag_stack, 'Stack');
		
		$tag= $tag_stack->getNext();
		
		$this->assertIsA($tag, 'Tag');
		
		$this->assertEqual($tag->value, 'foo');
		
		$group->delete();
		$new_content->delete();
		$content->delete();
		$user->delete();		
	}
	
}
?>










