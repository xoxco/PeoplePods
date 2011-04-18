<?php
require_once(dirname(__FILE__) . '/simpletest/autorun.php');
require_once('init_and_auth.php');
require_once('content.php');
require_once('person.php');
require_once('stack_query.php');
//require_once('file.php');
require_once('comment.php');
require_once('group.php');
require_once('tag.php');
require_once('message.php');

check_for_users_and_content();//clear any users/content left in database after a failed test of stack_query.php

$test = &new GroupTest('All tests');
$test->addTestCase(new TestOfInitAndAuth());
$test->addTestCase(new TestOfContent());
$test->addTestCase(new TestOfPerson());
$test->addTestCase(new TestOfStackQuery());
//$test->addTestCase(new TestofFile());
$test->addTestCase(new TestofComment());
$test->addTestCase(new TestofGroup());
$test->addTestCase(new TestofTag());
$test->addTestCase(new TestofMessage());

$test->run(new HtmlReporter());


////////////////////////////////////////////////////////////////

function check_for_users_and_content(){
		
		$POD = new PeoplePod();
		
		$extra_people = $POD->getPeople(array(
  				'testcase'=>'testuser'));
  		
  		$extra_content= $POD->getContents(array(
  				'testcase'=>'testcontent'));
  				
  		While($next= $extra_content->getNext()){
  			$next->delete(true);
  		}
  		
  		While($next_person= $extra_people->getNext()){
  			$POD->changeActor(array('id'=>$next_person->id));
			$next_person->delete();
  		}
	}
?>