<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* core_usercontent/edit.php
* Handles the add/edit form for this type of content
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/new-content-type
/**********************************************/


	include_once("content_type.php"); // this defines some variables for use within this pod
	include_once("../../PeoplePods.php");
	$POD = new PeoplePod(array('debug'=>0,'lockdown'=>'verified','authSecret'=>@$_COOKIE['pp_auth']));
	if (!$POD->libOptions("enable_contenttype_{$content_type}_add")) { 
		header("Location: " . $POD->siteRoot(false));
		exit;
	}
	
	
	// by default, this script will redirect to the homepage of the site.
	// this can be changed by passing in an alternative via the redirect parameter
	$redirect = $POD->siteRoot(false);


	if ($_POST) { 

		if (isset($_POST['id'])) { 
			$content = $POD->getContent(array('id'=>$_POST['id']));
			if (!$content->success()) { 
				$POD->addMessage($content->error());
			}
			if (!$content->isEditable()) { 
				$POD->addMessage("Access Denied");
			}		
		} else {
			$content = $POD->getContent();
		}
		
		if (isset($_POST['headline'])) { 
			$content->set('headline',$_POST['headline']);
		}
		
		if (isset($_POST['body'])) { 
			$content->set('body',$_POST['body']);	
		} 
		
		if (isset($_POST['link'])) {
			$content->set('link',$_POST['link']);	
		}
		if (isset($_POST['type'])) {
			$content->set('type',$_POST['type']);	
		}		
		if (isset($_POST['parentId'])) { 
			$content->set('parentId',$_POST['parentId']);
		}
		if (isset($_POST['groupId'])) { 
			$content->set('groupId',$_POST['groupId']);
		}
		
		if (isset($_POST['friends_only'])) { 
			$content->set('privacy','friends_only');
		}
		if (isset($_POST['group_only'])) { 
			$content->set('privacy','group_only');
		}		
		// save these fields to the database.  This will update existing records or create a new one if necessary
		$content->save(true);
		if ($content->success()) { 
			$POD->addMessage("Content saved!");
			
			// now that we have real object, we can add stuff to it.


			// add tags from a space delimited string.  You can pass in an alternative delimter like so:
			// $content->tagsFromSTring($_POST['tags'],',');
			if (@$_POST['tags']!='') { 
				$content->tagsFromString($_POST['tags']);
			}


			// now we'll add any meta fields that have been passed in.
			// we do this by looking for anything with a field name starting with meta_
			// so if you want to add a meta field called foo to your content
			// you'll pass in the value via meta_foo
			foreach ($_POST as $key=>$value) { 
				if (preg_match("/^meta_(.*)/",$key,$match)) { 
				
					$key = $match[1];
					// add the field.
					// the third parameter is no_html, set it to true to strip html, or false to allow html
					$content->addMeta($key,$value,true);
				
				}
			}

			foreach ($_FILES as $filename=>$file) { 
			
				$content->addFile($filename,$file);
				if (!$content->success()) { 
					$POD->addMessage('An error occured while attaching your file: ' . $content->error());
				}

			}
			$content->files()->fill();

		} else {
			$POD->addMessage("Error! " . $content->error());
		}
		
		$msg = implode("\n",$POD->messages());
		
		if (isset($_POST['redirect'])) {
			if ($_POST['redirect'] == "permalink") {  	
				$redirect = $content->get('permalink');
			} else {
				$redirect = $_POST['redirect'];
			}
		}	
		header("Location: $redirect?msg=" . urlencode($msg));
	} else if ($_GET['id']) { 
	
		$content = $POD->getContent(array('id'=>$_GET['id']));
		if (!$content->isEditable()) { 
			header("Location: $redirect?msg=" . urlencode("Access Denied"));
		} else {
		
			$POD->header("Edit " . $content->get('headline'));
			$content->output($input_template);
			$POD->footer();
		}
	} else {
	
			$POD->header("Add Something");
			$POD->getContent(array('type'=>$content_type))->output($input_template);
			$POD->footer();
	}
	
?>