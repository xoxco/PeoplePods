<?php


require_once(dirname(__FILE__) . '/simpletest/autorun.php');

include_once("../PeoplePods.php");


class TestOfStackQuery extends UnitTestCase {


		function Test_Stack_by_person(){
		
		$POD = $this->CreateBaseContent();
		
		
		//test pulling a non-existent parameter, and get a count of zero
		$no_people= $POD->getPeople(array(
				'testcase'=>'none'));
		$this->assertIsA($no_people, 'Stack');	
  		$this->assertEqual($no_people->count(), 0);//no people should be returned
  		$this->assertEqual($no_people->totalCount(), 0); 
		
		//pull all test people in descending order
		$all_people= $POD->getPeople(array(
  				'testcase'=>'testuser'), 'u.id DESC');
  				
  		$this->assertIsA($all_people, 'Stack');	
  		$this->assertEqual($all_people->count(), 3);//two people should be returned
  		$this->assertEqual($all_people->totalCount(), 3);
  		
  		$person= $all_people->getNext();
  		$this->assertEqual($person->nick, 'test3');
  		
  		$all_people->sortBy('id', true);//resort ascending
  		
  		$person= $all_people->getNext();
  		$this->assertEqual($person->nick, 'test');
  		
  		
  		//pull all that match two parameters of the same type, but different values
  		$names = array('test', 'test2');
  		
  		$some_people= $POD->getPeople(array(
  				'nick'=>$names));
		$this->assertEqual($some_people->count(), 2);//three people should be returned
  		$this->assertEqual($some_people->totalCount(), 2);
  		
  /*
		$names= array('test2', 'Damien','admin');
  		$some_people= $POD->getPeople(array(
  				'nick:!='=>$names));
  				
  		$this->assertEqual($some_people->count(), 2);
  	
  		
  		while($person= $some_people->getNext()){
  			
  			$this->assertNotEqual($person->get('nick'), 'test2');
  		}
 */
 		
		$this->Delete_all_content($POD);
	}
	
	
	function Test_Stack_by_Content(){
	
		$POD = $this->CreateBaseContent();
	
		//test pulling a non-existent parameter, and get a count of zero
		$no_content= $POD->getPeople(array(
				'testcase'=>'none'));
		$this->assertIsA($no_content, 'Stack');	
  		$this->assertEqual($no_content->count(), 0);
  		$this->assertEqual($no_content->totalCount(), 0); 
		
		
		//pull all test content in descending order
		$all_content= $POD->getContents(array(
  				'testcase'=>'testcontent'));
  			
  		$this->assertIsA($all_content, 'Stack');	
  		$this->assertEqual($all_content->count(), 3);
  	    $this->assertEqual($all_content->totalCount(), 3);
  		
  		$content= $all_content->getNext();
  		//$this->assertEqual($content->headline, 'first headline');
  	
	
		$this->Delete_all_content($POD);
	}
/////////////////////////////////////////////////////////////////

	
	//This function is used to create testing content and is not intended to be tested itself
	function CreateBaseContent(){
	
		$POD= new PeoplePod();
		
		$user = $POD->getPerson();
		$user->email = 'ben+test@example.com';//create user
		$user->password = 'foo';
		$user->nick = 'test';
		$user->addMeta('testcase','testuser'); // add a new field
		$user->save();
		
		$user2 = $POD->getPerson();
		$user2->email = 'ben2+test@example.com';//create user
		$user2->password = 'foo';
		$user2->nick = 'test2';
		$user2->addMeta('testcase','testuser'); // add a new field
		$user2->save();
		
		$user3 = $POD->getPerson();
		$user3->email = 'ben3+test@example.com';//create user
		$user3->password = 'foo';
		$user3->nick = 'test3';
		$user3->addMeta('testcase','testuser'); // add a new field
		$user3->save();
		
		$POD->changeActor(array('id'=>$user->id));//log in as test
		
		$content = $POD->getContent();
		$content->set('headline', 'first headline');
		$content->set('type', 'same type');
		$content->addMeta('testcase','testcontent'); // add a new field
		$content->save();
		
		$content2 = $POD->getContent();
		$content2->set('headline', 'second headline');
		$content2->set('type', 'same type');
		$content2->addMeta('testcase','testcontent'); // add a new field
		$content2->save();
		
		$content3 = $POD->getContent();
		$content3->set('headline', 'second headline');
		$content3->set('type', 'different type');
		$content3->addMeta('testcase','testcontent'); // add a new field
		$content3->save();
		
		return $POD;
	   }
		
		
	//This function is used to delete testing content and is not intended to be tested itself	
	function Delete_all_content($POD){
	
		$people = $POD->getPeople(array(
  				'testcase'=>'testuser'));
	
	
		$content= $POD->getContents(array(
				'testcase'=>'testcontent'));
		
	
		while($test_content= $content->getNext()){
	
			$test_content->delete(true);
		}
	
		while($user= $people->getNext()){
	
			$POD->changeActor(array('id'=>$user->id));
			$user->delete();
		}
	
	}
	
	



}
?>