<? 
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* lib/Groups.php
* This file defines the Group object
*
* Documentation for this object can be found here:
* http://peoplepods.net/readme/group-object
/**********************************************/


class Group extends Obj {

	protected $ALLMEMBERS;	 // contains all invitees, applicants, etc
	protected $MEMBERS;	// just members
	protected $DOCUMENTS;	
	protected $FILES;

	// base database configuration for this object.
	static private $DEFAULT_FIELDS = array('id','groupname','description','stub','type','userId','date','changeDate');
	static private $IGNORE_FIELDS = array('permalink','minutes');
	static private $DEFAULT_JOINS = array(
						'o' => 'inner join users o on o.id=g.userId', // join to the group's owner
						'u' => 'left join groupMember mem on mem.groupId=g.id inner join users u on mem.userId=u.id', // link to group members
						'mem'=>'left join groupMember mem on mem.groupId=g.id', // link just to membership records
						'd'=> 'inner join content d on d.groupId=g.id', // link to content in this group
						't' => 'inner join tagRef tr on tr.itemId=g.id and tr.type="group" inner join tags t on tr.tagId=t.id', // link to tags
					);
					
	static private $FIELD_PROCESSORS = array();	
	static private $EXTRA_METHODS = array();
	
	function Group($POD,$PARAMETERS = null) {
		
		$this->success = null;
		parent::Obj($POD,'group',array(
			'table_name' => "groups",
			'table_shortname' => "g",
			'fields' => self::$DEFAULT_FIELDS,
			'ignore_fields'=>self::$IGNORE_FIELDS,				
			'joins' => self::$DEFAULT_JOINS,
			'field_processors'=>self::$FIELD_PROCESSORS		
		));	
	

		if (isset($PARAMETERS['id']) && (sizeof($PARAMETERS) == 1)) {
			$this->loadById($PARAMETERS['id']);
			if (!$this->success()) {
				return;
			}	
		} else if (isset($PARAMETERS['stub']) && (sizeof($PARAMETERS)==1)) {
			$this->loadByStub($PARAMETERS['stub']);
			if (!$this->success()) {
				return;
			}	
		} else {
			$fill = true;
			if (isset($PARAMETERS['id'])) {
				$d = $this->POD->checkcache('Group','id',$PARAMETERS['id']);
				if ($d) {
					$fill = false;
					$this->DATA = $d;
				} 
			}
			
			if ($PARAMETERS) { 
				// fill in the deets with the parameters passed in_array
				foreach ($PARAMETERS as $key=>$value) {
					$this->set($key,$value);
				}
			}
			if ($fill && $this->get('id')) { 
				$this->loadMeta();
			}
		}		
		
		$this->generatePermalink();			
		$this->POD->cachestore($this);

		$this->success = true;

	}

	function generatePermalink() {
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array());
		}

		$groupPath =  $this->POD->siteRoot(false) . "/" . $this->POD->libOptions('groupPath');
		$this->set('permalink',"$groupPath/" . $this->get('stub'));

	}


/*************************************************************************************	
* Files 																			
*************************************************************************************/


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
					$file->set('userId',$this->POD->currentUser()->id);
					$file->set('groupId',$this->id);
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
				$this->FILES = new Stack($this->POD,'file',array('groupId'=>$this->get('id')),null,$count,$offset,null,null,null,$this->id.'-group-files');
				if (!$this->FILES->success()) { 
					return new Stack($this->POD,'file');
				}
			}
			return $this->FILES;
		}



/*************************************************************************************	
* Accessors 																			
*************************************************************************************/

	function members() { 
		if (!$this->get('id')) {
			return null;
		}
		if (!$this->MEMBERS) { 
			$this->MEMBERS = $this->POD->getPeople(array('mem.type:!='=>'invitee','mem.groupId'=>$this->get('id')),'mem.date DESC',100,0,$this->id.'-group-members');
			if (!$this->MEMBERS->success()) {
				return null;
			}
		}
		
		return $this->MEMBERS;
	}
	
	function allmembers() { 
		if (!$this->get('id')) {
			return null;
		}
		if (!$this->ALLMEMBERS) { 
			$this->ALLMEMBERS = $this->POD->getPeople(array('mem.groupId'=>$this->get('id')),'mem.date DESC',100,0,$this->id.'-group-allmembers');
			if (!$this->ALLMEMBERS->success()) {
				return null;
			}
		}
		
		return $this->ALLMEMBERS;
	}
	function content() { 
		if (!$this->get('id')) {
			return null;
		}
		if (!$this->DOCUMENTS) { 
			$this->DOCUMENTS = new Stack($this->POD,'content',array('d.groupId'=>$this->get('id')));
			if (!$this->DOCUMENTS->success()) { 
				return null;
			}
		} 
		
		return $this->DOCUMENTS;
	}
	

/*************************************************************************************	
* CRUD 																			
*************************************************************************************/

	function loadById($id) {
		$d = $this->POD->checkcache('Group','id',$id);

		if ($d) {
			$this->DATA = $d;
			$this->generatePermalink();
			

		} else {
			$this->POD->tolog("I AM LOADING FROM THE DB A GROUP!");
			$this->load('id',$id);
			if ($this->success()) { 
				$this->loadMeta();
				$this->generatePermalink();
			}
		}
	}
	
		
	
	function loadByStub($stub) {


		$d = $this->POD->checkcache('Group','stub',$stub);

		if ($d) {
			$this->DATA = $d;
			$this->generatePermalink();
			

		} else {
			$this->load('stub',$stub);
			if ($this->success()) { 
				$this->loadMeta();
				$this->generatePermalink();		
			}
		}

	}
	
	
	function clearCaches() {
	
		$this->POD->cachefact($this->id.'-group-members',null);
	
	}


	
	function save() {
	
		$this->success = null;
		
		if (!$this->POD->isAuthenticated()) { 
			$this->success = false;
			$this->throwError("No current user! Can't save group!");
			$this->error_code = 500;
			return null;
		}			
		
		if ($this->get('id')) {
		// if we are updating this group, make sure this user has permission to do so!
			$membership = $this->isMember($this->POD->currentUser());
			if ($membership != 'owner' && $membership!='manager' && !$this->POD->currentUser()->get('adminUser')) {
				$this->success = false;
				$this->throwError("Access denied!  Only group owner or manager can create group!");
				$this->error_code = 401;	
				return null;				
			}
		} else {	
			$this->set('userId',$this->POD->currentUser()->get('id'));		
		}
	
	
		
		if ($this->get('groupname') && $this->get('description') && $this->get('userId')) {
		
			$this->set('groupname',stripslashes(strip_tags($this->get('groupname'))));
			$this->set('description',stripslashes(strip_tags($this->get('description'))));

			if (!$this->get('stub')) {
				$stub = $this->get('groupname');			
				$stub = preg_replace("/\s+/","-",$stub);
				$stub = preg_replace("/[^a-zA-Z0-9\-]/","",$stub);
				$stub = strtolower($stub);
				$this->set('stub',$stub);
			}

			$stub = $this->get('stub');			
			$newstub = $stub;
			
			// check and see if any documents already use this stub.
			$stubcheck = $this->POD->getGroup(array('stub'=>$stub));
			$counter = 2;
			while ($stubcheck->success() && $stubcheck->get('id')!=$this->get('id')) {
			
				$newstub = $stub . "_" . $counter++;
				$stubcheck = $this->POD->getGroup(array('stub'=>$newstub));				
			}
			
			$stub = $newstub;							
			$this->set('stub',$stub);

			if (!$this->saved()) { 
				$this->set('date','now()');
				$this->set('changeDate','now()');
			} else {
				$this->set('changeDate','now()');
			}

			
			parent::save();


			$this->generatePermalink();			

			$this->DOCUMENTS = new Stack($this->POD,'content',array('d.groupId'=>$this->get('id')));
			$this->MEMBERS = new Stack($this->POD,'user',array('mem.groupId'=>$this->get('id')),'mem.date DESC',20,0);			
			$this->addMember($this->POD->getPerson(array('id'=>$this->get('userId'))),'owner');

			$this->POD->cachestore($this);


			$this->success = true;
			return $this;
		} else {
			$this->success = null;
			$this->throwError("Missing required field");
			$this->error_code = 500;
			return null;			
		}
	
	}

	function delete($delete_documents = false) {
		$this->success = false;
		
		// only allow delete by the owner of this group!
		if ($this->POD->isAuthenticated() && (($this->isMember($this->POD->currentUser())=="owner") || $this->POD->currentUser()->get('adminUser'))) {
			
			if ($delete_documents) { 
				$this->content()->reset();
				while ($doc = $this->content()->getNext()) {
					$doc->delete();
				}
			} else {
				$this->content()->reset();
				while ($doc = $this->content()->getNext()) {
					$doc->set('groupId',null);
				}
				if ($this->get('type')=="private") {
					$sql = "UPDATE content SET privacy='owner_only' WHERE privacy='group_only' and groupId=" . $this->get('id');
					$this->POD->tolog($sql,2);
					mysql_query($sql,$this->POD->DATABASE);
				}

				$sql = "UPDATE content SET groupId=null WHERE groupId=" . $this->get('id');
				$this->POD->tolog($sql,2);
				mysql_query($sql,$this->POD->DATABASE);
				
			}
						
			mysql_query("DELETE FROM groupMember WHERE groupId=" . $this->get('id'),$this->POD->DATABASE);
			mysql_query("DELETE FROM invites WHERE groupId=" . $this->get('id'),$this->POD->DATABASE);			
			mysql_query("DELETE FROM meta WHERE type='group' and itemId=". $this->get('id'),$this->POD->DATABASE);
			mysql_query("DELETE FROM groups WHERE id=". $this->get('id'),$this->POD->DATABASE);

			$this->POD->cacheclear($this);

			
			$this->DATA = null;
			$this->success = true;
			return $this->success;
		} else {

			$this->success = false;
		
			$this->throwError("Access denied");
			$this->error_code = 401;
			return $this->success;
		}
	
	
	}
	

	function permalink($field='groupname',$return=false) {
	
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($field,$return));
		}


		$string = "<a href=\"" . $this->get('permalink') . "\" title=\"" . htmlentities($this->get('groupname')) . "\">" . $this->get($field) . "</a>";
		if ($return) { 
			return $string;
		} else {
			echo $string;
		}
	
	}






/*************************************************************************************	
* MEMBERS 																			
*************************************************************************************/

	
	function removeMember($person) {
		$this->success = null;
		
		$this->POD->tolog("group->removeMember()");
		if (!$this->POD->isAuthenticated()) { 
			$this->success = false;
			$this->throwError("No current user! Can't save group!");
			$this->error_code = 500;
			return null;
		}			

		if (!$person->get('id')) {
			$this->throwError("Person not saved yet!");
			$this->error_code = 500;
			return null;
		}
	
		if (!$this->get('id')) {
			$this->throwError("Group not saved yet!");
			$this->error_code = 500;
			return null;
		}

		$membership = $this->isMember($person);
		$my_membership = $this->isMember($this->POD->currentUser());
		
		if (($person->get('id') != $this->POD->currentUser()->get('id')) && ($my_membership != 'owner') && ($my_membership!='manager') && !$this->POD->currentUser()->get('adminUser')) {
			$this->success = false;
			$this->throwError("Access denied!  Only group owner or manager can remove someone from a group!");
			$this->error_code = 401;	
			return null;				
		}
	
		
		if ($membership == "owner") {
			$this->throwError("Group owner cannot quit!");
			$this->error_code = 401;
			$this->success = null;

			return null;
		} else {
			$sql = "DELETE FROM groupMember WHERE userId=" . $person->get('id') . " AND groupId=" . $this->get('id');
			$this->POD->tolog($sql,2);
			$res = mysql_query($sql);
			$this->success = true;
			$this->members()->fill();
			if (!$this->members()->success()) { 
				$this->throwError($this->members()->error());
				return null;
			}

			$fact = $person->get('id') . "-ismemberof-" . $this->get('id');
			$this->POD->cachefact($fact,false);

			$this->clearCaches();


			return true;
		}
	}
	
	
	function changeMemberType($person,$type) {
		$this->success = false;
		$this->POD->tolog("group->changeMemberType() $type");


		if (!$this->POD->isAuthenticated()) { 
			$this->success = false;
			$this->throwError("No current user! Can't save group!");
			$this->error_code = 500;
			return null;
		}

		$membership = $this->isMember($person);
		$my_membership = $this->isMember($this->POD->currentUser());

		if (!$membership) {
			$this->success = false;
			$this->throwError("Person is not member of this group.");
			$this->error_code = 500;
			return null;
		}

		if (($person->get('id') != $this->POD->currentUser()->get('id')) && ($my_membership != 'owner') && ($my_membership!='manager') && !$this->POD->currentUser()->get('adminUser')) {
			$this->success = false;
			$this->throwError("Access denied!  Only group owner or manager can change someone's member type.");
			$this->error_code = 401;	
			return null;				
		}

		if ($membership == "owner") {
			$this->throwError("Group owner can't be demoted!");
			$this->error_code = 401;
			$this->success = false;
			return null;
		}

		if (($type == "manager" || $type=="owner") && !($my_membership=="manager" || $my_membership=="owner")) {
			$this->throwError("Only a group owner or manager can promote members to manager or owner");
			$this->error_code = 401;
			$this->success = false;
			return null;		
		}

		$fact = $person->get('id') . "-ismemberof-" . $this->get('id');
		$this->POD->cachefact($fact,$type);
		
		$type = mysql_real_escape_string($type);
		$sql = "UPDATE groupMember SET type='$type',date=NOW() WHERE userId=" . $person->get('id') . " AND groupId=" . $this->get('id');
		$this->POD->tolog($sql,2);
		$res = mysql_query($sql,$this->POD->DATABASE);
		$num = mysql_affected_rows($this->POD->DATABASE);
		if ($num < 1 || !$res) {
			$this->success = false;
			$this->throwError("SQL Error: GroupMember Update failed!");
			$this->error_code = 500;
			return null;
		}
		$this->success = true;
		return $type;
	
	}
	
	function addMember($person,$type='member',$invited = null) {
		$this->success = null;

		$this->POD->tolog("group->addMember()");
	
		if (!$this->POD->isAuthenticated()) { 
			$this->success = false;
			$this->throwError("No current user! Can't add a member to a group!");
			$this->error_code = 500;
			return null;
		}
		
		if (!$person->get('id')) {
			$this->throwError("Person not saved yet!");
			$this->error_code = 500;
			return null;
		}
	
		if (!$this->get('id')) {
			$this->throwError("Group not saved yet!");
			$this->error_code = 500;
			return null;
		}


// FIX THIS
		$allowed = false;
		
		$my_membership = $this->isMember($this->POD->currentUser());
		$new_member_membership = $this->isMember($person);
		
		$reason = '';
		
		// can add invite - is member, owner or manager
		if ($type=='invite') { 
			if ($my_membership=='member') {
				$allowed = true;
			} else {
				$reason = 'Only members can invite others';
			}
		}

		// can add applicant - is not member and group is private
		if ($type=='applicant') { 
			if ($this->type=='private' && !$new_member_membership) {
				$allowed = true;
			} else {
				$reason = 'You cannot apply to this group';
			}
		}
		
		// can add member - is owner or manager, or is current user
		if ($type=='member') {
			if ($this->POD->currentUser()->id==$person->id) {

				if ($this->type=='private' && $new_member_membership=='invitee') {
					$allowed = true;
				} else if ($this->type!='private') {
					$allowed= true;
				} else {
					$reason = 'You must be invited to join this group';
				}

			} else {
				$reason = 'Only group managers are allowed to add other people';
			}		
		}

		// if I am an admin, or a moderator, i can add whoever i want at whatever leve.
		if (in_array($my_membership,array("manager","owner")) || $this->POD->currentUser()->id==$this->userId || $this->POD->currentUser()->adminUser) {
			$allowed = true;
		}

		if ($type==$new_member_membership) {
			$allowed = true;
		}

		if (!$allowed) {
			$this->throwError($reason);
			$this->success = false;
			return null;
		}
				
		if (!$this->isMember($person)) {
			$this->POD->tolog("group->addMember() adding member");
			$sql = "INSERT INTO groupMember (groupId,userId,type,date) values (" . $this->get('id') . "," . $person->get('id') . ",'" . $type . "',NOW());";
			$this->POD->tolog($sql,2);
			$result = mysql_query($sql);
			$num = mysql_affected_rows($this->POD->DATABASE);
			if ($num < 1 || !$result) {
				$this->success = false;
				$this->throwError("SQL Error: GroupMember Insert failed!");
				$this->error_code = 500;
				return null;
			}
			$this->members()->add($person);
			if (!$this->members()->success()) { 
				$this->throwError($this->members()->error());
				return null;
			}
			$fact = $person->get('id') . "-ismemberof-" . $this->get('id');
			$this->POD->cachefact($fact,$type);

		} else if ($this->isMember($person)!=$type) {
			$this->changeMemberType($person,$type);
		} else {
			$this->POD->tolog("group->addMember() already a member!");
		}

		$this->clearCaches();
		
		$this->success = true;

		return $type;
	}
	
	function isMember($person) {
		$this->success = false;
		$this->error = null;
		
		if (!$person || !$person->get('id')) {
			// this doesn't necessarily mean an error has happened
			// maybe the user isn't authenticated...
			//$this->throwError("Person not saved yet!");
			//$this->error_code = 500;
			return null;
		}
	
		if (!$this->get('id')) {
			$this->throwError("Group not saved yet!");
			$this->error_code = 500;
			return null;
		}
		
		$fact = $person->get('id') . "-ismemberof-" . $this->get('id');
		
		if ($val = $this->POD->factcache($fact)) { 
			$this->success = true;
			return $val;
		}
		
			$sql = "SELECT type FROM groupMember WHERE userId=" . $person->get('id') . " and groupId= " . $this->get('id');
			$this->POD->tolog($sql,2);
			$res = mysql_query($sql,$this->POD->DATABASE);	
			$num = mysql_num_rows($res);
			$this->success = true;	

			if ($num > 0) {
				$g = mysql_fetch_assoc($res);
				$this->POD->cachefact($fact,$g['type']);
				return $g['type'];
			} else {
				$this->POD->cachefact($fact,false);
				return null;
			}
	}
	

/*************************************************************************************	
* Content 																			
*************************************************************************************/
	
	function addContent($doc) {
		$this->success = null;
		
		if (!$doc->get('id')) {
			$this->throwError("Content not saved yet!");
			$this->error_code = 500;
			return null;
		}
		if (!$doc->isEditable()) {
			$this->throwError("Access Denied! Not authenticated");
			$this->error_code = 401;
			return null;		
		}
	
		if (!$this->get('id')) {
			$this->throwError("Group not saved yet!");
			$this->error_code = 500;
			return null;
		}
		
		if (!$this->POD->isAuthenticated()) { 
			$this->throwError("Access Denied! Not authenticated");
			$this->error_code = 401;
			return null;		
		}

		$membership = $this->isMember($this->POD->currentUser());
		$this->success = false;
		if (!$membership && !$this->POD->currentUser()->get('adminUser')) {
			$this->throwError("Access Denied! Not a member");
			$this->error_code = 401;
			return null;		
		}
			
		if ($doc->get('groupId') && $doc->get('groupId') != $this->get('id')) {
			$this->throwError("Content already belongs to a group!");
			$this->error_code=401;
			return null;
		}
		
		if ($doc->get('groupId') == $this->get('id')) {
			// already in the group
			$this->success = true;
			return true;
		}
		
		// set the group.  don't change privacy here.  (so if a public content is added to a private group, it remains public.)
		$doc->setGroup($this->get('id'));
		if (!$doc->success()) {
			$this->throwError($doc->error());
			$this->error_code = $doc->errorCode();
			return null;
		}
		
		$this->success = true;	
		$this->content()->add($doc);
		return $doc;
	}
	
	function removeContent($doc) {
		$this->success = false;
		if (!$doc->get('id')) {
			$this->throwError("Content not saved yet!");
			$this->error_code = 500;
			return null;
		}
	
		if (!$this->get('id')) {
			$this->throwError("Group not saved yet!");
			$this->error_code = 500;
			return null;
		}
		if ($doc->get('groupId') && $doc->get('groupId') != $this->get('id')) {
			$this->throwError("Content doesn't belong to this group!");
			$this->error_code=401;
			return null;
		}

		if (!$doc->get('groupId')) {
			$this->success = true;
			return true;		
		}
		
		if ($doc->get('groupId') == $this->get('id')) {
			
			if ($doc->get('privacy') == "group_only") {
				$doc->set('privacy','owner_only');
			}
			$doc->setGroup(null);
			if (!$doc->success()) {
				$this->throwError($doc->error());
				$this->error_code = $doc->errorCode();
				return null;
			}
		
			$this->success = true;	
			$this->content()->fill();
			return $doc;
		}
	}

/* Functions that output things */

		function render($template = 'output',$backup_path=null) {


			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array($template,$backup_path));
			}
		
			return parent::renderObj($template,array('group'=>$this),'groups',$backup_path);
	
		}
	
		function output($template = 'output',$backup_path=null) {
		
			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array($template,$backup_path));
			}

			parent::output($template,array('group'=>$this),'groups',$backup_path);
	
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
}

?>