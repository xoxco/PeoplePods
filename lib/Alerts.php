<? 
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* lib/Alerts.php
* This file defines the Alerts object
* Handles alerts sent to the user by 
*
* Documentation for this object can be found here:
* http://peoplepods.net/readme/alerts
/**********************************************/	

	
	class Alert extends Msg { 
	
		static private $EXTRA_METHODS = array();


		function Alert($POD,$PARAMETERS=null) { 
			parent::Msg($POD,'alert',array(
					'table_name'=>'alerts',
					'table_shortname'=>'x',
					'fields'=>array('id','date','userId','targetUserId','targetContentId','targetContentType','message','status'),
					'ignore_fields'=>array('minutes'),
					'joins'=>array(
						'u'=>'inner join users u on u.id=x.userId', // link to alert creator
						'g'=>'left join groups g on g.id=x.targetContentId and targetContentType=\'group\'', // link to target group if one exists
						'c'=>'left join comments c on c.id=x.targetContentId and targetContentType=\'comment\'', // link to target group if one exists
						'd'=>'left join content d on d.id=x.targetContentId and targetContentType=\'content\'', // link to target content if one exists
						'tu'=>'inner join users u on u.id=x.targetUserId', // link to alerts target
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

	
		// send an alert to a user
		// $userId is the user to send the alert to
		// $message is the singular message "Ben added a comment"
		// $targetContent is the id of the content affected. "[Ben] did something to [Dakota]'s [content]"
		function publish($userId,$message,$actingUser=null,$targetContent=null,$ok_to_send_email=true) {
		
			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array($userId,$message,$actingUser,$targetContent,$ok_to_send_email));
			}

			$this->targetUserId = $userId;

			if ($actingUser) { 
				$this->userId = $actingUser->id;
			}
			$this->message = $message;

			if ($targetContent) { 
				$this->targetContentId = $targetContent->id;
				$this->targetContentType = $targetContent->TYPE;

			}
			
			$this->status = 'new';


			$this->save();				
			
			if ($ok_to_send_email===true && $this->POD->libOptions('alertEmail')) { 
				$this->targetUser()->sendEmail('alert',array('message'=>$this->formatMessage()));
			}
	
		}
		
		
		// handle database stuff
		function save() {

			$this->success = false;
			$this->POD->tolog("alert->save()");			
			

			if (!$this->POD->isAuthenticated()) { 
				$this->throwError("No current user! Can't save alert!");
				return null;
			}
			if (!$this->targetUserId) { 
				$this->throwError("Missing required field 'targetUserId'! Can't save alert!");
			}
	
			if (!$this->message) { 
				$this->throwError("Missing required field 'message'! Can't save alert!");
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

	
			$targetUser = $targetContent  = null;	
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
			
			return parent::renderObj($template,array('alert'=>$this,'targetUser'=>$targetUser,'targetContent'=>$targetContent),'alerts',$backup_path);
	
		}
	
		function output($template = 'output',$backup_path=null) {
	
			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array($template,$backup_path));
			}
	
			$targetUser = $targetContent  = null;	
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
		
			parent::output($template,array('alert'=>$this,'targetUser'=>$targetUser,'targetContent'=>$targetContent),'alerts',$backup_path);
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