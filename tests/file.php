<?php
/*
For the file Object tests, a picture with the following attributes was upload 
	via the command center - new content interface. The attributes of this content
	should be as follows
	
	filename:    darwin.jpg
	Headline:    Darwin
	type:        test Image
	status:      new
	stub:        darwin
	date:        2010-09-28 12:50:46
	Meta field:  science=>biology
	
//////////////////////////////////////
Functions Tested

->is_image();
->parent();
->owner();
->creator()*** currently failing
->getFiles()
->getFile()
////////////////////////////////////

fields tested

extension
userId
id
contentId
original_name
mime_type
minutes
date


///////////////////////////////////
Untested Functions

->download()
->src()	
->addFile()
->delete()
*/

require_once(dirname(__FILE__) . '/simpletest/autorun.php');

include_once("../PeoplePods.php");


class TestOfFile extends UnitTestCase {

	function test_File_fields() {	
			
		$POD = new PeoplePod();
		
		$user= $POD->getPerson(array('nick'=>'admin'));
		$POD->changeActor(array('id'=>$user->id));
		
		$most_recent= $POD->getFiles(array(
  		'userId'=>$user->get('id')));
  		
  		$file= $most_recent->getNext();
  		$this->assertIsA($file, 'File');
  		
  		$this->assertTrue($file->isImage());
  		
  		$creator= $file->creator();
  		$this->assertIsA($creator, 'Person');
  		
  		$owner= $file->owner();
  		$this->assertIsA($owner, 'Person');
  		
  		$this->assertTrue($file->get('userId'), $user->id);
  		
  		$this->assertEqual($file->get('extension'), 'jpg');
  		
  		$this->assertNotNull($file->get('id'));
  		$this->assertNotNull($file->get('contentId'));
  		$this->assertEqual($file->get('original_name'), 'darwin.jpg');
  		
  		$this->assertNull($file->get('description'));//should this be initialized to null?
  		
  		$this->assertEqual($file->get('mime_type'), 'image/jpeg');
  		
  		$this->assertNotNull($file->get('date'));
  		$this->assertNotNull($file->get('minutes'));
  		
  		$parent_content= $file->parent();
  		$this->assertIsA($parent_content, 'Content');
  		$this->assertEqual($parent_content->get('headline'), 'Darwin');
  		
  		
  		//now attempt to get a file by means of getFile()
  		$new_file= $POD->getFile(array('id'=>$file->get('id')));
  		$this->assertIsA($new_file, 'File');
  		
	}
}



?>






