<?php
/* 
//////////////////
Tested Functions
//////////////////

inherited functions

$Person->get()
$Person->set()
$Person->addMeta()
$Person->getMeta()
$Person->removeMeta()
$Person->addFlag()
$Person->hasFlag()
$Person->removeFlag()
$Person->toggleFlag()
$Person->success()
$Person->error()

Relational Functions

$Person->favorites()
$Person->friends() 
$Person->followers()
$Person->watched()
$Person->files()

Person Object Functions

$Person->addFavorite()
$Person->removeFavorite()
$Person->addFriend()
$Person->removeFriend()
$Person->addWatch()
$Person->removeWatch()
$Person->recommendFriends()
$Person->isFriendsWith()

$Person->  sendMessage()
$Person->  getInbox($count,$offset)
$inbox->   unreadCount()
$message-> recipient()
$message-> from();
$message-> to();
$thread->clear();
$thread->reply($message);
$thread->markAsRead();

$Person->verify()
$Person->isVerified()

$Person->isFavorite()
$Person->toggleFavorite()
$Person->toggleWatch()
$Person->isWatched()

////////////////////////
Untested Functions
////////////////////////


Untested inherited functions

$Person->write()
$Person->writeFormatted()
$Person->htmlspecialwrite()
$Person->errorCode()
$Person->asArray()


Untested Person Object Functions

$Person->output()

$Person->sendEmail()
$Person->sendInvite()
$Person->welcomeEmail()

$Person->sendPasswordReset()

*/
require_once(dirname(__FILE__) . '/simpletest/autorun.php');


include_once("../PeoplePods.php");


//test inherited functions

class TestOfPerson extends UnitTestCase {

		function testPersonInheritedFunctions() {	
			
		
		$POD = new PeoplePod();

		$user = $POD->getPerson();
		$user->email = 'ben+test@example.com';//create user
		$user->password = 'foo';
		$user->set('nick','test');
		$user->save();
		
		$this->assertTrue($user->success());
		$this->assertFalse($user->error());
		
		$POD->changeActor(array('id'=>$user->id)); 
		
		// tests get/set
		$testget = $user->get('nick');
		$this->assertIdentical($testget, 'test');
		
		//below tests inherited meta functions
		
		$user->addMeta('about me', 'I live in Austin');
		$new_meta= $POD->getContents(array('type'=>'about me'));
		$this->assertIsA($new_meta, 'stack');
		
		$meta = $user->getMeta();
		$test_this = $meta['about me'];
		$this->assertEqual($test_this, 'I live in Austin');
		
		$user->removeMeta('about me');
		$test_meta = $user->get('about me');
		$this->assertNull($test_meta);
		
		//below tests inherited flag functions
		$user->addFlag('block',$POD->currentUser());
		$this->assertTrue($POD->currentUser()->hasFlag('block', $user));
		
		$user->removeFlag('block', $user);
		$this->assertFalse($POD->currentUser()->hasFlag('block', $user));

		$flag= FALSE;
		$this->assertFalse($user->hasFlag($flag, $user));
		$user->toggleFlag($flag, $user);
		$this->assertTrue($user->hasFlag($flag, $user));
		
		$user->delete();
	}
	
	function test_relationals(){
	
		$POD = new PeoplePod();

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
		
		$content2 = $POD->getContent();
		$content2->set('headline', 'this is the headline');
		$content2->set('type', 'this is the type');
		$content2->save();
		
		// below tests Favorites(), addFavorite(), removeFavorite()
		$user->addFavorite($content);
		$user->addFavorite($content2);
		$this->assertTrue($user->success());
		
		$favs_list= $user->favorites();
		$this->assertIsA($favs_list, 'Stack');
		
		$fav = $favs_list->getNext();
		$this->assertIsA($fav, 'Content');
		
		$favs_list->reset();
		$this->assertNotNull($favs_list->getNext());
		
		$user->removeFavorite($content);
		$user->removeFavorite($content2);
		
		$this->assertNull($favs_list->getNext());
		
		// below tests friends(), addFriend(), followers(), removeFriend()
		
		$user2 = $POD->getPerson();
		$user2->email = 'ben2+test@example.com';//create user
		$user2->password = 'foo';
		$user2->nick = 'test2';
		$user2->save(); 
		
		$POD->changeActor(array('id'=>$user2->id));
		
		$user2->addFriend($user,$send_email = false);
		$this->assertTrue($user2->success());
		
		$friend_list= $user2->friends();
		$this->assertIsA($friend_list, 'Stack');
		$friend = $friend_list->getNext();
		$this->assertIsA($friend, 'Person');
		
		$POD->changeActor(array('id'=>$user->id));
		
		$user->addFriend($user2,$send_email = false);
		
		$boolean = $user2->isFriendsWith($user);
		$this->assertTrue($boolean);
		
		$followedBy= $user->followers();
		
		$this->assertTrue($user->success());
		$this->assertIsA($followedBy, 'Stack');
		
		$follower= $followedBy->getNext();
		$this->assertIsA($follower, 'Person');
		
		$user_friends= $user->friends();
		$this->assertNotNull($user_friends->getNext());
		
		
		//test watched(), addwatch()
		$user->addWatch($content);
		
		$watched= $user->watched();
		$this->assertTrue($user->success());
		
		//**note come back to this and test comments returned **
		$this->assertIsA($watched->getNext(), 'Content');
				
		$user->removeWatch($content);
		$this->assertTrue($user->success());
		
		
		// test files()
		//should at least return default avatar icon
		$useFiles= $user->files();
		$this->assertNotNull($useFiles);
		
		// test removeFriend()		
		$POD->changeActor(array('id'=>$user2->id));
		$user2->removeFriend($user);
		$friend_list= $user2->friends();
		$this->assertNull($friend_list->getNext());
		
		
		$content->delete();
		$user2->delete();
		
		$POD->changeActor(array('id'=>$user->id));
		$user->delete();
				
	}
	
	function Test_recommendFriends(){
	
		$POD = new PeoplePod();

		$user = $POD->getPerson();
		$user->email = 'ben+test@example.com';//create user
		$user->password = 'foo';
		$user->nick = 'test';
		$user->save(); 
		
		$POD->changeActor(array('id'=>$user->id));//log in as user
		
		$user2 = $POD->getPerson();
		$user2->email = 'ben2+test@example.com';//create user
		$user2->password = 'foo';
		$user2->nick = 'test2';
		$user2->save(); 
		
		$user3 = $POD->getPerson();
		$user3->email = 'ben3+test@example.com';//create user
		$user3->password = 'foo';
		$user3->nick = 'test3';
		$user3->save(); 
		
		/*
		The following code will friend user with user2, and then friend user2 with user3.
		user3 should show up in user's recommendFriends stack with an overlap of 1
		*/
		
		$user->addFriend($user2, $send_email= false);//user friends user2
		$this->assertTrue($user->success());
		
		$POD->changeActor(array('id'=>$user2->id));// change to user2
		
		$user2->addFriend($user3, $send_email= false);//friend user2 to user3
		$this->assertTrue($user->success());
		
		
		$POD->changeActor(array('id'=>$user->id));// log into user
		
		$recommended= $user->recommendFriends($minimum_overlap=1,$max_Results=2);
		
		$this->assertEqual($recommended->count(), 1);
		$this->assertIsA($recommended, 'Stack');
		
		$get_rec= $recommended->getNext();
		$this->assertNotNull($get_rec);
		$this->assertIsA($get_rec, 'Person');
		$this->assertEqual($get_rec->get('nick'), 'test3');
		
		
		$POD->changeActor(array('id'=>$user->id));
		$user->delete();
		
		$POD->changeActor(array('id'=>$user2->id));
		$user2->delete();
		
		$POD->changeActor(array('id'=>$user3->id));
		$user3->delete();
	}

	function Test_messages(){
	
		$POD = new PeoplePod();

		$user = $POD->getPerson();
		$user->email = 'ben+test@example.com';//create user
		$user->password = 'foo';
		$user->nick = 'test';
		$user->save(); 
		
		$POD->changeActor(array('id'=>$user->id));//log in as user
		
		$user2 = $POD->getPerson();
		$user2->email = 'ben2+test@example.com';//create user
		$user2->password = 'foo';
		$user2->nick = 'test2';
		$user2->save(); 

		$message1= "first message";
		$message2= "second message";
		
		$user2->sendMessage($message1);
		
		sleep(2);
		$user2->sendMessage($message2);
		
		$inbox = $POD->getInbox(2,0);
		$this->assertIsA($inbox, 'Stack');
		
		$total_unread = $inbox->unreadCount();
		$this->assertEqual($total_unread, 0); //user's sent messages should be automatically flagged as read
		
		$inbox->fill();
			 
		
		$thread = $inbox->getNext();
		$this->assertIsA($thread, 'Thread');
		
		$sent_to= $thread->recipient();
		$this->assertIsA($sent_to, 'Person');
		$this->assertEqual($sent_to->get('nick'), 'test2');
		
		$messages= $thread->messages();//get list of message objects
		$this->assertIsA($messages, 'Stack');
		
		$message= $messages->getNext();//get individual message object from stack
		$this->assertIsA($message, 'Message');
		
		$from = $message->from();
		$this->assertEqual($from->get('nick'), 'test');
		
		$to = $message->to();
		$this->assertEqual($to->get('nick'), 'test2');
		
		
		$POD->changeActor(array('id'=>$user2->id));
		
		
		$inbox2= $POD->getInbox(2,0);
		$total_unread2= $inbox2->unreadCount();
		$this->assertEqual($total_unread2, 2);//user2's inbox should have 2 unread messages
		
		$thread = $inbox2->getNext(); //get one thread
		$this->assertIsA($thread, 'Thread');
		
		$messages= $thread->messages();
		$message= $messages->getNext();
		
		$this->assertEqual($message->get('message'), 'second message');
		
		$thread->markAsRead();
		$unread = $thread->unreadCount();
		$this->assertEqual($unread, 0);
		
		$reply= "this is a reply";
		$thread->reply($reply);
		
		$thread->clear();
		
		$inbox2= $POD->getInbox(2,0);
		$total_unread2= $inbox2->unreadCount();
		$this->assertEqual($total_unread2, 0);	
		
		$POD->changeActor(array('id'=>$user->id));
		
		$inbox= $POD->getInbox(3, 0);
		$total_unread= $inbox->unreadCount();
		$this->assertEqual($total_unread, 1);
		
		$thread= $inbox->getNext();
		$messages= $thread->messages();
		$message= $messages->getNext();
		$message= $messages->getNext();
		$message= $messages->getNext();
		$this->assertEqual($message->get('message'), 'first message');
		

  		$POD->changeActor(array('id'=>$user->id));	
   		$user->delete();
		
		$POD->changeActor(array('id'=>$user2->id));		
		$user2->delete();
	
	}

	function Test_password_functions(){
	
		$POD = new PeoplePod();

		$user = $POD->getPerson();
		$user->email = 'ben+test@example.com';//create user
		$user->password = 'foo';
		$user->nick = 'test';
		$user->save(); 
		
		$POD->changeActor(array('id'=>$user->id));//log in as user	

		$this->assertFalse($user->isVerified());
	
		$user->verify($user->verificationKey);
		
		$this->assertTrue($user->success());
		
		$this->assertTrue($user->isVerified());
	
		
		$user->delete();	
	}
	
	function Test_favorite_watched(){
	
		
		$POD = new PeoplePod();

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
		
		$isFavorite = $user->isFavorite($content);
		$this->assertFalse($isFavorite);
		
		$user->toggleFavorite($content);
		$isFavorite= $user->isFavorite($content);
		$this->assertTrue($isFavorite);
		
		$isWatched= $user->isWatched($content);
		$this->assertTrue($isWatched);
		
		$user->toggleWatch($content);
		$isWatched= $user->isWatched($content);
		$this->assertFalse($isWatched);
		
		
		$content->delete();
		$user->delete();
		
	}
	
}
?>