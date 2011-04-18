<?
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* lib/Stack.php
* Handles lists of PeoplePods Objects
*
* Documentation for this object can be found here:
* http://peoplepods.net/readme/stacks
/**********************************************/	
	class Stack implements Iterator {
		public $POD;
		public $TYPE = "Generic";

		protected $success = false;
		protected $error;
		protected $error_code;

		private $full = false;
		private $position = 0;
		private $COUNT = 0;
		private $TOTAL_COUNT = null;
		private $STORE = array();
		private $STACK = array();
		private $baseConditions;
		private $baseSort;
		private $baseCount;
		private $baseOffset;
		private $baseSelect;
		private $baseFrom;
		private $baseGroup;
		private $OVERFLOW = 0;
		private $CACHE_KEY = null;

		static private $EXTRA_METHODS = array();


		function Stack($POD,$type,$baseConditions=null,$sort=null,$count=20,$offset=0,$from=null,$select=null,$groupBy=null,$cacheAs=null) {
		
			$this->position = 0;
		
			if ($count == null || $count == '') {  $count = 20; }
			if ($offset == null || $offset == '') { $offset = 0; }
			if (!is_numeric($offset)) {
				$this->throwError("Could not instantiate stack: Invalid Offset value! $offset");
				$this->error_code = 501;
				return;
			}
			if (!is_numeric($count)) {
				$this->throwError("Could not instantiate stack: Invalid Count value! $count");
				$this->error_code = 501;
				return;
			}
			
			if ($cacheAs) { 
				$this->CACHE_KEY = $cacheAs;
			}

			if ($POD && $type) { 
				$this->POD = $POD;
				$this->TYPE = $type;
				$this->baseConditions = $baseConditions;
				$this->baseSort = $sort;
				$this->baseCount = $count;
				$this->baseOffset = $offset;
				$this->baseFrom = $from;
				$this->baseGroup = $groupBy;
				
				if ($this->TYPE=='content' && isset($baseConditions)) {
					$this->baseConditions['d.hidden']='0';
				}

				if (isset($baseConditions)) { 
					if (is_array($baseConditions)) { 
					//	$bc = implode(",",$baseConditions);
						$bc = "";
						foreach ($baseConditions as $key=>$value) { 
							$bc .= "$key=$value,";
						}
					} else {
						$bc = $baseConditions;
					}
				} else {
					$bc = "NO CONDITIONS";
				}
				$this->POD->tolog("stack->new: $type offset=$offset count=$count WHERE=$bc");
				
				$this->baseSelect = $select;
				$this->success = true;
			} else {
				$this->throwError("Could not instantiate stack: Missing POD or type");
				$this->error_code = 501;
				return;
			}	


	


		}

		function errorCode() { 
			return $this->error_code;
		}

		function throwError($error) {
			$this->error = $error;
			error_log("Error [{$this->TYPE}]: $this->error");		
		}		
		function error() {	
			return $this->error;
		}	
		function success() { 
			return $this->success;
		}
		
		function asArray() { 

			if (!$this->full) {
				$this->fill();
			}

			$data = array();
			$this->reset();
			while ($x = $this->getNext()) { 
				array_push($data,$x->asArray());
			}
			$this->reset();
			return $data;
		
		}
		
		
		function combineWith($stack) { 
		
			$stack->reset();
			while ($obj = $stack->getNext()) { 
				$this->add($obj);
			}
	
		}
		
		function suboutput($template,$title,$empty_message,$additional_parameters,$templateDir,$backup_path) { 
		
			$POD = $this->POD;
			if (file_exists("{$templateDir}/$template.php")) {
				include("{$templateDir}/$template.php");
			} else if (file_exists("{$backup_path}/$template.php")) {
				include("{$backup_path}/$template.php");
			} else {
				$this->throwError("Tried to use template {$templateDir}/stacks/$template but could not find it.");
			}
		
		}		
		
		function output($alt_template=null,$header='header',$footer='footer',$title=null,$empty_message=null,$additional_parameters=null,$backup_path=null) {
			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array($alt_template,$header='header',$footer='footer',$title,$empty_message,$additional_parameters,$backup_path));
			}

			$templateDir = $this->POD->libOptions('templateDir') . '/stacks';
		
			$this->reset();
			$count = 1;
			if ($header) { 
				$this->suboutput($header,$title,$empty_message,$additional_parameters,$templateDir,$backup_path);
			}
			while ($x = $this->getNext()) {
				if ($count % 9 == 0) { $x->set('isNinthItem',1,false); }
				if ($count % 8 == 0) { $x->set('isEigthItem',1,false); }
				if ($count % 7 == 0) { $x->set('isSeventhItem',1,false); }
				if ($count % 6 == 0) { $x->set('isSixthItem',1,false); } 
				if ($count % 5 == 0) { $x->set('isFifthItem',1,false); }
				if ($count % 4 == 0) { $x->set('isFourthItem',1,false);}
				if ($count % 3 == 0) { $x->set('isThirdItem',1,false); }
				
				if ($count % 2 == 0) { $x->set('isEvenItem',1,false); $x->set('isOddItem',null,false); }
				if ($count % 2 == 1) { $x->set('isOddItem',1,false); $x->set('isEvenItem',null,false); }
				if ($count==1) {
					$x->set('isFirstItem',1,false);
				}
				if (!$this->peekAhead()) { 
					$x->set('isLastItem',1,false);
				}

				$x->set('listCount',$count,false);

				$x->output($alt_template,$backup_path);
				$count++;
			}
			if ($footer) { 
				$this->suboutput($footer,$title,$empty_message,$additional_parameters,$templateDir,$backup_path);
			}
		
		}

		function full() { return $this->full; }
		
		function serializeparameters($conditions,$sort,$count,$offset,$from,$select,$groupBy) { 
	
			$string = '';
			foreach ($conditions as $key=>$value) {
				$string .= "&$key=>$value";
			}
			$string .= "&sort=$sort&count=$count&offset=$offset&from=$from&select=$select&groupBy=$groupBy";
			return md5($string);
		
		}

		function setCount($newcount) { 
		
			$this->baseCount = $newcount;
		}

		function fill($conditions=null,$sort=null,$count=null,$offset=null,$from=null,$select=null,$groupBy=null) {
			
			$this->success = false;

			$this->full = true; // set this to true even though it may fail. this way, we don't fail over and over and over.
			$this->STORE = array();
			if (!$conditions && $this->baseConditions) {
				$conditions = $this->baseConditions;
			}
			
			if ($sort == null) { $sort = $this->baseSort; }
			if ($count == null) { $count = $this->baseCount; }
			if ($offset == null) { $offset = $this->baseOffset; }
			if ($select == null) { $select = $this->baseSelect; }
			if ($from == null) { $from = $this->baseFrom; }
			if ($groupBy==null) { $groupBy= $this->baseGroup; }
			
	
			if ($conditions) {

	
				if ($this->TYPE == "comment") {			
						$x = "Comment";
					} else if ($this->TYPE == "tag") {			
						$x = "Tag"; 
					} else if ($this->TYPE == "content") {
						$x = "Content";
					} else if ($this->TYPE == "user") {
						$x = "Person";
					} else if ($this->TYPE == "group") {
						$x = "Group";
					} else if ($this->TYPE=="messages") { 
						$x = "Message";
						$sort = "date DESC";
					} else if ($this->TYPE=="threads") { 
						$x = "Thread";
					} else if ($this->TYPE == "file") { 
						$x = "File";
					} else if ($this->TYPE == "activity") { 
						$x = "Activity";
					} else if ($this->TYPE == "alert") { 
						$x = "Alert";
					}
	
				$resultcount = 0;
				if ($this->CACHE_KEY) {
					$list = $this->POD->factcache($this->CACHE_KEY);
				}
			
				if ($offset == 0 && $this->CACHE_KEY && (is_array($list)) && $this->POD->libOptions('stack_caching_enabled')) { 
					$this->POD->tolog("Loading cached stack of " . $this->TYPE);
					// load from cache this list
					foreach ($list as $id) { 
						$this->POD->tolog("Loading from cache " . $this->TYPE . " id $id");
						$new = new $x($this->POD,array('id'=>$id));
						if ($new->success()) {
							$this->add($new);
							$resultcount++;
						}
					}
				
				} else {

					// do some database magic.
	
					$glue = " AND ";

				
					$q = new $x($this->POD);
	
					$this->POD->tolog("stack->fill(): " . $this->TYPE);				
					$rows = $q->query($conditions,$sort,$count,$offset,$from,$select,$glue,$groupBy);
					$done = false;
					$overflow = false;
					while (!$done) {
						if ($rows) {
							$rowcount = sizeof($rows);
							foreach ($rows as $row) {
								if ($resultcount < $count) { 
									$new = new $x($this->POD,$row);
								
									if ($new->success()) {
										$this->add($new);
										$resultcount++;
									} else {
										$this->OVERFLOW++;
										$this->throwError("stack->fill(): Error creating new obj: " . $new->error());
									}
								}
								
								$this->success = true;
	
							}
							if (($resultcount < $rowcount) && ($rowcount == $count) && ($rowcount > 0)) {
								// if we got fewer items than we wanted, that means some got excluded for security reasons.
								// we need to requery to fill out the list!
								$this->POD->tolog("requerying...off: $offset rows returned: $rowcount  results $resultcount  max $count,  new offset=" . ($offset + $rowcount));
								$overflow = true;
								$offset += $rowcount;
								$rows = $q->query($conditions,$sort,$count,$offset,$from,$select,$glue);		
							} else if ($resultcount >= $rowcount) {
								$this->POD->tolog("Done!");
								// if we got enough, we're done!
								$done = true;	
							} else if (($resultcount < $rowcount) && ($rowcount < $count)) { 
								// if we didn't get enough, but we're out of options, we're done!
								$this->POD->tolog("done!");
								$done = true;
							}
						}  else { 
							$done = true;
						}					
					}
					
					if ($this->CACHE_KEY && $offset==0 && $this->POD->libOptions('stack_caching_enabled')) { 
						$this->POD->tolog("Caching stack of " . $this->TYPE);
						$this->POD->cacheFact($this->CACHE_KEY,$this->extract('id'));
					}
				} // if not using cache

														
				$this->POD->tolog("stack->fill(): found " . $resultcount . " items with " . $this->OVERFLOW ." overflow");
				$this->reset();
			} else {
				$this->POD->tolog("stack->fill(): Creating an empty stack");		
			}
			
			return $this;
		}

		function exists() {
			if (!$this->full) {
				$this->fill();
			}
			
			if ($this->count() > 0) {
				return true;
			} else {
				return false;
			}
			
		}
	
	
		function contains($field,$value) {
		// check to see if the stack contains a record with a specified field and value pair
		
			$this->reset();
			while ($x = $this->getNext()) {
				if ($x->get($field)==$value) {
					$this->reset();
					return $x;
				}
			}
			$this->reset();
			return null;		
		}	
		
		function extract($field) {
			$this->reset();
			$return = array();
			while ($o = $this->getNext()) {
				array_push($return,$o->get($field));
			}
			
			$this->reset();
			return $return;			
		}

		
		function implode($delim,$field) {
			$string = "";
			$this->reset();
			$first = true;
			while ($o = $this->getNext()) {
				if (!$first) {
					$string .= $delim;
				}
				$string .= $o->get($field);
				$first = false;
			}
			
			$this->reset();
			return $string;
			
		}
		
		function add($obj) {
//			$this->full=true;
			if (!$this->contains('id',$obj->get('id'))) {
				$this->POD->tolog("stack->add() adding to stack " . $this->TYPE);
				array_push($this->STORE,$obj);
			}
			$this->reset();
			$this->success = true;
		}


		function peekAhead() {
			if (!$this->full) {
				$this->POD->tolog("stack->peekAhead() Stack of type " . $this->TYPE . " not found, automagically loading!");
				$this->fill();
			}
			if (isset($this->STACK[0])) { 
				return $this->STACK[0];
			} else {
				return null;
			}
		}
			
		
		function getNext($reverse = null) { 
			if (!$this->full) {
				$this->POD->tolog("stack->getNext() Stack of type " . $this->TYPE . " not found, automagically loading!");
				$this->fill();
			}
			$this->position++;
			if ($reverse) { 
				return array_pop($this->STACK);
			} else {
				return array_shift($this->STACK);					
			}				
		}
		
		function reset() {
			if (!$this->full) { $this->fill(); }
			$this->position = 0;
			$this->STACK = $this->STORE;
		}
		
		
		
		/* Iterator functions */
		
		function rewind() { 
			$this->reset();
		}
		
		function current() { 
			return $this->STACK[0];
		}
		
		function key() {
			return $this->position;
		}
		
		function next() { 
			$this->getNext();
		}
		
		function valid() { 
			return isset($this->STACK[0]);
		}
		
			
		
		/* Counting and paging functions */	
		
		function hasNextPage() { 
			
			$this->POD->tolog("hasNextPage? " .$this->totalCount()  . " > " . $this->nextPage() );
			if ($this->totalCount() > $this->nextPage()) { 
				return true;
			} else {
				return false;
			}
		
		}


		function hasPreviousPage() { 
			
			if ($this->offset() > 0) { 
				return true;
			} else {
				return false;
			}
		
		}
		
		function previousPage() { 
	
			// FIX THIS
			// if we had to remove some documents from the previous page, the offset may not match up.
			$ret = $this->offset() - ($this->baseCount + $this->OVERFLOW);
			if ($ret < 0) { $ret = 0; }
			return $ret;
			
		}
		function nextPage() { 
			return $this->offset() + $this->OVERFLOW + $this->count();
		}
		
		function getNextPage() { 
			$this->baseOffset = $this->nextPage();
			$this->fill();
			return $this;
		}

		function getOtherPage($offset) { 
			$this->baseOffset = $offset;
			$this->fill();
			return $this;
		}

		
		function offset() { 
			return $this->baseOffset;
		}
		
		function count() {
			if (!$this->full) { $this->fill(); }
			$this->COUNT = sizeof($this->STORE);
			$this->POD->tolog("stack->count() " . $this->TYPE . ' ' . $this->COUNT);
			return $this->COUNT;			
		}
		
		
		function totalCount() {
		
			if ($this->CACHE_KEY != null) {
				$count = $this->POD->factcache($this->CACHE_KEY . '_totalcount');
				if (isset($count)) {
					$this->TOTAL_COUNT = $count;
					return $count;
				}
			}
		
		
			if ($this->TOTAL_COUNT == null) { 
				
				$conditions = $this->baseConditions;
				$glue = " AND ";
	
				if ($conditions) {
	
					if ($this->TYPE == "comment") {			
						$x = "Comment";
					} else if ($this->TYPE == "tag") {			
						$x = "Tag"; 
					} else if ($this->TYPE == "content") {
						$x = "Content";
					} else if ($this->TYPE == "user") {
						$x = "Person";
					} else if ($this->TYPE == "group") {
						$x = "Group";
					} else if ($this->TYPE=="messages") { 
						$x = "Message";
						$sort = "date ASC";
					} else if ($this->TYPE=="threads") { 
						$x = "Thread";
						$select = "SELECT count(distinct(targetUserId)) as totalCount ";
						$sort = "ORDER BY date DESC";
					} else if ($this->TYPE == "file") { 
						$x = "File";
					} else if ($this->TYPE == "activity") { 
						$x = "Activity";
					} else if ($this->TYPE == "alert") { 
						$x = "Alert";
					}
		
					
					$q = new $x($this->POD);

					$select = "SELECT count(distinct(" . $q->table_shortname() . ".id)) AS totalCount ";			
					$from = $this->baseFrom;
					$sort = $this->baseSort;
	
	
					$this->POD->tolog("stack->totalCount() Getting total count for stack");				

					$rows = $q->query($conditions,$sort,99999999,0,$from,$select,$glue);
					if ($rows) {
						$row = array_shift($rows);
						$this->TOTAL_COUNT = $row['totalCount'];
					} else {
						$this->TOTAL_COUNT = 0;
					}
				} else {
					return $this->count();
				}
			} 

			if ($this->CACHE_KEY) {
				$this->POD->cachefact($this->CACHE_KEY . '_totalcount',$this->TOTAL_COUNT);
			}

			return $this->TOTAL_COUNT;
		
		}

		function shuffle() { 
		
			shuffle($this->STORE);
			$this->reset();			
		
		}				


		function sortBy($sort,$reverse = null) {
			if (!$this->full) { $this->fill(); }
		
			if ($this->count() > 0) {
			foreach ($this->STORE as $key => $row) {
   				$sorter[$key]  = $row->get($sort);
   			}
   		
   			if ($reverse) { 
	   			array_multisort($sorter, SORT_ASC, $this->STORE);	
	   		} else { 		
	   			array_multisort($sorter, SORT_DESC, $this->STORE);	
	   		}
			$this->reset();
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