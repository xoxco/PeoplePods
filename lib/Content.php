<? 
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* lib/Content.php
* This file defines the Content object.  This object handles all of the different kinds of content
* that might exist in a peoplepods site.
*
* Documentation for this object can be found here:
* http://peoplepods.net/readme/content-object
/**********************************************/
	require_once("Obj.php"); 
	class Content extends Obj{

		protected $CHILDREN;
		protected $COMMENTS;
		protected $FILES;
		protected $GROUP;
		protected $IS_FAVORITE = false;

		protected $VOTE = null;

		// base database configuration for this object.
		static private $DEFAULT_FIELDS = array('id','userId','createdBy','parentId','groupId','date','changeDate','body','headline','link','type','privacy','status','flagDate','commentCount','yes_votes','no_votes','stub','editDate','commentDate','hidden');
		static private $IGNORE_FIELDS =array('timesince','permalink','yes_percent','no_percent','editlink','editpath','minutes');	
		static private $DEFAULT_JOINS = array ( 
						'u' => 'inner join users u on u.id=d.userId',  // link to content author
						'a' => 'inner join users a on a.id=d.userId',  // link to content author
						'o' => 'inner join users o on o.id=d.createdBy',  // link to content creator
						'g' => 'inner join groups g on g.id=d.groupId', // link to group
						'p' => 'inner join content p on p.id=d.parentId', // link to parent content
						'f' => 'inner join files f on f.contentId=d.id', // link to files
						'c' => 'inner join comments c on c.contentId=d.id', // link to comments
						't' => 'inner join tagRef tr on tr.itemId=d.id and tr.type="content" inner join tags t on tr.tagId=t.id', // link to tags
					);
		static private $FIELD_PROCESSORS = array();


		static private $EXTRA_METHODS = array();
				
		/* 
		* Content Constructor
		* @var $POD peoplepod object
		* @var array Associative array of parameters that MUST include POD, but may also include id or stub to load, or a list of default values.
		* @return Content New object
		*/ 
		function Content($POD,$PARAMETERS=null) {
				parent::Obj($POD,'content',array(
					'table_name' => "content",
					'table_shortname' => "d",
					'fields' => self::$DEFAULT_FIELDS,
					'ignore_fields'=>self::$IGNORE_FIELDS,				
					'joins' => self::$DEFAULT_JOINS,
					'field_processors'=>self::$FIELD_PROCESSORS
				));

				if (!$this->success()) {
					return $this;
				}
				
				
				// Load a document from the database or from defaults, based on the parameters
				if (isset($PARAMETERS['id']) && (sizeof($PARAMETERS)==1)) { 
					// load by ID
					$this->getContentById($PARAMETERS['id']);		
					if (!$this->success()) {
						return;
					}				
				} else if (isset($PARAMETERS['stub']) && (sizeof($PARAMETERS)==1)) {		
					// load by unique stub
					$this->getContentByStub($PARAMETERS['stub']);
					if (!$this->success()) {
						return;
					}				

				} else if ($PARAMETERS) {
					// create based on parameters
					$this->POD->tolog("content->new Create doc from parameters");
					
					$fill = true;
					if (isset($PARAMETERS['id'])) {
						$d = $this->POD->checkcache('Content','id',$PARAMETERS['id']);
						if ($d) {
							$fill = false;
							$this->DATA = $d;
						} 
					}
			
					if ($fill) { 
						foreach ($PARAMETERS as $key=>$value) {
							$this->set($key,$value);
						}
			
								
						if (!$this->get('id')) {
							if (!$this->POD->isAuthenticated()) { 
								$this->success = false;
								$this->throwError("No current user! Can't create content!");
								return null;
							}
		
							$this->set('userId',$this->POD->currentUser()->get('id'));
						}
						
						$this->stuffDoc();	
						$this->POD->cachestore($this);
					}
				} else {	
					// this is a brand new content object	
					// we still need to call generatePermalink because some other stuff gets set up		
					$this->generatePermalink();

				}

				
				
				
				if (!$this->isViewable()) { 
					// do not use throwError because we don't necessarily need to complain about this to the logs
					$this->error = "Access denied to " . ($this->POD->isAuthenticated() ? 'user #'.$this->POD->currentUser()->id : 'anon user') . " to content #" . $this->id . " because of insufficient permissions.";
					$this->success = false;
					$this->DATA = array();
					return $this;
				}

					
					
				$this->success = true;
				return $this;	
		
		}
	
/*********************************************************************************************
* Privacy
*********************************************************************************************/

	function isEditable() {		
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array());
		}

		if ( $this->POD->isAuthenticated() && (($this->get('userId') == $this->POD->currentUser()->get('id')) || ($this->POD->currentUser()->get('adminUser')) || ($this->get('createdBy') == $this->POD->currentUser()->get('id')) ||!$this->saved()) ) {
			// if there is a user logged in, and this user is the creator of this content, set the editable flag to true.
			return true;
		}
		
		return false;
	}


	function isViewable() { 

		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array());
		}
			
		if ($this->get('privacy') == "friends_only") { 
		
			if ($this->POD->isAuthenticated() && $this->author()->isFriendsWith($this->POD->currentUser())) {
				// the author of this document is friends with $user, so friends_only applies!
				return true;
			} else if ($this->isEditable()) {
				// this document is editable by $user, so yeah, its viewable too.
				return true;
			} else {
				return false;
			}		
		}


		if ($this->get('privacy') == "owner_only") { 
			if ($this->isEditable()) {
				// i own this, so of course I can see it
				return true;
			} else {
				return false;
			}		
		}

		if ($this->get('privacy') == "group_only") { 
			$group = $this->POD->getGroup(array('id'=>$this->get('groupId')));
			if ($group->success()) { 
				if ($this->POD->isAuthenticated() && $group->isMember($this->POD->currentUser())) {
					// OK! we are authenticated and this person is a member of the group that this doc is in.	
					return true;
				} else if ($this->isEditable()) {
					// i own this, so of course I can see it
					return true;
				} else {
					return false;
				}		
			}
		}
		
		return true;
				
	}					
	
	
/*********************************************************************************************
* Accessors
*********************************************************************************************/
	
		
		function children() {		
			if (!$this->get('id')) {
				return null;
			}		
			if (!$this->CHILDREN) {
				$this->CHILDREN = $this->POD->getContents(array('parentId'=>$this->get('id')));
				if (!$this->CHILDREN->success()) { 
					return null;
				}
			}
			return $this->CHILDREN;	
		}
						
		function comments() {		
			if (!$this->get('id')) {
				return null;
			}		
			if (!$this->COMMENTS) {
				$this->COMMENTS = $this->POD->getComments(array('contentId'=>$this->get('id')),"date ASC",100,0,$this->get('id') . '-content-comments');
				if (!$this->COMMENTS->success()) { 
					return null;
				}
			}
			return $this->COMMENTS;			
		
		}
		
		
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
					$file->set('contentId',$this->get('id'));
					$file->set('description',$description);
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
			}
		
			return $this->success;
		
		}
		
		
		function files() {		
			if (!$this->get('id')) {
				return new Stack($this->POD,'file');
			}
			if (!$this->FILES) { 
				$this->FILES = new Stack($this->POD,'file',array('contentId'=>$this->get('id')),null,100,0,null,null,null,$this->id.'-content-files');
				if (!$this->FILES->success()) { 
					return new Stack($this->POD,'file');
				}
			}
			return $this->FILES;
		}

		function isFavorite() {		
			return $this->IS_FAVORITE;
		}

	
		function asArray() { 
		
			$data = parent::asArray();
			// remove some fields
	
			return $data;
			

		}

	
/*	Functions that load things */

		function save($strip_html = true) {

	// set up some options
		
			$this->success = false;
			$this->POD->tolog("content->save()");			

			if (!$this->POD->isAuthenticated()) { 
				$this->throwError("No current user! Can't save content!");
				return null;
			}
			
			if (!$this->isEditable()) { 
				$this->throwError("Access Denied");
				$this->error_code = 401;
				return null;
			}			


			if ($strip_html) {
				$this->set('body',$this->POD->sanitizeInput($this->get('body')));			
			}	
			
			$this->set('body',stripslashes($this->get('body')));
			$this->set('headline',stripslashes(strip_tags($this->get('headline'))));
			$this->set('link',stripslashes(strip_tags($this->get('link'))));

			if (!$this->saved()) { 
				$this->set('date','now()');
				$this->set('editDate','now()');
				$this->set('minutes','0');
				$this->set('changeDate','now()');
				$this->set('yes_votes','0');
				$this->set('no_votes','0');
				$this->set('hidden','0');

			} else {
				$this->set('editDate','now()');
				$this->set('changeDate','now()');
			}			
			
			if ($this->get('privacy')=='') {
				$this->set('privacy','public');
			}
			
			// do this down here instead of at the top to catch cases where the headline is blank after stripping html
			if ($this->get('headline')=='') {
				$this->success = false;
				$this->throwError("Missing required fields");
				$this->error_code = 500;
				return null;
			}
			
			
			if (!$this->get('type')) { 
				$this->set('type','document');
			}

			if (!$this->get('status')) { 
				$this->set('status','new');
			}

			if ($this->get('createdBy') == '') {
				$this->set('createdBy',$this->POD->currentUser()->get('id'));
			}

			if ($this->get('userId') == '') {
				$this->set('userId',$this->get('createdBy'));
			}

			if (!$this->get('stub')) {
				$stub = $this->get('headline');			
				$stub = preg_replace("/\s+/","-",$stub);
				$stub = preg_replace("/[^a-zA-Z0-9\-]/","",$stub);
				$stub = strtolower($stub);
			} else {
				$stub = $this->get('stub');
			}
			
			$newstub = $stub;

			// check and see if any content already use this stub.
			$stubcheck = $this->POD->getContent(array('stub'=>$stub));
			$counter = 2;
			while ($stubcheck->success() && $stubcheck->get('id')!=$this->get('id')) {
			
				$newstub = $stub . "_" . $counter++;
				$stubcheck = $this->POD->getContent(array('stub'=>$newstub));				
			}
			
			$this->set('stub',$newstub);

			parent::save();
			
			if (!$this->success()) { 
				$this->POD->cacheclear($this);
				return null;
			}					
			
			$this->stuffDoc();	
			
			$this->POD->cachestore($this);

			$this->POD->tolog("content->save() ADD WATCH");			
			$this->POD->currentUser()->addWatch($this);

			$this->success= true;
			$this->POD->tolog("content->save(): Content saved!");
		}
	
		function changeStatus($status) {
			if ($this->get('id') && $this->isEditable()) { 
				$this->set('status',$status);
				$status = mysql_real_escape_string($status);
				$sql = "UPDATE content SET status='$status', changeDate=NOW(),flagDate=NOW() where id=" . $this->get('id');
				$this->POD->tolog($sql,2);
				$result = mysql_query($sql,$this->POD->DATABASE);	
				$num = mysql_affected_rows($this->POD->DATABASE);	
				if ($num < 1 || !$result) {
					$this->success = false;
					$this->throwError("SQL Error: Content Update failed!");
					$this->error_code = 500;
					return null;
				} else {
					$this->flagDate = $this->changeDate = time();
					$this->success = true;
					$this->POD->cachestore($this);
					return $this;
				}
			} else {
					$this->success = false;
					$this->throwError("Status change failed: permission denied");
					$this->error_code = 500;
					return null;			
			}
		}

	
		function delete($force=null,$non_destructive=false) {
			$this->success = false;
			if ($this->get('id')) {
				if ($this->isEditable() || $force) {
					
					$this->POD->cacheclear($this);

					if ($non_destructive) { 
						$sql = "UPDATE content SET hidden=1 WHERE id=" . $this->get('id');
						$this->POD->tolog($sql,2);
						mysql_query($sql,$this->POD->DATABASE);					
					
					} else {
					
						$this->POD->cacheclear($this);
						
						$sql = "DELETE FROM content WHERE id=" . $this->get('id');
						$this->POD->tolog($sql,2);
						mysql_query($sql,$this->POD->DATABASE);
						
		
						$this->files()->reset();
						while ($file = $this->files()->getNext()) {
							$file->delete();
						}
		
						$sql = "DELETE FROM tagRef WHERE contentId=" . $this->get('id');
						$this->POD->tolog($sql,2);
						mysql_query($sql,$this->POD->DATABASE);
						
						$sql = "DELETE FROM comments WHERE contentId=" . $this->get('id');
						$this->POD->tolog($sql,2);
						mysql_query($sql,$this->POD->DATABASE);
						
						$id = $this->get('id');
				
						// meta		
						mysql_query("DELETE FROM meta WHERE type='content' and itemId=$id",$this->POD->DATABASE);	

						mysql_query("DELETE FROM activity WHERE (targetContentId=$id and targetContentType='content') or (resultContentId=$id and resultContentType='content');",$this->POD->DATABASE);		
		
						mysql_query("DELETE FROM alerts WHERE (targetContentId=$id and targetContentType='content')",$this->POD->DATABASE);		
				
						$sql = "DELETE FROM flags WHERE type='content' and itemId=" . $this->get('id');
						$this->POD->tolog($sql,2);
						mysql_query($sql,$this->POD->DATABASE);
					
						$sql = "UPDATE content SET parentId=null WHERE parentId=" . $this->get('id');
						$this->POD->tolog($sql,2);
						mysql_query($sql,$this->POD->DATABASE);
						
						$this->COMMENTS = null;
						$this->TAGS = null;
						$this->CHILDREN = null;
						$this->DATA = array();	
					}						
					
					$this->success = true;
				}  else {
					$this->throwError("You do not have permission to delete this content.");
					$this->error_code=403;
				}
			} else {
				// hasn't been saved yet
				$this->throwError("No such content");
				$this->error_code = 404;
			}
		}


		function getContentById($did) {
			$this->success = null;

			if ($did != '' && preg_match("/\d+/",$did)) {
				$d = $this->POD->checkcache('Content','id',$did);
				if ($d) {
					$this->POD->tolog("content->getContentById(): USING CACHE");
					$this->DATA = $d;
				} else {
					$this->load('id',$did);
					if ($this->success()) { 
						$this->stuffDoc();
						$this->POD->cachestore($this);
					} else {
						return $this;
					}
				}
				$this->success = true;
				return $this;
			} else {
				$this->throwError("No content id specified");
				$this->error_code=500;
			}
		}
	
		
		function getContentByStub($stub) {
			$this->POD->tolog("content->getContentByStub($stub)");
			$d = $this->POD->checkcache('Content','stub',$stub);
			if ($d) {
				$this->POD->tolog("content->getContentByStub(): USING CACHE");
				$this->DATA = $d;
			} else {

				$this->load('stub',$stub);		
				if ($this->success()) { 
					$this->stuffDoc();		
					$this->POD->cachestore($this);
					$this->success = true;
				} 
			}

			return $this;
		}	

		
	

/*************************************************************************************	
* COMMENTS 																			
*************************************************************************************/

	
		function markCommentsAsRead() {
		
			if (!$this->get('id')) {
			
				$this->throwError("Content not saved yet!");
				$this->error_code = 500;
				return;
			}

			if (!$this->POD->isAuthenticated()) { 
				$this->throwError("Access denied");
				$this->error_code = 401;
				return;
			}
			
			$this->POD->currentUser()->addWatch($this);	
				
		}
		
		
		function goToFirstUnreadComment() {
			$last = 0;
			if ($this->POD->isAuthenticated()) { 
				$last = $this->POD->currentUser()->isWatched($this);
			}
			
			$last = $last * 1;
			$this->comments()->reset();
			while ($this->comments()->peekAhead() && $this->comments()->peekAhead()->get('id') <= $last) {
				$this->comments()->getNext(); 
			}

		}
		

		function addComment($comment,$type=null) {
		
			$this->success= false;
			if (!$this->get('id')) {
			
				$this->throwError("Content not saved yet!");
				$this->error_code = 500;
				return;
			}
			
			if (!$this->POD->isAuthenticated()) { 
				$this->throwError("Access denied");
				$this->error_code = 401;
				return;
			}
			
			$newcomment = $this->POD->getComment();
	
			$newcomment->set('contentId',$this->get('id'));
			$newcomment->set('comment',$comment);
			$newcomment->set('type',$type);
			$newcomment->set('userId',$this->POD->currentUser()->get('id'));
			$newcomment->save();
	
			if ($newcomment->success()) {

				$sql = "UPDATE content SET commentDate=NOW(),changeDate=NOW() where id=" . $this->get('id');
				$this->POD->tolog($sql,2);
				$result = mysql_query($sql,$this->POD->DATABASE);
				if (!$result) {
					$this->throwError("SQL Error: commentDate update failed!");
					$this->error_code = 500;
				}

				$this->commentDate = $this->changeDate = $newcomment->date;
					
				$this->comments()->add($newcomment);
				$this->POD->currentUser()->addWatch($this);

				// if this is a comment being left on someone else's content
				// publish an activity stream item.
				if ($this->POD->currentUser()->id!= $this->author()->id) { 
					if ($this->POD->libOptions('contentCommentActivity')) { 
						$this->POD->currentUser()->publishActivity('{actor.nick} left <a href="@resultContent.permalink">a comment</a> on {targetUser.nick}\'s post, {targetContent.headline}.','You left <a href="@resultContent.permalink">a comment</a> on {targetUser.nick}\'s post, {targetContent.headline}.','{actor.nick} left <a href="@resultContent.permalink">a comment</a> on your post, {targetContent.headline}.',$this->author(),$this,$newcomment);
					}
					
					if ($this->POD->libOptions('contentCommentAlert')) { 
						$this->author()->sendAlert('{actor.nick} left <a href="@targetContent.permalink">a comment on your post</a>.',$this->POD->currentUser(),$this);
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
* Groups 																			
*************************************************************************************/		

	function group($field=null) { 
		if ($this->get('groupId') && !$this->GROUP) { 
			$this->GROUP = $this->POD->getGroup(array('id'=>$this->get('groupId')));		
		}
		if ($field != null) {
			return $this->GROUP->get($field);
		} else {
			return $this->GROUP;	
		}
	}


	// this is a special function that bypasses normal update security to allow a group owner or manager to change the group and privacy settings of a content.
	function setGroup($groupId) { 
		$this->success = false;
		if (!$this->get('id')) {
		
			$this->throwError("Content not saved yet!");
			$this->error_code = 500;
			return;
		}		
		if (!$this->POD->isAuthenticated()) { 
			$this->throwError("Access denied");
			$this->error_code = 401;
			return;
		}	
			
		if ($groupId == "" || !$groupId) { 
			$group = $this->POD->getGroup(array('id'=>$this->get('groupId')));	
		} else {
			$group = $this->POD->getGroup(array('id'=>$groupId));
		}	
		
		if (!$group->success()) { 
			$this->throwError($group->error());
			$this->error_code = $group->errorCode();
			return;		
		}		
		$membership = $group->isMember($this->POD->currentUser());
		if ($group->success()) { 
			if (!($membership == "owner" || $membership == "manager")) { 
				$this->throwError("Access denied: Insufficient Group Privileges");
				return;		
			}
		} else {
			$this->throwError("Couldn't check membership: " . $group->error());
			return;
		}			
		$this->set('groupId',$groupId);
		
		if ($groupId == '') { 
			$groupId = "NULL";
		} else {
			$groupId= "'" . mysql_real_escape_string($groupId) . "'";
		}
		$privacy = mysql_real_escape_string($this->get('privacy'));
		
		$sql = "UPDATE content SET groupId=$groupId, privacy='$privacy', changeDate=NOW() where id=" . $this->get('id');
		$this->POD->tolog($sql,2);
		$result = mysql_query($sql,$this->POD->DATABASE);	
		$num = mysql_affected_rows($this->POD->DATABASE);	
		if ($num < 1 || !$result) {
			$this->success = false;
			$this->throwError("SQL Error: Set group failed!");
			$this->error_code = 500;
			return null;
		} else {
			$this->success = true;
			$this->POD->cachestore($this);
			return $this;
		}

	}

/*************************************************************************************	
* VOTING 																			
*************************************************************************************/

		
		function vote($vote) {
			$this->success = false;
		
			$vote = strtolower($vote);
			if (!($vote == "y" || $vote=="n")) { 
				$this->error_code = 500;
				$this->throwError("Invalid vote!");
				return null;
			}

			if (!$this->get('id')) {
				$this->error_code = 500;
				$this->throwError("Content has not been saved");
				return null;
			}

			$this->addFlag('vote',$this->POD->currentUser(),$vote);
			if (!$this->success()) { 
				return false;
			} else {
				$this->getVotes();
			}
			return true;
		}
		
		function unvote() {
			$val = $this->removeFlag('vote',$this->POD->currentUser());		
			if (!$this->success()) { 
				return $false;
			} else {
				$this->getVotes();
			}
			return $this;	
		}		
	
/* Helper functions */


	
	
		function permalink($field="headline",$return = false) {

			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array($field="headline",$return));
			}


			$string = "<a href=\"" . $this->get('permalink') . "\" title=\"" . htmlentities($this->get('headline')) . "\">" . $this->get($field) . "</a>";
			if ($return) {
				return $string;
			} else {
				echo $string;
			}
		}
		
		

		
		function authorisFriendsWith($person) {
			return $this->author()->isFriendsWith($person);
		}
			


		
	
		function stuffDoc() {
						
			$this->POD->tolog("content->stuffDoc " . $this->get('id'));

			
			if ($this->get('minutes')!='') { 
				$this->set('timesince', $this->POD->timesince($this->get('minutes')));
			}
			
			
			$tot = $this->get('yes_votes') + $this->get('no_votes');
			
			if ($tot > 0) { 
				$this->set('yes_percent',intval(($this->get('yes_votes') / $tot) * 100));
				$this->set('no_percent',intval(($this->get('no_votes') / $tot) * 100));
			} else {
				$this->set('yes_percent',0);
				$this->set('no_percent',0);
			}	
		

			
			$this->loadMeta();
			$this->generatePermalink();
		
		
	
				
		}
	
	
		function getVotes() {
			
			
			$this->success = false;
			if (!$this->get('id')) {
				$this->error_code = 500;
				$this->throwError("Content has not been saved");
				$this->POD->tolog("content->getVotes FAILED!");

				return null;
			}

			$this->POD->tolog("content->getVotes for doc " . $this->get('id'));
			$this->set('yes_votes',$this->flagCount('vote',"y"));
			$this->set('no_votes',$this->flagCount('vote',"n"));
	
			$tot = $this->get('yes_votes') + $this->get('no_votes');
			
			if ($tot > 0) { 
				$this->set('yes_percent',intval(($this->get('yes_votes') / $tot) * 100));
				$this->set('no_percent',intval(($this->get('no_votes') / $tot) * 100));
			} else {
				$this->set('yes_percent',0);
				$this->set('no_percent',0);
			}	
		
	
	// can't save, because security model won't let non-owners update!		
	//		$this->save();
	
			$sql = "UPDATE content SET yes_votes=" . $this->get('yes_votes') . ",no_votes=" . $this->get('no_votes') . " WHERE id=" . $this->get('id');
			$this->POD->tolog($sql,2);
			$res = mysql_query($sql,$this->POD->DATABASE);
			$this->success = true;


		
		}
	
	
		function generatePermalink() {			

			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array());
			}


			$path = $this->POD->libOptions('default_document_path');
			if ($this->POD->libOptions($this->get('type') . '_document_path')) { 
		 		$path = $this->POD->libOptions($this->get('type') . '_document_path');
			}
			
			$this->set('permalink',$this->POD->siteRoot(false) . "/$path/" . $this->get('stub'));

			$path = $this->POD->libOptions('default_document_editpath');
			
			if ($this->POD->libOptions($this->get('type') . '_document_editpath')) { 
		 		$path = $this->POD->libOptions($this->get('type') . '_document_editpath');
			}

			$this->set('editpath',$this->POD->siteRoot(false) . "/$path");
			$this->set('editlink',$this->POD->siteRoot(false) . "/$path?id=" . $this->get('id'));
			
			if ($this->POD->libOptions('content_permalink_' . $this->get('type'))) { 
				$this->set('permalink',$this->POD->siteRoot(false) . $this->tokenReplace($this->POD->libOptions('content_permalink_' . $this->get('type'))));
			}

			if ($this->POD->libOptions('content_editlink_' . $this->get('type'))) { 
				$this->set('editlink',$this->POD->siteRoot(false) . $this->tokenReplace($this->POD->libOptions('content_editlink_' . $this->get('type'))));
			}				
			if ($this->POD->libOptions('content_editpath_' . $this->get('type'))) { 
				$this->set('editpath',$this->POD->siteRoot(false) . $this->tokenReplace($this->POD->libOptions('content_editpath_' . $this->get('type'))));
			}				

		}
		
		
		
		function getTagIdByValue($value) {
			
			$t = new Tag($this->POD);
			$t->load('value',$value);
			if ($t->success()) {
				return $t->get('id');
			} else {
				$t->set('value',$value);
				$t->save();
				return $t->get('id');
			}
		}
		

/* Functions that output things */


		function render($template = 'output',$backup_path=null) {
		
			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array($template,$backup_path));
			}
		
			return parent::renderObj($template,array('content'=>$this,'doc'=>$this),'content',$backup_path);
	
		}
	
		function output($template = 'output',$backup_path=null) {
		
			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array($template,$backup_path));
			}

			parent::output($template,array('content'=>$this,'doc'=>$this),'content',$backup_path);
	
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
				if (@$options['select'] ) {
					self::$FIELD_PROCESSORS[$field . '_select'] = $options['select'];
				}
				if (@$options['insert'] ) {
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
				
		
	
	}
?>
