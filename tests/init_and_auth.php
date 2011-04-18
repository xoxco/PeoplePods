<?php
require_once(dirname(__FILE__) . '/simpletest/autorun.php');


include_once("../PeoplePods.php");

class TestOfInitAndAuth extends UnitTestCase {

	function testWorks() {
		
		$this->assertTrue(true);
		$this->assertFalse(false);
	}

    function testPeoplePodsInit() {
    
    	$this->assertNotNull($POD = new PeoplePod());
    
    }
    
    function testPeoplePodsNoAuth() { 
    	$POD = new PeoplePod(array('authSecret'=>'baldjkdfljh'));
    	$this->assertFalse($POD->isAuthenticated());
    }
    
    function testPeoplePodsCreateUser() { 
    
    	$POD = new PeoplePod();
    	$user = $POD->getPerson();
    	$this->assertIsA($user,'Person');
		$this->assertNull($user->id);

		$user->email = 'ben+test@example.com';
		$user->password = 'foo';
		$user->nick = 'test';
		
		$user->save();
		
		$this->assertTrue($user->success());
		$this->assertNotNull($user->id);
		
		$POD->changeActor(array('id'=>$user->id));
		
		$this->assertTrue($POD->isAuthenticated());
				
		$user->delete();
	    
    }


}
?>