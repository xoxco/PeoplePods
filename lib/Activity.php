<? 
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* lib/Activity.php
* This file defines the Activity object
* Handles news feed/activity feeds
*
* Documentation for this object can be found here:
* http://peoplepods.net/readme/activity
/**********************************************/	

	
	class Activity extends Msg { 
	
		static private $EXTRA_METHODS = array();


		function Activity($POD,$PARAMETERS=null) { 
			parent::Msg($POD,'activity',array(
					'table_name'=>'activity',
					'table_shortname'=>'a',
					'fields'=>array('id','date','userId','targetUserId','targetContentId','targetContentType','resultContentId','resultContentType','message','userMessage','targetMessage','gid'),
					'ignore_fields'=>array('minutes'),
					'joins'=>array(
						'u'=>'inner join users u on u.id=a.userId', // link to activity stream's owner
						'g'=>'left join groups g on g.id=a.targetContentId and targetContentType=\'group\'', // link to target group if one exists
						'c'=>'left join comments c on c.id=a.targetContentId and targetContentType=\'comment\'', // link to target group if one exists
						'd'=>'left join content d on d.id=a.targetContentId and targetContentType=\'content\'', // link to target content if one exists
						'tu'=>'left join users tu on tu.id=a.target and targetType=\'user\'',  // link to target user if one exists
						'rg'=>'left join groups rg on rg.id=a.resultContentId and resultContentType=\'group\'', // link to target group if one exists
						'rc'=>'left join comments rc on rc.id=a.resultContentId and resultContentType=\'comment\'', // link to target group if one exists
						'rd'=>'left join content rd on rd.id=a.resultContentId and resultContentType=\'content\'', // link to target content if one exists
					)
				)
			);
			
			if (!$this->success()) {
				return $this;
			}
		
			// load item by id or accept params
			if (isset($PARAMETERS['gid']) && sizeof($PARAMETERS)==1) { 
				$this->load('gid',$PARAMETERS['gid']);
			} else if (isset($PARAMETERS['id']) && sizeof($PARAMETERS)==1) {
				$this->load('id',$PARAMETERS['id']);
			} else if ($PARAMETERS) { 
				foreach ($PARAMETERS as $key=>$val) {
					$this->set($key,$val);
				}
			}
			
			return $this;
		
		}

	
		// publish a simple news item
		// $message is the singular message "Ben added a comment"
		// $userId is the acting user.  "[Ben] did something."
		// $targetUserId is the user who the action was taken upon.  "[Ben] did something to [Dakota]"
		// $targetContentId is the id of the content affected. "[Ben] did something to [Dakota]'s [content]"
		// $resultContentId is the id of the content created.  "[Ben] left a [comment] on [Dakota]'s [content]"
		// $gid is an optional parameter that defines this action as a unique and non-repeatable action
		// (for example, Ben added Katie as a friend.  we don't want that to shop up multiple times!)
		function publish($userId,$message,$userMessage,$targetMessage,$targetUser=null,$targetContent=null,$resultContent=null,$gid=null) {
		
			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array($userId,$message,$userMessage,$targetMessage,$targetUser,$targetContent,$resultContent,$gid));
			}

			if ($gid) { 	
				$act = $this->POD->getActivity(array('gid'=>$gid));
				if ($act->success()) { 
					return $act;
				}
			}
			
			$this->userId = $userId;
			$this->message = $message;
			if ($userMessage) { 
				$this->userMessage = $userMessage;
			} else {
				$this->userMessage = $message;
			}
			if ($targetMessage) { 
				$this->targetMessage = $targetMessage;
			} else {
				$this->targetMessage = $message;
			}

			if ($targetUser) { 
				$this->targetUserId = $targetUser->id;
			}
			if ($targetContent) { 
				$this->targetContentId = $targetContent->id;
				$this->targetContentType = $targetContent->TYPE;

			}
			if ($resultContent) { 
				$this->resultContentId= $resultContent->id;
				$this->resultContentType = $resultContent->TYPE;	
			}
			$this->gid = $gid;

			$this->save();				
	
		}
		
		
		// handle database stuff
		function save() {

			$this->success = false;
			$this->POD->tolog("activity->save()");			

			if (!$this->POD->isAuthenticated()) { 
				$this->throwError("No current user! Can't save activity!");
				return null;
			}
			if (!$this->userId) { 
				$this->throwError("Missing required field 'userId'! Can't save activity!");
			}
	
			if (!$this->message) { 
				$this->throwError("Missing required field 'message'! Can't save activity!");
			}
		
			if (!$this->saved()) { 
				$this->set('date','now()');
			}
			
			parent::save();		
		
		}
	


		function render($template = 'output',$backup_path=null) {

			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array($template,$backup_path));
			}

	
			$targetUser = $targetContent = $resultContent = null;	
			if ($this->targetUserId) { 
				$targetUser = $this->POD->getPerson(array('id'=>$this->targetUserId));
			}

			if ($this->targetContentId) { 
				if ($this->targetContentType=='content') { 
					$targetContent = $this->POD->getContent(array('id'=>$this->targetContentId));
				} else if ($this->targetContentType=='comment') { 
					$targetContent = $this->POD->getComment(array('id'=>$this->targetContentId));				
				} 
			}
			if ($this->resultContentId) {
				if ($this->resultContentType=='content') { 
					$resultContent = $this->POD->getContent(array('id'=>$this->resultContentId));
				} else if ($this->resultContentType=='comment') { 
					$resultContent = $this->POD->getComment(array('id'=>$this->resultContentId));				
				} 
			}			
			
			return parent::renderObj($template,array('activity'=>$this,'targetUser'=>$targetUser,'targetContent'=>$targetContent,'resultContent'=>$resultContent),'activity',$backup_path);
	
		}
	
		function output($template = 'output',$backup_path=null) {
	
			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array($template,$backup_path));
			}
	
			$targetUser = $targetContent = $resultContent = null;
				
			if ($this->targetUserId) { 
				$targetUser = $this->POD->getPerson(array('id'=>$this->targetUserId));
			}

			if ($this->targetContentId) { 
				if ($this->targetContentType=='content') { 
					$targetContent = $this->POD->getContent(array('id'=>$this->targetContentId));
				} else if ($this->targetContentType=='comment') { 
					$targetContent = $this->POD->getComment(array('id'=>$this->targetContentId));				
				} 
			}
			if ($this->resultContentId) {
				if ($this->resultContentType=='content') { 
					$resultContent = $this->POD->getContent(array('id'=>$this->resultContentId));
				} else if ($this->resultContentType=='comment') { 
					$resultContent = $this->POD->getComment(array('id'=>$this->resultContentId));				
				} 
			}
			
		
			parent::output($template,array('activity'=>$this,'targetUser'=>$targetUser,'targetContent'=>$targetContent,'resultContent'=>$resultContent),'activity',$backup_path);
		}
	
		function hasMethod($method) { 
			return (isset(self::$EXTRA_METHODS[$method]));		
		}
		
		function override($method,$args) { 
		    if (isset(self::$EXTRA_METHODS[$method])) {
		      array_unshift($args, $this);
		      return call_user_func_array(self::$EXTRA_METHODS[$method], $args);
		    } else {
		    	$this->throwError('Unable to find execute overridden method: ' . $method);
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

1;
?>