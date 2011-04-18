<?


/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* lib/Messages.php
* On-site messaging, including email-style and im-style messages
* Getting and putting messages
* Inbox management
*
* Documentation for this object can be found here:
* http://peoplepods.net/readme/messaging
/**********************************************/



require_once("Stack.php");

	Class Inbox extends Stack {

		protected $UNREAD_COUNT = 0;
			
		function Inbox($POD,$count = 20, $offset = 0) { 
		
			$this->POD = $POD;
			
			if (!$this->POD) { return false; }
			if (!$this->POD->isAuthenticated()) { return false; }


			// get unread count.
			$sql = "SELECT count(1) as count FROM messages WHERE userId=" . $this->POD->currentUser()->get('id') . " and status='new';";
			$this->POD->tolog($sql,2);
			$res = mysql_query($sql,$this->POD->DATABASE);
			if ($ur = mysql_fetch_assoc($res)) {
				$this->UNREAD_COUNT = $ur['count'];
			}
			mysql_free_result($res);

			
			$conditions = array();
			$conditions['userId'] = $this->POD->currentUser()->get('id');
			$sort = 'GROUP by targetUserId ORDER BY max(date) DESC';
			$tables = 'FROM messages m';
			$select = 'SELECT m.targetUserId as id, m.userId as ownerId,m.targetUserId,max(m.date) as latestMessage,(TIME_TO_SEC(TIMEDIFF(NOW(),max(date))) / 60) as minutes';
		
			parent::Stack($POD,'threads',$conditions,$sort,$count,$offset,$tables,$select);
			return $this;

			
		}

		function unreadCount() { 
			return $this->UNREAD_COUNT;
	
		}
	
		function newThread($friendId) { 
			return new Thread($this->POD,array('id'=>$friendId,'ownerId'=>$this->POD->currentUser()->get('id'),'targetUserId'=>$friendId));
		}
	

	
	
	}


	Class Thread extends Obj {
		
		public $MESSAGES;
		public $RECIPIENT;
		protected $UNREAD_COUNT = 0;
		
		function Thread($POD,$threadInfo=null) {

				
			parent::Obj($POD,'thread');
			if (!$this->success()) {
				return $this;
			}	 		

			if (isset($threadInfo)) { 
				foreach ($threadInfo as $key => $value) {
					$this->set($key,$value);
				}
			
				$this->RECIPIENT = $this->POD->getPerson(array('id'=>$this->get('targetUserId')));
	
					
				// get unread count.
				$sql = "SELECT count(1) as count FROM messages WHERE userId=" . $this->get('ownerId') ." AND targetUserId=" . $this->get('targetUserId') . " and status='new';";
				$this->POD->tolog($sql,2);
				$res = mysql_query($sql,$this->POD->DATABASE);
				if ($ur = mysql_fetch_assoc($res)) {
					$this->UNREAD_COUNT = $ur['count'];
				}
				mysql_free_result($res);
				
				$this->set('permalink',$this->POD->siteRoot(false) . $this->POD->libOptions('messagePath') . "/" . $this->RECIPIENT->get('stub'));		
				$this->MESSAGES = new Stack($this->POD,'messages',array('userId'=>$this->get('ownerId'),'targetUserId'=>$this->get('targetUserId')),null,1000);			
			}
			$this->success = true;
			return $this;			
					
		}
		
		
		function messages() { 
		
			return $this->MESSAGES;
			
		}
		
		function recipient() {
		
			return $this->RECIPIENT;
			
		}
		function unreadCount() {
			return $this->UNREAD_COUNT;
		}
	
		function markAsRead() {
		
			$this->MESSAGES->reset();
			while ($message = $this->MESSAGES->getNext()) {
				$message->set('status','read');
				$message->save();
			}
			$this->UNREAD_COUNT = 0;
			
			
		}

		function render($template = 'thread',$backup_path=null) {
		
			return parent::renderObj($template,array('thread'=>$this),'messages',$backup_path);
	
		}
	
		function output($template = 'thread',$backup_path=null) {
		
			parent::output($template,array('thread'=>$this),'messages',$backup_path);
	
		}


		function reply($message) {
			$this->success = null;
			
			$msg = new Message($this->POD,array('targetUserId'=>$this->RECIPIENT->id,'message'=>$message));
			$msg->save();
			if ($msg->success()) { 
				$this->MESSAGES->exists();
				$this->MESSAGES->add($msg);
				$this->success = true;
				return $msg;		
			} else {
				$this->throwError($msg->error());
				$this->error_code = $msg->errorCode();
				return null;
			}	
		}
	
		function clear() {
			$this->success = null;
			while ($message = $this->MESSAGES->getNext()) { 
				$message->delete();
				if (!$message->success()) {
					$this->throwError($message->error());
					$this->error_code = $message->errorCode();
					return null;
				}
			}
			$this->MESSAGES->fill();
			$this->success = true;
		}
	
	
	}
	
	

Class Message extends Msg {
	static private $EXTRA_METHODS = array();

	function Message($POD,$PARAMETERS=null) { 
		parent::Msg($POD,'message',array(
				'table_name'=>'messages',
				'table_shortname'=>'m',
				'fields'=>array('id','userId','targetUserId','fromId','message','date','status'),
				'ignore_fields'=>array('permalink','minutes'),
				'joins'=>array(
					'from'=>'inner join users from on m.fromId=from.id',
					'to'=>'inner join users to on m.targetUserId=to.id and m.fromId!=m.targetUserId'
				)
			)
		);
		
		if (isset($PARAMETERS['id']) && (sizeof($PARAMETERS)==2)) { 
			// load by ID
			$this->load('id',$PARAMETERS['id']);							
		} else if ($PARAMETERS) {
			foreach ($PARAMETERS as $key=>$value) {
				if ($key != 'POD') {
					$this->set($key,$value);
				}
			}
		}


		return $this;
	}
	
	function from() {
		return $this->POD->getPerson(array('id'=>$this->fromId));
	}
	function to() {
		if ($this->fromId==$this->userId) { 
			return $this->POD->getPerson(array('id'=>$this->targetUserId));
		} else {
			return $this->POD->getPerson(array('id'=>$this->userId));		
		}
	}

	// save() 
	// send a message
	function save($ok_to_send_email=true) {
	
		$this->success = false;
		if (!$this->POD->isAuthenticated()) {
			$this->throwError("Access Denied");
			$this->error_code = 401;
			return null;
		}
		
		if ($this->get('message') == "" || $this->get('targetUserId') == "") {
			$this->throwError("Fields missing!");
			$this->error_code = 500;
			return null;
		}


	
//		$this->set('message',strip_tags($this->get('message')));
		
		if (!$this->saved()) { 
			
			$this->set('fromId',$this->POD->currentUser()->get('id'));
			
			$from = $this->get('fromId');
			$to = $this->get('targetUserId');
					
			// for messages, we need to insert two near duplicate rows,
			// one for the sender and one for the recipient
			// we can do this by just swapping the values.
			
			// first create the recipient

			$this->set('userId',$to);
			$this->set('targetUserId',$from);
			$this->set('status','new');
			$this->set('date','now()');

			parent::save();
			
			// now create the sender version
			
			$this->set('userId',$from);
			$this->set('targetUserId',$to);
			$this->set('status','read');
	  
			$this->set('id',null);

			parent::save();
			
			if ($ok_to_send_email===true && $this->POD->libOptions('contactEmail')) { 
				$this->from()->sendEmail("contact",array('to'=>$this->to()->get('email'),'message'=>$this->get('message')));
			}
		 	
		} else {
		
			parent::save();			
		}	 	

 		return $this;
	 	
	}
	
	// reply to a message
	function reply() {
		$this->success = null;
		
		$msg = new Message($this->POD,array('targetUserId'=>$this->fromId,'message'=>$message));
		$msg->save();
		if ($msg->success()) { 
			$this->MESSAGES->exists();
			$this->MESSAGES->add($msg);
			$this->success = true;
			return $msg;		
		} else {
			$this->throwError($msg->error());
			$this->error_code = $msg->errorCode();
			return null;
		}	
	}


	function output($template = 'message',$backup_path=null) {
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($template,$backup_path));
		}
		parent::output($template,array('message'=>$this),'messages',$backup_path);

	}	


 	function render($template = 'message',$backup_path=null) {
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($template,$backup_path));
		}
	
		return parent::render($template,array('message'=>$this),'messages',$backup_path);

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

	
