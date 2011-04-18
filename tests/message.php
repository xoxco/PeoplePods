<?php
/*
///////////
fields

id
userId
friendId
toId
fromId
message
date
status

///////////
functions

	sendMessage()

Inbox
	getInbox() 
	unreadCount()
	newThread($friendID)
	
Thread($POD)
	messages()
	recipient()
	unreadCount()
	markasRead()
	reply($message)
	
Message
	from()
	to()
*/

require_once(dirname(__FILE__) . '/simpletest/autorun.php');

include_once("../PeoplePods.php");


class TestOfMessage extends UnitTestCase {

	function testTagFields(){
	
		$POD = new PeoplePod();

		$user = $POD->getPerson();
		$user->email = 'ben+test@example.com';//create user
		$user->password = 'foo';
		$user->set('nick','test');
		$user->save(); 	
		
		$POD->changeActor(array('id'=>$user->id));//log in as user
		
		$user2 = $POD->getPerson();
		$user2->email = 'ben2+test@example.com';//create user
		$user2->password = 'foo';
		$user2->set('nick','test2');
		$user2->save(); 	
		
		$user2->sendMessage('hi user2');
		sleep(2);
		$user2->sendMessage('hi again user2');
		
		$POD->changeActor(array('id'=>$user2->id));
		
		
		$inbox= $POD->getInbox();
		$this->assertTrue($inbox);
		
	    $thread= $inbox->newThread($user->id);
		$this->assertNotNull($thread->MESSAGES);
		
		$messages= $thread->messages();
		$this->assertEqual($thread->unreadCount(), 2);
		$this->assertEqual($messages->count(), 2);
		
		$first_message= $messages->getNext();
		$this->assertEqual($first_message->to()->get('id'), $user2->id);
		$this->assertEqual($first_message->from()->get('id'), $user->id);
		
		$thread->markAsRead();
		$this->assertEqual($thread->unreadCount(), 0);
		
		$this->assertEqual($thread->recipient()->get('id'), $user->id);
		
		$second_message= $messages->getNext();
		$thread->reply('hello to you');
		$this->assertTrue($thread->success());
		
		$POD->changeActor(array('id'=>$user->id));
		
		$inbox= $POD->getInbox();
		$thread= $inbox->newThread($user2->id);
		$this->assertEqual($thread->unreadCount(), 1);
		
		$messages= $thread->messages();
		$this->assertEqual($messages->count(), 3);

		$messages->reset();
		
		$message= $messages->getNext();
		$this->assertEqual($message->message, 'hi user2');
		
		///Test fields of message object//////
		$this->assertNotNull($message->id);
		$this->assertEqual($message->userId, $user->id);
		$this->assertEqual($message->fromId, $user->id);
		$this->assertEqual($message->targetUserId, $user2->id);
		$this->assertNotNull($message->date);
		$this->assertEqual($message->status, 'read');
		
		///////////////////////////////////
		
		$POD->changeActor(array('id'=>$user->id));
		$user->delete();
		$POD->changeActor(array('id'=>$user2->id));
		$user2->delete();
	}	
		

}
?>