<? 
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* lib/Comment.php
* This file defines the Comment object
*
* Documentation for this object can be found here:
* http://peoplepods.net/readme/comment-object
/**********************************************/

	require_once("Obj.php");
	class Comment extends Obj {
	
		// base database configuration for this object.
		static private $DEFAULT_FIELDS = array('id','contentId','profileId','userId','date','comment','type');
		static private $IGNORE_FIELDS = array('permalink','minutes');
		static private $DEFAULT_JOINS =  array(
						'd'=>'inner join content d on c.contentId=d.id', // link to a comment's content
						'u'=>'inner join users u on u.id=c.userId', // link to a comment's user
						'a'=>'inner join users a on a.id=c.userId', // link to a comment's user
						't' => 'inner join tagRef tr on tr.itemId=c.id and tr.type="comment" inner join tags t on tr.tagId=t.id', // link to tags

					);
						
		static private $FIELD_PROCESSORS = array();			

		static private $EXTRA_METHODS = array();
	

		function Comment($POD,$PARAMETERS=null) {
	
			parent::Obj($POD,'comment',array(
				'table_name' => "comments",
				'table_shortname' => "c",
				'fields' => self::$DEFAULT_FIELDS,
				'ignore_fields'=>self::$IGNORE_FIELDS,				
				'joins' => self::$DEFAULT_JOINS,
				'field_processors'=>self::$FIELD_PROCESSORS		
			));
			if (!$this->success()) {
				return null;
			}
			
			if (isset($PARAMETERS['id']) && (sizeof($PARAMETERS)==1)) {
				if ($d = $POD->checkcache('Comment','id',$PARAMETERS['id'])) {
					$this->DATA = $d;				
				} else {
					$this->load('id',$PARAMETERS['id']);
					$this->generatePermalink();
					$this->loadMeta();
				}
				$POD->cachestore($this);
				return $this;
			} else if ($PARAMETERS) {
				// create based on parameters
				foreach ($PARAMETERS as $key=>$value) {
					if ($key != 'POD') {
						$this->set($key,$value);
					}
				}
			}
			$this->generatePermalink();
			$this->loadMeta();
			$POD->cachestore($this);

			$this->success = true;
			return $this;
		}
		
		
		function generatePermalink() { 
			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array());
			}


			if ($this->saved()) { 
				if ($this->parent()) { 
					$v = $this->parent()->permalink . '#' . $this->id;
				} else {
					$v = '#';
				}
				$this->set('permalink',$v);
			} else {
				$this->set('permalink',null);
			}
		}
		
		
		function parent($field = null) {
			if (!$this->PARENT) {
				if ($this->contentId!='') { 
					$this->PARENT = $this->POD->getContent(array('id'=>$this->get('contentId')));
				} else {				
					$this->PARENT = $this->POD->getPerson(array('id'=>$this->get('profileId')));
				}
				if (!$this->PARENT->success()) {
					return null;
				} 			
			}
			if (isset($field)) {
				return $this->PARENT->get($field);
			} else {
				return $this->PARENT;
			}
		}
		
				
		
		function delete() {
			$this->success = false;
			if (!$this->POD->isAuthenticated()) { 
				$this->error_code = 401;
				$this->throwError("Permission Denied");
				return null;
			}
			if (!$this->get('id')) {
				$this->error_code = 500;
				$this->throwError("Comment not saved yet.");			
				return null;
			}
			if (($this->get('userId') != $this->POD->currentUser()->get('id')) && ($this->parent('userId') != $this->POD->currentUser()->get('id')) && (!$this->POD->currentUser()->get('adminUser'))) { 
			// the only people who can delete a comment are the commenter, the owner of the content commented upon, or an admin user
			// if this person is none of those people, fail!
				$this->error_code = 401;
				$this->throwError("Permission Denied");
				return null;
			}



			
			$id = $this->get('id');

			mysql_query("DELETE FROM meta WHERE type='comment' and itemId=$id",$this->POD->DATABASE);	

			mysql_query("DELETE FROM activity WHERE (targetContentId=$id and targetContentType='comment') or (resultContentId=$id and resultContentType='comment');",$this->POD->DATABASE);		

			mysql_query("DELETE FROM alerts WHERE (targetContentId=$id and targetContentType='comment')",$this->POD->DATABASE);		
			
			$sql = "DELETE FROM comments WHERE id=$id";
			$this->POD->tolog($sql,2);
			mysql_query($sql,$this->POD->DATABASE);

			$this->clearCaches();
						
			$this->POD->cacheclear($this);
		
			$this->DATA = array();
			$this->success = true;
			return true;
		
		}
		
		
		function clearCaches() {
			// clear cache of comments for parent object.
		
			if ($this->get('contentId')) { 
				$this->POD->cachefact($this->contentId.'-content-comments',null);			
				$this->POD->cachefact($this->contentId.'-content-comments_totalcount',null);			
			} else if ($this->get('profileId')) { 
				$this->POD->cachefact($this->contentId.'-person-comments',null);					
				$this->POD->cachefact($this->contentId.'-person-comments_totalcount',null);					

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
				
		
/*********************************************************************************************
* Accessors
*********************************************************************************************/
	
		
		function save() {
			$this->success = false;

			if (!$this->get('contentId') && !$this->get('profileId')) {
				$this->throwError("Could not save comment. Required field contentId or profileId missing.");
				$this->error_code = 500;
				return;
			}
			if (!$this->get('comment')) {
				$this->throwError("Could not save comment. Required field comment missing.");
				$this->error_code = 500;
				return;
			}
			if (!$this->get('userId')) {
				$this->throwError("Could not save comment. Required field userId missing.");
				$this->error_code = 500;
				return;
			}
	
			$this->clearCaches();

			// strip everything but basic tags out of the comment field.
			$this->set('comment',strip_tags(stripslashes($this->get('comment')),'<p><em><strong><a><b><i><br>'));
			
			if (!$this->saved()) { 
				$this->set('date','now()');
				$this->set('minutes',0);

			}

			parent::save();			

			$this->generatePermalink();
			$this->POD->cachestore($this);
			

			return $this;
			
		}

/* Functions that output things */

		function render($template = 'comment',$backup_path=null) {
		
			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array($template,$backup_path));
			}


			return parent::renderObj($template,array('comment'=>$this),'content',$backup_path);
	
		}
	
		function output($template = 'comment',$backup_path=null) {
		
			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array($template,$backup_path));
			}

			parent::output($template,array('comment'=>$this),'content',$backup_path);
	
		}
	
		
	}
	
?>