<?php
/*
///////////
fields

tag
value
date

///////////
functions

$Content->hasTag()
$Content->addTag()
$Content->removeTag()**
$Content->tagsFromString()**
$Content->tagsAsString()**
$Content->contentCount()** 

*/

require_once(dirname(__FILE__) . '/simpletest/autorun.php');

include_once("../PeoplePods.php");


class TestOfTag extends UnitTestCase {

	function testTagFields(){
	
		$POD = new PeoplePod();

		$user = $POD->getPerson();
		$user->email = 'ben+test@e';//create user
		$user->password = 'foo';
		$user->set('nick','test');
		$user->save(); 	
		
		$POD->changeActor(array('id'=>$user->id));//log in as user
		
		$content = $POD->getContent();
		$content->set('headline', 'this is the headline');
		$content->set('type', 'this is the type');
		$content->save();
		
		$this->assertFalse($content->hasTag('foo'));
		
		$content->addTag('foo');
		$content->save();
		
		$this->assertTrue($content->hasTag('foo'));
		
		$tags= $content->tags();
		$tag= $tags->getNext();
		$this->assertIsA($tag, 'Tag');
		
		/// test of fields //////////////////////
		$this->assertNotNull($tag->id);
		$this->assertEqual($tag->value, 'foo');
		$this->assertNotNull($tag->get('date'));
		////////////////////////////////////////
		
		$content->removeTag('nonexistant');
		$this->assertFalse($content->success());// should not be able to remove a tag that does not exist
		$tag_count= $content->tags()->count();
		
		$this->assertEqual($tag_count, 1);
		
		$content->removeTag('foo');
		$tag_count= $content->tags()->count();
		$this->assertEqual($tag_count, 0);
		
		$string= "foo bar baz";
		$content->tagsFromString($string);
		$tag_count= $content->tags()->count();
		$this->assertEqual($tag_count, 3);
		
		$string= $content->tagsAsString();
		$this->assertEqual($string, "foo bar baz"); 
		$content->addTag("");
		$this->assertEqual($tag_count, 3);
		
		$string= $content->tagsAsString();
		$this->assertEqual($string, "foo bar baz "); 
		
		$string= "";                       //test adding a null value tag
		$content->tagsFromString($string);
		$tag_count= $content->tags()->count();
		$this->assertEqual($tag_count, 0);
		
		$content->delete();
		$user->delete();
	}

}
?>