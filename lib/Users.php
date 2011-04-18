<?
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* lib/Users.php
* Creates the Person object
*
* Documentation for this object can be found here:
* http://peoplepods.net/readme/person-object
/**********************************************/

require_once("Obj.php");
require_once("Stack.php");

class Person extends Obj {
	var $wrong_password;
	protected $FRIENDS;
	protected $FOLLOWERS;	
	protected $FAVORITES;
	protected $WATCHED;
	protected $FILES;
	protected $COMMENTS;


	// base database configuration for this object.
	static private $DEFAULT_FIELDS = array('id','nick','fullname','stub','email','password','memberSince','date','lastVisit','verificationKey','authSecret','passwordResetCode','invitedBy','zipcode');
	static private $IGNORE_FIELDS = array('permalink','minutes','invite_code');	
	static private $DEFAULT_JOINS = array ( 
						'd'=>'inner join content d on d.userId=u.id', // link to this user's content
						'g'=>'inner join groupMember mem on mem.userId=u.id inner join groups g on g.id=mem.groupId', // link to user's groups
						'mem'=>'inner join groupMember mem on mem.userId=u.id', // link to just a user's membership records
						'c'=>'inner join comments c on c.userId=u.id', // link to a user's comments
						'f'=>'inner join files f on f.userId=u.id', // link to a user's files
						't' => 'inner join tagRef tr on tr.itemId=u.id and tr.type="user" inner join tags t on tr.tagId=t.id', // link to tags
					);
					
	static private $FIELD_PROCESSORS = array();			
	static private $EXTRA_METHODS = array();

	function Person($POD,$PARAMETERS=null) {
		parent::Obj($POD,'user',array(
			'table_name' => "users",
			'table_shortname' => "u",
			'fields' => self::$DEFAULT_FIELDS,
			'ignore_fields'=>self::$IGNORE_FIELDS,				
			'joins' => self::$DEFAULT_JOINS,
			'field_processors'=>self::$FIELD_PROCESSORS		
		));	
		if (!$this->success()) {
			return $this;
		}
		
		$this->success = false;
		if (isset($PARAMETERS['authSecret']) && (sizeof($PARAMETERS)==1)) {
			$this->POD->tolog("user->new(): Attempting to verify user...");
			$this->getUserByAuthSecret($PARAMETERS['authSecret']);
		} else if (isset($PARAMETERS['passwordResetCode']) && (sizeof($PARAMETERS)==1)) {
			$this->POD->tolog("user->new(): Load by reset code...");
			$this->getUserByPasswordResetCode($PARAMETERS['passwordResetCode']);
		} else if (isset($PARAMETERS['id']) && (sizeof($PARAMETERS)==1)) {
			$this->POD->tolog("user->new(): Load user by id " . $PARAMETERS['id']);
			$this->getUserById($PARAMETERS['id']);
		} else if (isset($PARAMETERS['stub']) && (sizeof($PARAMETERS)==1)) {
			$this->POD->tolog("user->new(): Load user by stub " . $PARAMETERS['stub']);
			$this->getUserByStub($PARAMETERS['stub']);
	 	} else if (isset($PARAMETERS['email']) && (sizeof($PARAMETERS)==1)) {
			$this->POD->tolog("user->new(): Load user by email");
	 		$this->getUserByEmail($PARAMETERS['email']);
	 	} else if (isset($PARAMETERS['nick']) && (sizeof($PARAMETERS)==1)) {
			$this->POD->tolog("user->new(): Load user by nick");
	 		$this->getUserByNick($PARAMETERS['nick']); 
		} else if (isset($PARAMETERS['nick']) && isset($PARAMETERS['email']) && ($PARAMETERS['password'] || $PARAMETERS['id'])) {
			$this->POD->tolog("user->new(): Creating user from parameters");
			$fill = true;
			if (isset($PARAMETERS['id'])) {
				$d = $this->POD->checkcache('Person','id',$PARAMETERS['id']);
				if ($d) {
					$fill = false;
					$this->DATA = $d;
					$this->success = true;
				} 
			}
			
			if ($fill) {
				foreach ($PARAMETERS as $key => $value) {
					if ($key != 'POD') {
						$this->set($key,$value);
					}
				}
				$this->success = true;
				$this->stuffUser();
				$this->loadMeta();
				$this->POD->cachestore($this);
			}
		} else {
			$this->success = true;
			 $this->POD->tolog("user->new(): Empty User");
		}


		// if we failed to create the user by this point, we're screwed.
		if (!$this->success()) { 
			return;
		}

		return $this;
		
	}



	
/*********************************************************************************************
* Accessors
*********************************************************************************************/
		function addFile($file_name,$uploaded_file,$description=null) { 
		// pass in an array of parameters from the $_FILES array and this will automatically create the file record.
	
			$this->success = false;
			
			// if the file already exists, update it.
			if (!$file = $this->files()->contains('file_name',$file_name)) { 
				// create a new file
				$file = $this->POD->getFile();
			}
		
			if ($uploaded_file['name']!='') {
					
					$file->set('file_name',$file_name);
					$file->set('original_name',$uploaded_file['name']);
					$file->set('tmp_name',$uploaded_file['tmp_name']);
					$file->set('description',$description);
					$file->set('userId',$this->id);
					$file->save();		
					if (!$file->success()) {
						$this->throwError($file->error());
					} else {
						$this->success = true;
						return $file;
					}
			} else if ($uploaded_file['error']!= 0 && $uploaded_file['error']!= UPLOAD_ERR_NO_FILE) {
			
				if ($uploaded_file['error'] == UPLOAD_ERR_INI_SIZE) {
					$this->throwError('The file ' . $file_name . ' exceeds the maximum allowed upload size on this server.');
				}
				if ($uploaded_file['error'] == UPLOAD_ERR_FORM_SIZE) {
					$this->throwError('The file ' . $file_name . ' exceeds the maximum allowed upload size for this form.');
				}
				if ($uploaded_file['error'] == UPLOAD_ERR_PARTIAL) {
					$this->throwError('The file ' . $file_name . ' did not successfully upload.');
				}
				if ($uploaded_file['error'] == UPLOAD_ERR_NO_TMP_DIR) {
					$this->throwError('PeoplePods cannot find a temporary folder to store the uploaded files.');
				}				
				if ($uploaded_file['error'] == UPLOAD_ERR_CANT_WRITE) {
					$this->throwError('PeoplePods cannot write to the temporary folder.');
				}				
				if ($uploaded_file['error'] == UPLOAD_ERR_EXTENSION) {
					$this->throwError('A PHP extension stopped the file upload.');
				}				

				return false;
			
			} else {
				// sometimes an invalid record gets into $_FILES where no tmp_name is specified
				// this normally happens when a javascript form validator has caused the file input to submit
				// even though there is no file!
				// we don't want to throw an error if this happens, we just want to silently ignore this record.
				//$file->set('description',$description);
				//$file->save();


				$this->success = true;
				return null;
			}

			return $this->success;		
		
		}
		
		function files($count=100,$offset=0) {		
			if (!$this->get('id')) {
				return new Stack($this->POD,'file');
			}
			if (!$this->FILES) { 
				$this->FILES = new Stack($this->POD,'file',array('groupId'=>'null','contentId'=>'null','userId'=>$this->get('id')),null,$count,$offset,null,null,null,$this->id.'-person-files');
				if (!$this->FILES->success()) { 
				return new Stack($this->POD,'file');
				}
			}
			return $this->FILES;
		}
		
		
		function friends($count = 20,$offset=0) { 
			if (!$this->get('id')) {
				return null;
			}
			if (!$this->FRIENDS || $count != 20 || $offset != 0) {
				$this->FRIENDS =   new Stack($this->POD,'user',array('flag.name'=>'friends','flag.userId'=>$this->get('id')),"flag.date DESC",$count,$offset,null,null,null,$this->id.'-person-friends');
				if (!$this->FRIENDS->success()) { 
					return null;
				}
			}
			return $this->FRIENDS;	

		}

		function followers($count=20,$offset=0) { 
			if (!$this->get('id')) {
				return null;
			}
			if (!$this->FOLLOWERS || $count != 20 || $offset != 0) {
				$this->FOLLOWERS = new Stack($this->POD,'user',array('flag.name'=>'friends','flag.itemId'=>$this->get('id')),"flag.date DESC",$count,$offset,null,null,null,$this->id.'-person-followers');
				if (!$this->FOLLOWERS->success()) { 
					return null;
				}
			}
			return $this->FOLLOWERS;	
		}

		function favorites($count=20,$offset=0) {
			if (!$this->get('id')) {
				return null;
			}		
			if (!$this->FAVORITES) {
				$this->FAVORITES =   new Stack($this->POD,'content',array('flag.name'=>'favorite','flag.userId'=>$this->get('id')),'flag.date DESC',$count,$offset,null,null,null,$this->id.'-person-favorites');	
				if (!$this->FAVORITES->success()) { 
					return null;
				}
			}
			return $this->FAVORITES;
		
		}

		function watched($count=20,$offset=0) {
			if (!$this->get('id')) {
				return null;
			}		
			if (!$this->WATCHED) {
				$this->WATCHED =   new Stack($this->POD,'content',array('flag.name'=>'watching','flag.userId'=>$this->get('id')),'d.commentDate DESC',$count,$offset,null,null,null,$this->id.'-person-watched');	
				if (!$this->WATCHED->success()) { 
					return null;
				}
			}
			return $this->WATCHED;
		
		}
		
		function asArray() { 
		
			$data = parent::asArray();
			// remove some fields

			
			unset($data['email']);
			unset($data['verificationKey']);
			unset($data['authSecret']);
			unset($data['password']);
			unset($data['passwordResetCode']);
			
			
			return $data;

		}


/* Loader Functions */


	function save($nomail = false) {
		
		$profilePath = $this->POD->libOptions('profilePath');
		
		$this->success = false;
		
		$this->POD->tolog("user->save() " . $this->get('nick'));



		// clean up input
		$this->set('nick',stripslashes(strip_tags($this->get('nick'))));
		$this->set('email',stripslashes(strip_tags($this->get('email'))));


		if ($this->get('nick') == "") {
			$this->throwError("Missing required field nick.");
			$this->error_code=201;
			return null;
		}
		if ($this->get('email') == "") {
			$this->throwError("Missing required field email.");
			$this->error_code=202;
			return null;
		}

		if (!$this->get('stub')) {
			$stub = $this->get('nick');			
			$stub = preg_replace("/\s+/","-",$stub);
			$stub = preg_replace("/[^a-zA-Z0-9\-]/","",$stub);
			$stub = strtolower($stub);
		} else {
			$stub = $this->get('stub');
		}

		$newstub = $stub;
	
		$this->POD->tolog("Begin Checking stub...");
		
		// check and see if any users already use this stub.
		$stubcheck = $this->POD->getPerson(array('stub'=>$stub));

		$counter = 2;
		while ($stubcheck->success() && $stubcheck->get('id')!=$this->get('id')) {
		
			$newstub = $stub . "_" . $counter++;
			$stubcheck = $this->POD->getPerson(array('stub'=>$newstub));				
		}
		
		$this->POD->tolog("End Checking stub...");
		$stub = $newstub;
										
		$this->set('stub',$stub);
		$stub = mysql_real_escape_string($stub);	


		// Do I need to create a user or update a user?
		if (!$this->saved()) { 

			// CREATE NEW USER!
			
			$this->set('memberSince','now()');


			// new users must specify a password, though we will not store it in the db			
			if ($this->get('password') == "") {
				$this->throwError("Missing required field password.");
				$this->error_code=203;
				return null;
			}		

			$error = $this->checkUsernames($this->get('nick'),$this->get('email'),'');
			if ($error == "nick_taken") {
				$this->throwError("Oops!  The name you specified is already being used by someone else on the site.  Please pick a new one.");
				$this->error_code = 204;
				return;
			} else if ($error == "email_taken") {
				$this->throwError("Ooops! The email address you specified is already registered on the site.");
				$this->error_code = 205;
				return;
			}
				
				
			// FIX THIS
			// Should use an oop method for handling invites.	
			if ($this->get('invite_code') != '') {
				$this->POD->tolog('user->save() Looking for invite.');
				$sql = "SELECT * FROM invites WHERE code='" . $this->get('invite_code') . "';";
				$this->POD->tolog($sql,2);
				$res = mysql_query($sql,$this->POD->DATABASE);
				$num = mysql_num_rows($res);
				if ($num > 0) {
					$this->POD->tolog("user->save() INVITE FOUND");
					$invite = mysql_fetch_assoc($res);
					$sql = "DELETE FROM invites WHERE id=" . $invite['id'];
					$this->POD->tolog($sql,2);
					mysql_query($sql,$this->POD->DATABASE);		
				}
			}
				
			$authSecret = md5($this->get('email') . $this->get('password'));
			$this->set('authSecret',$authSecret);
			
			// now that we've generated the authSecret, we can clear the password
			$this->set('password',null);

			$this->generatePermalink();
						
			
			if (isset($invite)) {
			
				$this->POD->tolog('user->save() Invite found, processing...');
				$invitedBy = $invite['userId'];
				$this->set('invitedBy',$invitedBy);
				
				// members who are invited by other members do not need to confirm their emails
				$this->set('verificationKey',null);

				parent::save();
				if (!$this->success()) { 
					$this->POD->cacheclear($this);
					return null;				
				}						
				
				$this->POD->changeActor(array('id'=>$this->get('id')));
				
				if (isset($invite['groupId'])) {
					$this->POD->tolog('user->save() Adding user to group');
					$group = $this->POD->getGroup(array('id'=>$invite['groupId']));
					$group->addMember($this,'member',true);					
				}

				$inviter = $this->POD->getPerson(array('id'=>$invitedBy));
				
				// add the person who invited me as a friend, and send an email
				$this->addFriend($inviter);
				
				// cause the friend who invited me to add me as a friend, but do not send email
				$inviter->addFriend($this,false);				
				
			} else {
				
				// new members have to confirm their email address
				$this->set('verificationKey',md5($this->get('password').$this->get('email')));

				parent::save();
				if (!$this->success()) { 
					$this->POD->cacheclear($this);
					return null;				
				}					

			}
			
			$this->success = true;
			if (!$nomail) { 
				$this->POD->tolog("user->save() user created, sending welcome email");
				$this->welcomeEmail();
			}	
		
		} else {
			// UPDATE USER
		
			$this->POD->tolog("user->save() Updating user " . $this->get('nick'));
			
			$error = $this->checkUsernames($this->get('nick'),$this->get('email'),$this->get('id'));
			if ($error == "nick_taken") {
				$this->throwError("Oops!  The name you specified is already being used by someone else on the site.  Please pick a new one.");
		 		$this->error_code = 208;
				$this->POD->cacheclear($this);
				return;
			} else if ($error == "email_taken") {
				$this->throwError("Oops! The email address you specified is already registered on the site.  You might need to <a href=\"" . $this->POD->siteRoot(false) . "/login.php\">log in</a>.");
		 		$this->error_code = 209;
				$this->POD->cacheclear($this);
				return;
			}

			if ($this->get('password')) { 
				$this->set('authSecret',md5($this->get('email') . $this->get('password')));		
				$this->set('password',null);	
			}

	
			parent::save();
			if (!$this->success()) { 
				$this->POD->cacheclear($this);
				return null;				
			}			
		
		}	
		

		$this->stuffUser();
		$this->success = true;
		$this->POD->cachestore($this);
		return $this;
	
	} // end function save()


	function generatePermalink() { 
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array());
		}
	
		$profilePath = $this->POD->siteRoot(false) .$this->POD->libOptions('profilePath');
		$this->set('permalink',"$profilePath/" . $this->get('stub'));
	
	}

	function delete() {
	
		$this->success = false;
		
		if ($this->get('id')=='') {
			$this->throwError("User not saved yet!");
			$this->error_code = 222;
			return false;
		}
		
		// can only be deleted by self or adminUser
		if ($this->POD->isAuthenticated() && (($this->POD->currentUser()->get('id') == $this->get('id')) || ($this->POD->currentUser()->get('adminUser')))) {
			if ($this->get('id')) { 
			
				$this->POD->cacheclear($this);

				$id = $this->get('id');
				// get all the documents, delete them
				// this should delete any watch, favorite, votes, etc.
				$docs = $this->POD->getContents(array('userId'=>$id),null,1000000);
				while ($doc = $docs->getNext()) { 
					$doc->delete();
					if (!$doc->success()) {
						$this->throwError($doc->error());
						$this->error_code = $doc->errorCode();
						return false;
					}
				}
				
				$this->files()->reset();
				while ($file = $this->files()->getNext()) {
					$file->delete();
				}
				// get rid of any remaining comments by this user in other threads
				mysql_query("DELETE FROM comments WHERE userId=$id",$this->POD->DATABASE);		

				mysql_query("DELETE FROM activity WHERE userId=$id or targetUserId=$id",$this->POD->DATABASE);		

				mysql_query("DELETE FROM alerts WHERE userId=$id or targetUserId=$id;",$this->POD->DATABASE);		


				// get rid of any remaining comments left on this user's profile.
				mysql_query("DELETE FROM comments WHERE profileId=$id",$this->POD->DATABASE);		
								
				// group memberships		
				mysql_query("DELETE FROM groupMember WHERE userId=$id",$this->POD->DATABASE);
				
				// meta		
				mysql_query("DELETE FROM meta WHERE type='user' and itemId=$id",$this->POD->DATABASE);	
				
				// outgoing flags
				mysql_query("DELETE FROM flags WHERE userId=$id",$this->POD->DATABASE);
				
				// incoming flags
				mysql_query("DELETE FROM flags WHERE type='user' and itemId=$id",$this->POD->DATABASE);
	
				// delete the messages
				mysql_query("DELETE FROM messages WHERE fromId=$id OR targetUserId=$id",$this->POD->DATABASE);		

				// delete the user totally
				mysql_query("DELETE FROM users WHERE id=$id",$this->POD->DATABASE);		

				$this->DATA = array();
				$this->success = true;
			}
		} else {
			$this->throwError("Access denied");
			$this->error_code = 401;
		}
		
		return $this->success;
	}


	function permalink($field = 'nick',$return = false) {
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($field,$return));
		}


		$string = "<a href=\"" . $this->get('permalink') . "\" class=\"person_permalink\" title=\"" . htmlentities($this->get('nick')) . "\">" . $this->get($field) . "</a>";
		if ($return) { 
			return $string;
		} else {
			echo $string;
		}
	}


	function stuffUser() {
		
		if ($this->get('id')) {
		
			$this->generatePermalink();
		
		}
	}
	
	
	function getContents($PARAMETERS = null,$sort="date DESC",$count=20,$offset=0) {

		$PARAMETERS['userId'] = $this->get('id');
		return $this->POD->getContents($PARAMETERS,$sort,$count,$offset);
	}

	function getUserByNick($nick) {

		$no_u_nick = preg_replace("/\_/"," ",$nick);

		$d = $this->POD->checkcache('Person','nick',$nick);
		if ($d) {
			$this->success = true;
			$this->DATA = $d;
		} else {
			$this->load('nick',$nick);
			if (!$this->success()) {
				$this->load('nick',$no_u_nick);
			}
			$this->stuffUser();
			$this->loadMeta();
			$this->POD->cachestore($this);
		}	
		return $this;

	}	
	
	function getUserByEmail($email) {
		$this->load('email',$email);
		$this->stuffUser();
		$this->loadMeta();
		$this->POD->cachestore($this);
		return $this;		
	}	

	function getUserById($uid) {
		$this->success = false;
		$d = $this->POD->checkcache('Person','id',$uid);
		if ($d) {
			$this->success = true;
			$this->POD->tolog("user->getUserById(): USING CACHE");
			$this->DATA = $d;
		} else {
			$this->POD->tolog("user->getUserById(); NOT USING CACHE");
			$this->load('id',$uid);
			if ($this->success()) { 
				$this->stuffUser();
				$this->loadMeta();
				$this->POD->cachestore($this);
				$this->success = true;
			}
		}
		return $this;
	}	
	
	function getUserByStub($stub) {
		$this->success = false;
		$d = $this->POD->checkcache('Person','stub',$stub);
		if ($d) {
			$this->success = true;
			$this->POD->tolog("user->getUserByStub(): USING CACHE");
			$this->DATA = $d;
		} else {
			$this->POD->tolog("user->getUserByStub(); NOT USING CACHE");
			$this->load('stub',$stub);
			if ($this->success()) { 
				$this->stuffUser();
				$this->loadMeta();
				$this->POD->cachestore($this);
				$this->success = true;
			}
		}
		return $this;
	}	


	function getUserByAuthSecret($authSecret) {
		$this->success = false;
		$d = $this->POD->checkcache('Person','auth',$authSecret);
		if ($d) { 
			$this->success = true;
			$this->POD->tolog("user->getUserByAuthSecret(): USING CACHE");
			$this->DATA = $d;
			$sql = "UPDATE users SET lastVisit=NOW() WHERE id=" . $this->get('id');
			$this->set('lastVisit',time());
			$this->POD->tolog($sql,2);
			mysql_query($sql,$this->POD->DATABASE);

		} else {
			$this->load('authSecret',$authSecret);
			$this->POD->tolog("user->getUserByAuthSecret(): NOT USING CACHE");
			if ($this->success()) {
				$sql = "UPDATE users SET lastVisit=NOW() WHERE id=" . $this->get('id');
				$this->POD->tolog($sql,2);
				mysql_query($sql,$this->POD->DATABASE);
				$this->set('lastVisit',time());

				$this->stuffUser();
				$this->loadMeta();		
				$this->POD->cachestore($this);
			}
		} 
		
		return $this;
	}	
	
	
	function getUserByPasswordResetCode($resetCode) {
		
		$this->load('passwordResetCode',$resetCode);
		$this->stuffUser();
		$this->loadMeta();

		$this->POD->cachestore($this);
		return $this;
	}	



/* Output Functions */

	function render($template = 'output',$backup_path=null) {
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($template,$backup_path));
		}	
		return parent::renderObj($template,array('user'=>$this),'people',$backup_path);

	}

	function output($template = 'output',$backup_path=null) {
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($template,$backup_path));
		}
		parent::output($template,array('user'=>$this),'people',$backup_path);

	}




/* Awesome functions */


	function avatar($width=null) {
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array());
		}	
		if ($width==null) {
			$width = $this->POD->libOptions('peopleIconMaxWidth');
		}
		if ($img = $this->files()->contains('file_name','img')) {
			return $img->thumbnail;
		} else {
			return $this->POD->templateDir(false) . '/img/noimage.png';
		}
	}


	/*
	* Recommend Friends based on Friend-of-Friend network
	*
	*/
	function recommendFriends($minimumoverlap = 2,$max=20,$offset=0) {
	

		$uid = $this->id;
		
		// get a list off potential recommended friend ids
		$sql = "select foaf.itemId as uid,count(foaf.userId) as count
				from flags foaf inner join flags friends on friends.itemId=foaf.userId and friends.type='user' and friends.name='friends' 
				left join flags isFriends on foaf.itemId=isFriends.itemId and isFriends.type='user' and isFriends.name='friends' and isFriends.userId={$uid} 
				where foaf.type='user' and friends.userId={$uid} and foaf.itemId!={$uid} and foaf.userId!={$uid} and isFriends.itemId is null 
				group by foaf.itemId having count >= {$minimumoverlap};";


		$res = $this->POD->executeSQL($sql);
		
		$friends = array();
		while ($record = mysql_fetch_assoc($res)) {
			$friends[] = $record['uid'];
		}

		if (sizeof($friends) > 0) {
			return $this->POD->getPeople(array('id'=>$friends),'lastVisit desc',$max,$offset);
		} else {
			return $this->POD->getPeople(array('1'=>2));
		}
	}

		
	function friendList() {
		
		return $this->friends()->extract('id');		
	
	}

	
	function getVote($doc) {
		$val = $doc->hasFlag('vote',$this);		
		$this->success = $doc->success();
		if (!$this->success()) { 
			$this->throwError($doc->error());
			$this->error_code = $doc->errorCode();
		}
		return $val;
	}
	
	function getActivityStream($count=10) {
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($count));
		}
	
	
		if ($this->saved()) { 
			return $this->POD->getActivityStream(array(
				'or'=>array(
					'userId'=>$this->get('id'),
					'targetUserId'=>$this->get('id'),
				),
			),'date desc',$count);
		} else {
			return null;
		}
	}
	

	function publishActivity($message,$userMessage=null,$targetMessage=null,$targetUser=null,$targetContent=null,$resultContent=null,$gid=null) {
	
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($message,$userMessage,$targetMessage,$targetUser,$targetContent,$resultContent,$gid));
		}


		$act = $this->POD->getActivity();
		$act->publish($this->id,$message,$userMessage,$targetMessage,$targetUser,$targetContent,$resultContent,$gid);
	}


	function getAlerts($count=10) { 
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($count));
		}
	
		if ($this->saved()) { 
			return $this->POD->getAlerts(array(
				'targetUserId'=>$this->get('id'),
				'status'=>'new',
			),'date desc',$count);
		} else {
			return null;
		}
	}

	function sendAlert($message,$actingUser=null,$targetContent=null,$ok_to_send_email=true) { 
		
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($message,$actingUser,$targetContent,$ok_to_send_email));
		}

		$alert = $this->POD->getAlert();
		$alert->publish($this->id,$message,$actingUser,$targetContent,$ok_to_send_email);
	}

	function expireAlertsAbout($obj) {

		if ($obj->TYPE=='user') { 
			$alerts = $this->POD->getAlerts(array(
				'targetUserId'=>$this->get('id'),
				'userId'=>$obj->id,
				'targetContentId'=>'null'
			),'date desc',500);			
		} else {
		
			$alerts = $this->POD->getAlerts(array(
				'targetUserId'=>$this->get('id'),
				'targetContentId'=>$obj->id,
				'targetContentType'=>$obj->TYPE
			),'date desc',500);		
		
		}
		
		foreach ($alerts as $alert) {
			$alert->markAsRead();
		}
	}
	
/*************************************************************************************	
* Comment Watching 																			
*************************************************************************************/

	function isWatched($doc) {

		$this->success = false;
		$val = $doc->hasFlag('watching',$this);		
		$this->success = $doc->success();
		if (!$this->success()) { 
			$this->throwError('isWatched ' . $doc->error());
			$this->error_code = $doc->errorCode();
		}
		return $val;				
	}

	function removeWatch($doc) {

		$val = $doc->removeFlag('watching',$this);		
		$this->POD->cachefact($this->id . '-person-watched',null);
		$this->success = $doc->success();
		if (!$this->success()) { 
			$this->throwError('removeWatch ' . $doc->error());
			$this->error_code = $doc->errorCode();
		} else {
			if ($this->watched()->full()) { 
				$this->watched()->fill();
			}
		}
		return $doc;	
	}
	

	function addWatch($doc,$start_from_beginning = false) {
		
		if ($doc->comments()->count() > 0) { 
			$doc->comments()->reset();
			while ($c = $doc->comments()->getNext()) {
				$lastcomment = $c->get('id');
			}
			$doc->comments()->reset();
		} else {
			$lastcomment = 1;
		}	
		
		if ($start_from_beginning) {
			$lastcomment = 1;
		}

		// we need to purge any pre-existing flag.
		$doc->removeFlag('watching',$this);
		$val = $doc->addFlag('watching',$this,$lastcomment);		
		$this->POD->cachefact($this->id . '-person-watched',null);

		$this->success = $doc->success();
		if (!$this->success()) { 
			$this->throwError('addWatch ' . $doc->error());
			$this->error_code = $doc->errorCode();
		} else {
			if ($this->watched()->full()) { 
				$this->watched()->fill();
			}
		}
		return $doc;

	}

	function toggleWatch($doc) {
		
		if ($this->isWatched($doc)) {
			$this->removeWatch($doc);
			return 0;
		} else {
			$this->addWatch($doc);
			return 1;
		}	
	}

	
/*************************************************************************************	
* FAVORITES 																			
*************************************************************************************/

	function isFavorite($doc) {
		$val = $doc->hasFlag('favorite',$this);		
		$this->success = $doc->success();

		if (!$this->success()) { 
			$this->throwError('isFavorite ' . $doc->error());
			$this->error_code = $doc->errorCode();
		}
		return $val;
	}

	function removeFavorite($doc) {

		$val = $doc->removeFlag('favorite',$this);		
		$this->success = $doc->success();
		$this->POD->cachefact($this->id . '-person-favorites',null);

		if (!$this->success()) { 
			$this->throwError('removeFavorite ' . $doc->error());
			$this->error_code = $doc->errorCode();
		} else {
			if ($this->favorites()->full()) { 
				$this->favorites()->fill();
			}
		}
		return $doc;
		
	}

	function addFavorite($doc) {

		$val = $doc->addFlag('favorite',$this);		
		$this->success = $doc->success();
		$this->POD->cachefact($this->id . '-person-favorites',null);

		if (!$this->success()) { 
			$this->throwError('addFavorite ' . $doc->error());
			$this->error_code = $doc->errorCode();
		} else {
			if ($this->favorites()->full()) { 
				$this->favorites()->fill();
			}
		}
		return $doc;

	}

	function toggleFavorite($doc) {

		$val = $doc->toggleFlag('favorite',$this);		
		$this->success = $doc->success();
		if (!$this->success()) { 
			$this->throwError('toggleFavorite ' . $doc->error());
			$this->error_code = $doc->errorCode();
		} else {
			if ($this->favorites()->full()) { 
				$this->favorites()->fill();
			}
		}
		return $val;
		
		
	}



/*************************************************************************************	
* FRIENDS 																			
*************************************************************************************/



	function isFriendsWith($person) {
		
		$val = $person->hasFlag('friends',$this);		
		$this->success = $person->success();
		if (!$this->success()) { 
			$this->throwError('isFriendsWith ' . $person->error());
			$this->error_code = $person->errorCode();
		}
		return $val;
	}

	function removeFriend($person) {
	
		$val = $person->removeFlag('friends',$this);		
		$this->success = $person->success();
		$this->POD->cachefact($this->id . '-person-friends',null);
		$this->POD->cachefact($person->id . '-person-followers',null);

		if (!$this->success()) { 
			$this->throwError('removeFriend ' .$person->error());
			$this->error_code = $person->errorCode();
		} else {
			if ($this->favorites()->full()) { 
				$this->favorites()->fill();
			}
		}
		return $person;
		
	
	}
	
	
	function addFriend($person,$sendEmail=true) {
		
		$this->POD->tolog("user->addFriend(): Adding friend relationship between " . $this->get('nick') . " and " . $person->get('nick'));

		$wasAlreadyFriends = $this->isFriendsWith($person);
		$val = $person->addFlag('friends',$this);		
		$this->success = $person->success();
		$this->POD->cachefact($this->id . '-person-friends',null);
		$this->POD->cachefact($person->id . '-person-followers',null);

		if (!$this->success()) { 
			$this->throwError('addFriend ' . $person->error());
			$this->error_code = $person->errorCode();
		} else {
			
			if ($this->POD->libOptions('friendActivity')) { 
				$this->publishActivity('{actor.nick} is now following {targetUser.nick}','You are now following {targetUser.nick}','{actor.nick} is now following you',$person,null,null,$this->id . '-friend-' . $person->id);
			}
			
			if ($this->POD->libOptions('friendAlert')) { 
				$person->sendAlert('{actor.nick} is now following you.',$this);
			}
			
			if ($sendEmail && !$wasAlreadyFriends) {
				if ($this->POD->libOptions('friendEmail')) {
					$this->sendEmail("addFriend",array('to'=>$person->get('email')));
				}
			}

			if ($this->friends()->full()) { 
				$this->friends()->fill();
			}
		}
		return $person;	
	}




/*************************************************************************************	
* COMMENTS 																			
*************************************************************************************/


	
		function comments() {		
			if (!$this->get('id')) {
				return null;
			}		
			if (!$this->COMMENTS) {
				$this->COMMENTS = $this->POD->getComments(array('profileId'=>$this->get('id')),'date asc',100,0,$this->id.'-person-comments');
				if (!$this->COMMENTS->success()) { 
					return null;
				}
			}
			return $this->COMMENTS;			
		
		}

		function addComment($comment,$type=null) {
		
			$this->success= false;
			if (!$this->get('id')) {
			
				$this->throwError("User not saved yet!");
				$this->error_code = 500;
				return;
			}
			
			if (!$this->POD->isAuthenticated()) { 
				$this->throwError("Access denied");
				$this->error_code = 401;
				return;
			}
			
			$newcomment = $this->POD->getComment();
	
			$newcomment->set('profileId',$this->get('id'));
			$newcomment->set('comment',$comment);
			$newcomment->set('type',$type);
			$newcomment->set('userId',$this->POD->currentUser()->get('id'));
			$newcomment->save();
	
			if ($newcomment->success()) {

				$this->comments()->add($newcomment);

				// if this is a comment being left on someone else's content
				// publish an activity stream item.
				if ($this->POD->currentUser()->id!= $this->id) { 
				
					if ($this->POD->libOptions('profileCommentActivity')) { 
						$this->POD->currentUser()->publishActivity('{actor.nick} left <a href="@resultContent.permalink">a comment</a> on {targetUser.nick}\'s profile.','You left <a href="@resultContent.permalink">a comment</a> on {targetUser.nick}\'s profile.','{actor.nick} left <a href="@resultContent.permalink">a comment</a> on your profile.',$this,null,$newcomment);
					}
					if ($this->POD->libOptions('profileCommentAlert')) { 
						$this->sendAlert('{actor.nick} left <a href="@targetContent.permalink">a comment on your profile</a>.',$this->POD->currentUser(),$newcomment);
					}
				}

				$this->success = true;
				return $newcomment;
			} else {
				$this->throwError($newcomment->error());
				$this->error_code = $newcomment->errorCode();
				return;				
			}
		}


/*************************************************************************************	
* HELPERS 																			
*************************************************************************************/



	function sendEmail($email_name,$vars = null,$backup_path=null) {

		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($email_name,$vars));
		}

		$this->success = null;
	
		// set up some variables
		// we know that we'll have a user, because the email is going to them.
		$sender = $this;
		$to = $this->get('email');
		$document = null;
		$group = null;
		$message = null;
		$code = null;
		
		$subject = "Email from " . $this->POD->libOptions('siteName');
	
		
		// we might also have a document, like when someone shares a post with someone else
		if (isset($vars['document'])) { 
			$document = $vars['document'];
		}


		if (isset($vars['subject'])) { 
			$subject = stripslashes($vars['subject']);
		}		
		


		// we might also have a group, like when someone invites someone to a group
		if (isset($vars['group'])) {
			$group = $vars['group'];
		}
		
		// and we might have a custom message, like when someone sends a personal note.
		if (isset($vars['message'])) {
			$message = stripslashes($vars['message']);
		}
		
		// by default, we assume this is an email going to this user.  but maybe we're sending a note or inviting someone.
		if (isset($vars['to'])) {
			$to = $vars['to'];
		}
		
		// finally, we may have an invite code or some other kind of secret code. 
		if (isset($vars['code'])) {
			$code = $vars['code'];
		}
	
		$POD = $this->POD;
	
		// using output buffering, we can just include the output of the appropriate email template and capture it in a string
		// the email templates should also reset $subject
		ob_start();
	
		if (file_exists($this->POD->libOptions('templateDir') . "/emails/" . $email_name . ".php")) {
			include($this->POD->libOptions('templateDir') . "/emails/" . $email_name . ".php");
		} else if ($backup_path) {
			include($backup_path . "/" . $email_name . ".php");	
		}
			
		$body = ob_get_contents();
		
		ob_end_clean();

		$headers = "From: " . $this->POD->libOptions('fromAddress') . "\r\n" . "X-Mailer: PeoplePods - XOXCO.com";
		$this->POD->tolog("Sending email: $subject to $to");
		if (mail($to, $subject, $body, $headers)) {
			$this->success = true;
		} else {
			$this->POD->tolog("Failed to send email $email_name to " . $to);
		}
		
		return $this->success;
	}



	
	function sendInvite($email,$message,$groupId = null) { // send an invite to someone.  optionally include a group to be invited to.
		
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($email,$message,$groupId));
		}


		$touser = $this->POD->getPerson(array('email'=>$email));
		if ($touser->success()) {
		// this person is already a member. 
		// add friend and/or invite to group 
		
			if (isset($groupId)) {
				
				$group = $this->POD->getGroup(array('id'=>$groupId));			
			// add group invitee membership
				$this->POD->tolog('user->sendInvite(): inviting existing user to group');
				$group->addMember($touser,'invitee',true);
				
				$this->sendEmail('invite',array('group'=>$group,'message'=>$message,'to'=>$touser->get('email')));

			} else {
				$this->POD->tolog('user->sendInvite(): adding friend on site');
				$this->addFriend($touser);
			}	
		} else {
			if (isset($groupId)) {
				$this->POD->tolog('user->sendInvite(): inviting new user to group');

				$group = $this->POD->getGroup(array('id'=>$groupId));			

			// generate invite key
				$vkey = md5($email . time() . $this->get('nick'));
				$sql = "INSERT INTO invites (userId,groupId,date,email,code) VALUES (" . $this->get('id') . "," . $group->get('id') . ",NOW(),'" . mysql_real_escape_string($email) . "','" . $vkey . "');";
				$this->POD->tolog($sql,2);
				mysql_query($sql,$this->POD->DATABASE);

				$this->sendEmail('invite',array('group'=>$group,'message'=>$message,'to'=>$email,'code'=>$vkey));
				
			} else {
				$this->POD->tolog('user->sendInvite(): inviting new user to join');		
		
				// generate invite key
				$vkey = md5($email . time() . $this->get('nick'));
					
				$sql = "INSERT INTO invites (userId,date,email,code) VALUES (" . $this->get('id') . ",NOW(),'" . mysql_real_escape_string($email) . "','" . $vkey . "');";
				$this->POD->tolog($sql,2);
				mysql_query($sql,$this->POD->DATABASE);

				$this->sendEmail('invite',array('message'=>$message,'to'=>$email,'code'=>$vkey));

			}
		}
				
	}


	function isVerified() {
		return ($this->get('verificationKey')=='');
	}

	function verify($code) {
		$this->success = null;	
	
		$this->POD->tolog("user->verify(): Does $code = " . $this->get('verificationKey'));
		if ($code == $this->get('verificationKey')) {
			$this->POD->tolog("user->verify(): VERIFIED");
			$this->set('verificationKey',null);
			$this->save();
		} else {
			$this->error_code = 221;
			$this->throwError("Could not verify: verification code incorrect");
		}
	

	}
	
	
	function sendMessage($message,$ok_to_send_email=true) {

		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($message,$ok_to_send_email));
		}

	
		$this->success = null;
		
		$msg = new Message($this->POD,array('targetUserId'=>$this->get('id'),'message'=>$message));
		$msg->save($ok_to_send_email);
		if ($msg->success()) { 
			$this->success = true;
			return $msg;		
		} else {
			$this->throwError($msg->error());
			$this->error_code = $msg->errorCode();
			return null;
		}	
	
	}
	

	function sendPasswordReset() { // send a password reset message to this user via email
		return $this->sendEmail('passwordReset');			
	}



	function welcomeEmail() { // send a welcome/verify your email message to this user via email
		return $this->sendEmail('welcome');			
	}

	function checkUsernames($nick,$email,$id) {
		
		
		$nick = mysql_real_escape_string(stripslashes($nick));
		$email = mysql_real_escape_string(stripslashes($email));
		$idsql = '';
		if ($id != '') {
			$idsql = "AND users.id!=$id ";
		}
		
		$sql = "SELECT nick='$nick' as nicktaken,email='$email' as emailtaken FROM users WHERE (nick='$nick' OR email='$email') $idsql;";
		$this->POD->tolog($sql,2);
		$res = mysql_query($sql,$this->POD->DATABASE);
		$num = mysql_num_fields($res);
		if ($num > 0) {
			$error = mysql_fetch_assoc($res);
			mysql_free_result($res);
			if ($error['nicktaken']==1) {	
				return 'nick_taken';
			} 
			if ($error['emailtaken']==1) {
				return 'email_taken';
			}
		} else {
			return;
		}
	
	}


		
		function hasMethod($method) { 
			return (isset(self::$EXTRA_METHODS[$method]));		
		}
		
		function override($method,$args) { 
		    if (isset(self::$EXTRA_METHODS[$method])) {
		      array_unshift($args, $this);
		      return call_user_func_array(self::$EXTRA_METHODS[$method], $args);
		    } else {
		    	$this->throwError('Unable to find execute plugin method: ' . $method);
		    	return false;
		    }				
		}
		
		function registerMethod($method,$alias=null) { 
			$alias = isset($alias) ? $alias : $method;
			self::$EXTRA_METHODS[$alias] = $method;
		}

		function addDatabaseFields($fields) { 
			foreach ($fields as $field=>$options) {
				self::$DEFAULT_FIELDS[] = $field;
				if ($options['select'] ) {
					self::$FIELD_PROCESSORS[$field . '_select'] = $options['select'];
				}
				if ($options['insert'] ) {
					self::$FIELD_PROCESSORS[$field . '_insert'] = $options['insert'];
				}
			}
		}
		function addIgnoreFields($fields) { 
			self::$IGNORE_FIELDS = array_merge(self::$IGNORE_FIELDS,$fields);
		}

		function __call($method,$args) { 
		
		    if (isset(self::$EXTRA_METHODS[$method])) {
		      array_unshift($args, $this);
		      return call_user_func_array(self::$EXTRA_METHODS[$method], $args);
		    } else {
		    	$this->throwError('Unable to find execute plugin method: ' . $method);
		    	return false;
		    }	

		
		}
		


} // end Person class

// -------------------------------------------------------------------------------------------------------- //
// -------------------------------------------------------------------------------------------------------- //
// -------------------------------------------------------------------------------------------------------- //
// -------------------------------------------------------------------------------------------------------- //
// -------------------------------------------------------------------------------------------------------- //
// -------------------------------------------------------------------------------------------------------- //


?>
