<? 
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* lib/Tag.php
* Handles lists of Tag Objects
*
* Documentation for this object can be found here:
* http://peoplepods.net/readme/tag-object
/**********************************************/

	require_once("Obj.php");
	class Tag extends Obj {
	
		static private $EXTRA_METHODS = array();

		function Tag($POD,$PARAMETERS=null) {
	
			parent::Obj($POD,'tag');
			if (!$this->success()) {
				return $this;
			}
			
			
			if ($PARAMETERS) { 
				// create based on parameters
				foreach ($PARAMETERS as $key=>$value) {
					$this->set($key,$value);
				}
			}		
		}
		
		function render($template = 'tag',$backup_path=null) {
		
			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array($template,$backup_path));
			}
			return parent::renderObj($template,array('tag'=>$this),'content',$backup_path);
	
		}
	
		function output($template = 'tag',$backup_path=null) {
			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array($template,$backup_path));
			}
		
			parent::output($template,array('tag'=>$this),'content',$backup_path);
	
		}
	
		
		function contentCount() { 
			
			$docs = $this->POD->getContents(array('t.id'=>$this->get('id')));
			return $docs->totalCount();
		
		}
	
		
		function save() {
			$this->success = false;
			if ($this->get('value')) { 	
				if (!$this->saved()) {
					$this->set('date','now()');
				}
				parent::save();
			} else {
				$this->throwError("No value!");
				$this->error_code = 500;				
			}
			return $this;	
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
	
?>