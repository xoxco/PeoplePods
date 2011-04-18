<?php
/*

////////////////
fields

id
groupname
description
stub
type
userId
date
changeDate


///////////////
functions

getGroup() 

members() 
removeMember()
changeMemberType()
addMember()
isMember() 

addContent()
removeContent()
content()

save() 
delete()

*/
require_once(dirname(__FILE__) . '/simpletest/autorun.php');

include_once("../PeoplePods.php");


class TestOfGroup extends UnitTestCase {

	function testGroupFields(){
	
		
		$POD = new PeoplePod();

		$user = $POD->getPerson();
		$user->email = 'ben+test@example.com';//create user
		$user->password = 'foo';
		$user->set('nick','test');
		$user->save(); 
		
		
		$POD->changeActor(array('id'=>$user->id));//log in as user
		
		$group = $POD->getGroup();
		$group->set('groupname','Music Learning Club Austin');
		$group->set('description','A club where Austin people learn music.');
		$group->save();
		
		$this->assertNotNull($group->get('id'));
		$this->assertEqual($group->groupname, 'Music Learning Club Austin');
		$this->assertEqual($group->description, 'A club where Austin people learn music.');
		$this->assertNotNull($group->stub);
		$this->assertNull($group->type);
		$this->assertEqual($group->userId, $user->id);
		$this->assertNotNull($group->get('date'));
		$this->assertNotNull($group->changeDate);
		
		$group->delete();
		$user->delete();
	}
	
	function testGroupMemberFunctions(){
		
		$POD = new PeoplePod();

		$user = $POD->getPerson();
		$user->email = 'ben+test@example.com';//create user
		$user->password = 'foo';
		$user->set('nick','test');
		$user->save(); 	
		
		$POD->changeActor(array('id'=>$user->id));//log in as user
		
		$group = $POD->getGroup();
		$group->set('groupname','Music Learning Club Austin');
		$group->set('description','A club where Austin people learn music.');
		$group->save();
	
		$this->assertEqual($group->isMember($user), 'owner');
		
		$user2= $POD->getPerson();
		$user2->email = 'ben2+test@example.com';//create user
		$user2->password = 'foo';
		$user2->set('nick','test2');
		$user2->save(); 
		
		$POD->changeActor(array('id'=>$user2->id));
		
		$this->assertFalse($group->isMember($user2));
		$this->assertFalse($group->changeMemberType($user2, 'owner'));//$user2 is not a member, and should not be able to change membership status
		$group->addMember($user2);
		$this->assertTrue($group->success());
		
		$this->assertTrue($group->isMember($user2));
		$this->assertEqual($group->isMember($user2), 'member');
		$this->assertFalse($group->changeMemberType($user2, 'owner'));//should not be able to change to owner
		$this->assertFalse($group->changeMemberType($user2, 'manager'));//should not be able to change themselves to manager
		$this->assertFalse($group->removeMember($user));
		
		$POD->changeActor(array('id'=>$user->id));
		$group->changeMemberType($user2, 'manager');
		$this->assertTrue($group->success());
		$this->assertEqual($group->isMember($user2), 'manager');
		
		$members= $group->members();
		$this->assertIsA($members, 'Stack');
		$this->assertEqual($members->count(), 2);
		
		$this->assertFalse($group->removeMember($user));
		$group->removeMember($user2);
		
		$members= $group->members();
		$this->assertEqual($members->count(), 1);
		
		
		$group->delete();
		
		$POD->changeActor(array('id'=>$user->id));
		$user->delete();
		$POD->changeActor(array('id'=>$user2->id));
		$user2->delete();
	}
	
	function testGroupContent(){
	
	
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
		
		$group = $POD->getGroup();
		$group->set('groupname','Music Learning Club Austin');
		$group->set('description','A club where Austin people learn music.');
		$group->save();
		
		$group->addContent($content);
		$this->assertTrue($group->success());
		
		$content_stack= $group->content();
		$this->assertIsA($content_stack, 'Stack');
		$first_content= $content_stack->getNext();
		$this->assertIsA($first_content, 'Content');
		$this->assertEqual($first_content->headline, 'this is the headline');
		
		$group->removeContent($content);
		
		$content_stack= $group->content();
		$this->assertIsA($content_stack, 'Stack');
		$first_content= $content_stack->getNext();
		$this->assertNull($first_content);
		
		$content->delete();
		$group->delete();
		$user->delete();
	}
	
	function testGroupContentPrivacy(){
	
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
		
		$content2 = $POD->getContent();
		$content2->set('headline', 'for friends');
		$content2->set('type', 'Friends only');
		$content2->set('privacy', 'friends_only');
		$content2->save();
		
		$content3 = $POD->getContent();
		$content3->set('headline', 'Group members only!');
		$content3->set('type', 'private');
		$content3->save();
		
		$group = $POD->getGroup();
		$group->set('groupname','Music Learning Club Austin');
		$group->set('description','A club where Austin people learn music.');
		$group->save();
		
		$group->addContent($content);
		$group->addContent($content2);
		
		$group->addContent($content3);
		$content3->set('privacy', 'group_only');
		$content3->save();
		
		$get_contents= $group->content();
		$this->assertEqual($get_contents->count(), 3);//$user should have access to all three contents

		$user2 = $POD->getPerson();
		$user2->email = 'ben2+test@example.com';//create user
		$user2->password = 'foo';
		$user2->set('nick','test2');
		$user2->save(); 

		$POD->changeActor(array('id'=>$user2->id));
		$group->POD->changeActor(array('id'=>$user2->id));
		//
		$user2->addFriend($user);//at this point $user is in $user2's friendlist, but not in the group
		
		$get_contents= $group->content()->fill();
		$this->assertEqual($get_contents->count(), 1);
		
		while($check= $get_contents->getNext()){
			$this->assertNotEqual($check->get('headline'), 'Group members only!');
			$this->assertNotEqual($check->get('headline'), 'for friends');
		}
	
		$POD->changeActor(array('id'=>$user->id));
		$group->delete();
		$user->delete();
		$POD->changeActor(array('id'=>$user2->id));
		$user2->delete();
		
	}
}
?>


