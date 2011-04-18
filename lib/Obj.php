<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* lib/Obj.php
* This file defines the base object
* Creates the parent object that all other peoplepods objects are based on
*
* Documentation for this object can be found here:
* http://peoplepods.net/readme/obj
/**********************************************/	

	class Obj {
	
		public $POD;	
		public $TYPE;
		protected $success = false;
		protected $error;
		protected $error_code;

		protected $AUTHOR; // userId
		protected $PARENT; //documentId
		protected $CREATOR; // creatorId

		protected $TAGS;

		
		protected $table_name;
		protected $table_shortname;
		
		public $DATA = array();
		private $FIELDS = array();
		private $TEMPFIELDS = array();
		private $JOINS = array();
		private $UNSAVED_META = array();
		
		private $FIELD_PROCESSORS = array();
		static private $EXTRA_METHODS = array();
				
		function Obj($POD,$type,$object_definition=null) { 
			if ($POD && $type) { 
				$this->POD = $POD;
				$this->TYPE = $type;
				$this->success = true;
			} else {
				$this->error = "Could not instantiate Obj: Missing POD or type";
				$this->error_code = 1;
			}
			
			if (!isset($object_definition)) { 
								
				if ($this->TYPE == "tag") {
	
					$this->table_name = "tags";
					$this->table_shortname = "t";	
					$this->FIELDS = array('id','value','weight','date');
					$this->TEMPFIELDS = array('permalink','minutes');
					
					$this->JOINS = array( 
						'd' => 'inner join tagRef tr on tr.tagId=t.id inner join content d on tr.itemId=d.id and tr.type="content"', // link to the tagged content				
						'u' => 'inner join tagRef tr on tr.tagId=t.id inner join users u on tr.itemId=u.id and tr.type="user"', // link to the tagged content				
						'c' => 'inner join tagRef tr on tr.tagId=t.id inner join comments c on tr.itemId=c.id and tr.type="comment"', // link to the tagged content				
						'g' => 'inner join tagRef tr on tr.tagId=t.id inner join groups g on tr.itemId=g.id and tr.type="group"', // link to the tagged content				
						'f' => 'inner join tagRef tr on tr.tagId=t.id inner join files f on tr.itemId=d.id and tr.type="file"', // link to the tagged content				

						'tr' => 'inner join tagRef tr on tr.tagId=t.id', // link to the tagged content				
	
					);
								
				} else if ($this->TYPE=="thread") { 
					$this->table_name="messages";
					$this->table_shortname="m";
					$this->FIELDS = array('userId');
					$this->TEMPFIELDS = array ('targetUserId','id','friendId','ownerId','latestMessage','minutes','permalink');
				}
			} else {
				// load in a dynamic object definition! Whoooo!
				$this->table_name = $object_definition['table_name'];
				$this->table_shortname = $object_definition['table_shortname'];
				$this->FIELDS = $object_definition['fields'];
				$this->TEMPFIELDS = $object_definition['ignore_fields'];
				$this->JOINS = $object_definition['joins'];
				$this->FIELD_PROCESSORS = @$object_definition['field_processors'];

			}	


			return $this;
		}
		
		
		function author($field = null) {
			if (!$this->get('userId')) { 
				return null;
			}
			if (!$this->AUTHOR) {
				$this->AUTHOR = $this->POD->getPerson(array('id'=>$this->get('userId')));
			}
			
			if ($field != null) {
				return $this->AUTHOR->get($field);
			} else {
				return $this->AUTHOR;
			}
		}

		function owner($field = null) { // synonym for author
			return $this->author($field);
		}

		function creator($field = null) {
					
			$id = $this->get('createdBy');
			if (!$id) { 
				$id = $this->get('ownerId');
				if (!$id) { 
					$id = $this->get('userId');
				}
			}
		
			if (!$this->CREATOR) {
				$this->CREATOR = $this->POD->getPerson(array('id'=>$id));
			}
			
			if ($field != null) {
				return $this->CREATOR->get($field);
			} else {
				return $this->CREATOR;
			}
		}


		function parent($field = null) {
			if (!$this->get('parentId') && !$this->get('contentId')) { 
				return null;
			}
			if (!$this->PARENT) {
				if ($this->TYPE == "content") { 
					$this->PARENT = $this->POD->getContent(array('id'=>$this->get('parentId')));
				} else {				
					$this->PARENT = $this->POD->getContent(array('id'=>$this->get('contentId')));
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


	
/* Basic IO functions */	


		function output($template = 'output',$variables,$sub_folder,$backup_path=null) {
			echo $this->renderObj($template,$variables,$sub_folder,$backup_path);
		}

		function renderObj($template = 'output',$variables,$sub_folder,$backup_path=null) {
			$POD = $this->POD;
	
			$cache_name = $this->TYPE . "-" . $this->get('id') . "-" . $template;
		
			if (!$POD->isAuthenticated() && !$POD->cacheHasExpired($cache_name) && $this->get('id')) { 
				return $POD->loadCache($cache_name);
			} 
			
			$POD->startBuffer();
	
			// set up variables that need to be available in the template
			extract($variables);

			
			if (file_exists($POD->libOptions('templateDir') . "/{$sub_folder}/{$template}.php")) {	
				include($POD->libOptions('templateDir') . "/{$sub_folder}/{$template}.php");
			} else if (file_exists($backup_path . "/{$template}.php")){
				include($backup_path . "/{$template}.php");		
			} else {
				echo "[PeoplePods Template Error: Could not find template file \"{$template}\"]";
			}
	
			$html = $POD->endBuffer();
			if (!$POD->isAuthenticated() && $this->get('id')) {
				$POD->writeCache($cache_name,$html);
			}
			return $html;
	
		}

		function set($field,$value=null,$cache=true) {
			if ($this->isRealField($field)) { 
				$this->DATA[$field] = $value;
			} else if ($this->isTempField($field)) { 
				$this->DATA[$field] = $value;
				// this is a field that has a temporary value
				// and doesn't need to be saved to the db
			} else {
				if ($cache) { 
					$this->addMeta($field,$value);
				} else {
					$this->DATA[$field] = $value;
				}
			}
			if ($cache) { 
				$this->POD->cachestore($this);
			}
		}
	
			
		function get($field) {
			if (isset($this->DATA[$field])) {
				return $this->DATA[$field];
			} else {
				return null;
			}
		}
		
		function isRealField($field) { 
			return (in_array($field,$this->FIELDS));
		}
		function isTempField($field) { 
			return (in_array($field,$this->TEMPFIELDS));
		}
		
		
		/* MAGIC METHODS! */
		
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
		
		
		function __get($field) { 
			return $this->get($field);
		}
		
		function __set($field,$value) { 
			return $this->set($field,$value);
		}
		
		function __isset($field) { 
			return isset($this->DATA[$field]);
		}
		
		function __unset($field) { 
			if (in_array($field,$this->FIELDS)) { 
			 	unset($this->DATA[$field]);
			} else {
				return $this->removeMeta($field);
			}
		}



		function htmlspecialwrite($field) { 
			echo htmlspecialchars(($this->get($field)),ENT_QUOTES);
		}

		function shorten($field,$length) {
			return $this->POD->shorten($this->get($field),$length);
		}

		function write($field) {
			echo $this->get($field);
		}
	
		function writeFormatted($field) {
			echo $this->formatText($field);
		}


		function throwError($error) {
			$this->error = $error;
			error_log("Error [{$this->TYPE}]: $this->error");		
		}
		

		function errorCode() { 
			return $this->error_code;
		}
		
		function error() {	
			return $this->error;
		}
	
		function success($val = null) { 
			if (isset($val)) { 
				$this->success = $val;
			}
			return $this->success;
		}



		function get_short($field,$chars = 25) {
	
			$text = $this->get($field);
			$text = strip_tags($text);
	        $text = $text." ";	
	        $text = substr($text,0,$chars);
	        $text = substr($text,0,strrpos($text,' '));
	        if (strlen($text) < strlen($this->get($field))) {
		        $text = $text."...";
			}        
	        return $text;
	
	    }


		function formatText($field,$add_p_tags=true) {
			return $this->POD->formatText($this->get($field),$add_p_tags);
		}

		function asArray() {
		
			return $this->DATA;
		
		}


/*************************************************************************************	
* TAGS 																			
*************************************************************************************/

		
		function tags() {		
			if (!$this->get('id')) {
				return null;
			}
			if (!$this->TAGS) { 
				$this->TAGS = $this->POD->getTags(array('tr.itemId'=>$this->get('id'),'tr.type'=>$this->TYPE),null,100);
				if (!$this->TAGS->success()) { 
					return null;
				}
			}
			return $this->TAGS;
		}

		function hasTag($tagvalue) {
			
			return $this->tags()->contains('value',$tagvalue);		
		
		}

		function removeTag($tag) {
		
			$this->success = false;
			if ($this->get('id')) { 
				$t = new Tag($this->POD);
				$t->load('value',$tag);
				if ($t->success()) {
					$sql = "DELETE FROM tagRef WHERE itemId=" . $this->get('id') . " AND type='" . $this->TYPE . "'  AND tagId=" . $t->get('id') . ';';
					$this->POD->tolog($sql,2);	
					mysql_query($sql,$this->POD->DATABASE);		
					$this->success = true;
					$this->tags()->fill();
					return $t;
				}else {
					$this->throwError("Tag not found");
					$this->error_code = 404;
				}		
			
			} else {
				$this->throwError("Object not saved yet!");
				$this->error_code = 500;
			}
			
			return null;
		
		}

		function addTag($tag) {
				
			$this->success = false;
			if ($this->get('id')) {
				$t = new Tag($this->POD);
				$t->load('value',$tag);
				if (!$t->success()) {
					$this->POD->tolog("content->addTag: Adding tag $tag");
					$t->set('value',$tag);
					$t->save();
				}
				
				$sql = "DELETE FROM tagRef WHERE itemId=" . $this->get('id') . " AND type='" . $this->TYPE . "' AND tagId=" . $t->get('id') . ';';
				$this->POD->tolog($sql,2);
				mysql_query($sql,$this->POD->DATABASE);		
				
				$sql = "INSERT INTO tagRef(itemId,tagId,type) VALUES (" . $this->get('id') . "," . $t->get('id') . ",'" . $this->TYPE . "');";
				$this->POD->tolog($sql,2);
				mysql_query($sql,$this->POD->DATABASE);		
				
				$this->tags()->add($t);
				$this->success = true;
				return $t;
			} else {
				$this->throwError("Object not saved yet!");
				$this->error_code = 500;
				return null;
			}
		}


		function tagsFromString($string,$delimiter=' ') {
		
			$tags = explode($delimiter,$string);

			$this->tags()->reset();
			$current_tags = new Stack($this->POD,'tags');
			foreach ($this->tags() as $tag) { 
				$current_tags->add($tag);
			}

			foreach ($current_tags as $tag) { 
				$this->removeTag($tag->get('value'));
			}

	
			foreach ($tags as $tag) {
				if ($tag != '') {
					$this->addTag($tag);
				}
			}
		
		}
			
		function tagsAsString($delimeter=' ') {
			if ($this->tags()) { 
				return $this->tags()->implode($delimeter,'value');
			} else {
				return null;
			}	
		}			


/* SQL Functions */


		function save() {
			$this->success = false;
		
			$sql = '';
			
			if (!$this->saved()) { 
				$sql .= "INSERT INTO " . $this->table_name . " SET ";	
			} else {
				$sql .= "UPDATE " . $this->table_name . " SET ";
			}
			
			$fields = array();
			foreach ($this->FIELDS as $field) { 
				if ($field == "id") { continue; } 

				$value = $this->get($field);				

				if (isset($this->FIELD_PROCESSORS[$field . "_insert"])) { 
					$func = $this->FIELD_PROCESSORS[$field."_insert"];
					$fields = array_merge($fields,$func($this,$field,$value));
				} else {
	
					if ($value=="now()") { 
						$fields[] = "$field=now()";
					} else if ($value=="" || $value==null) {
						$fields[] = "$field=null";
					} else {
						$value = mysql_real_escape_string($this->POD->handleUTF8($value));
						$fields[] = "$field='" . $value ."'";	
					}
				}
			}
			
			$sql .= implode(",",$fields);
			
			if ($this->saved()) { 
				$sql .= " WHERE id=" . $this->get('id');
			}
		
			$this->POD->tolog($sql,2);
			$result = mysql_query($sql,$this->POD->DATABASE);
			if (!$result) {
				$this->success = false;
				$this->throwError("SQL Error in Query: " . mysql_error() . " QUERY: $sql");
				$this->error_code = 500;
				return null;
			}
			
			if (!$this->saved()) { 
				$id = mysql_insert_id($this->POD->DATABASE);
				$this->set('id',$id);
			
				if (sizeof($this->UNSAVED_META) > 0) { 
					foreach ($this->UNSAVED_META as $field=>$value) { 
						$this->addMeta($field,$value);
					}
					$this->UNSAVED_META = array();
				}				
				
				
				
			}
			
			foreach ($this->FIELDS as $field) { 

				$value = $this->get($field);
				if ($value=="now()") {
					$this->set($field,date("Y-m-d H:i:s",time()));
				}
			}			
			$this->success = true;
		
		
		}

		function saved() {
			if ($this->get('id') == null || $this->get('id')=='') { 
				return false;
			} else {
				return true;
			}
		}

	
		function generateWhere($conditions,$glue = ' AND ') { 
		
			$sql = "";
			$stack = array();
			if (!is_array($conditions)) { 
				return;
			}
			foreach ($conditions as $field => $value) {
			
				$operator = "=";
				$computed_field = null;
				
				if (preg_match("/(.*?)\,(.*)/",$field,$matches)) { 
					$field = $matches[1];
					$computed_field = $matches[2];
					if (preg_match("/(.*?)\:(.*)/",$computed_field,$matches)) { 
						$computed_field = $matches[1];
						$field .= ":" . $matches[2];
					}
				} 
				
				if (preg_match("/(.*?)\:(.*)/",$field,$matches)) { 
					$field = $matches[1];
					switch ($matches[2]) {
					
						case "gt":
							$operator = ">";
							break;
						case "lt":
							$operator = "<";
							break;
						case "gte":
							$operator = ">=";
							break;
						case "lte":
							$operator = "<=";
							break;
						case "not":
							$operator = "!=";
							break;
						case "!=":
							$operator = "!=";
							break;
						case "like":
							$operator = "like";
							break;
						case "fulltext":
							$operator = "fulltext";			
					}		
				}			
				
				if (!$computed_field) {
					$computed_field = $field;
				}
	
				if (preg_match("/^or(\d+)?/",$field)) {
				
					array_push($stack,' (' . $this->generateWhere($value,' OR ') . ') ');	
		
				} else if (preg_match("/^and(\d+)?/",$field)) {

					array_push($stack,' (' . $this->generateWhere($value,' AND ') . ') ');	
				} else if (preg_match("/^\!or(\d+)?/",$field)) {
				
					array_push($stack,' !(' . $this->generateWhere($value,' OR ') . ') ');	
		
				} else if (preg_match("/^\!and(\d+)?/",$field)) {

					array_push($stack,' !(' . $this->generateWhere($value,' AND ') . ') ');	

				} else if (is_array($value)) {
					
					if ($operator == "=") {
						$ministack = array();
						foreach ($value as $v) {
							if (!is_numeric($v)) { 
								array_push($ministack,"'".mysql_real_escape_string($v)."'");
							} else {
								array_push($ministack,mysql_real_escape_string($v));
							}
						}
						array_push($stack," $computed_field in (" . implode(",",$ministack) . ") ");
					} else if ($operator == "!=") {
						$ministack = array();
						foreach ($value as $v) {
							if (!is_numeric($v)) { 
								array_push($ministack,"'".mysql_real_escape_string($v)."'");
							} else {
								array_push($ministack,mysql_real_escape_string($v));
							}
						}
						array_push($stack," $computed_field not in (" . implode(",",$ministack) . ") ");
					} else if ($operator == "like") {
						$ministack = array();
						foreach ($value as $v) {
							array_push($ministack,"($computed_field like '$v')");
						}
						array_push($stack,"(" . implode(" OR ",$ministack) . ")");
					
					} else {
						$v = mysql_real_escape_string(array_shift($value));
						if (strtolower($v) == "null" && $operator=="=") {
							array_push($stack,"$computed_field IS NULL");
						} else if (strtolower($v) == "null" && $operator=="!=") {
							array_push($stack,"$computed_field IS NOT NULL");
						} else {
							array_push($stack," $computed_field $operator '$v' ");
						}
					}
				} else {
			
						$v = mysql_real_escape_string($value);
						if (strtolower($v) == "null" && $operator=="=") {
							array_push($stack,"$computed_field IS NULL");
						} else if (strtolower($v) == "null" && $operator=="!=") {
							array_push($stack,"$computed_field IS NOT NULL");
						} else if ($operator == "fulltext") { 
							array_push($stack,"match ($computed_field) against ('$v' in boolean mode)");
						} else {
							if (!is_numeric($v)) { 
								array_push($stack," $computed_field $operator '$v' ");
							} else {	
								array_push($stack," $computed_field $operator $v ");
							}
						}
			
				}
			
			
			}
			
			
			$sql = implode($glue,$stack);
			return $sql;
		}
	
	
		function referenceObject($shortname) { 
		
			switch($shortname) {
			
				case "u": return new Person($this->POD);
				case "d": return new Content($this->POD);
				case "g": return new Group($this->POD);
				case "c": return new Comment($this->POD);
				case "t": return new Tag($this->POD);
				case "f": return new File($this->POD);
				case "o": return new Person($this->POD);
				case "tu": return new Person($this->POD);
			
			}
					
		}
				
		function generateFrom(&$from,&$conditions,$join_type='LEFT',$existing_joins=null) { 
			
			# $joins will contain a list of all the necessary joins which will be combined at the end to generate the FROM
			$joins = array();

			// setup a default $from
			if(!$from) { 
				$from = "FROM " . $this->table_name . " " . $this->table_shortname;
			}
			
			$original_conditions = $conditions;
			foreach ($original_conditions as $field=>$value) { 
			
				// conditions come in the format
				// table_alias.field:operator
				
				$linked_table = null;
				$operator = null;				
				$computed_field = null;
	
				if (preg_match("/^(.*?)\,(.*)/",$field,$matches)) { 
					$field = $matches[1];
					$computed_field = $matches[2];				
					if (preg_match("/(.*?)\:(.*)/",$computed_field,$matches)) { 
						$computed_field = $matches[1];
						$field .= ":" . $matches[2];
					}
				}
				
				// split table alias from fieldname
				if (preg_match("/^(.*?)\.(.*)/",$field,$matches)) { 
					$linked_table = $matches[1];
					$field = $matches[2];
					$operator =  null;
					if (preg_match("/(.*?)\:(.*)/",$field,$matches)) { 
						$field = $matches[1];
						$operator = ":" . $matches[2];
					}						
				} else if (preg_match("/^(.*?):(.*)/",$field,$matches)) {
					$linked_table = $this->table_shortname;
					$field = $matches[1];
					$operator = ":".$matches[2];
				} else {
					// no operator
					$linked_table = $this->table_shortname;
				}
				
				if (!$computed_field) {
					$computed_field = $field;
				}
				
				
				// now we know what field we want
				// what table the field is in
				// and what operator we're going to call on it.		

				//echo "Handling $field from $linked_table<br />";
								
				// handle sub-clauses
				if (preg_match("/^or(\d+)?/i",$field)) { 
					// handled later
				} else if (preg_match("/^and(\d+)?/i",$field)) {
					// handled later
				} else if (preg_match("/^\!or(\d+)?/i",$field)) { 
					// handled later
				} else if (preg_match("/^\!and(\d+)?/i",$field)) {
					// handled later
				} else if ($linked_table=='flag') { 
					// link in the flag tables in the proper way
				
					// if we are querying users... do something special.
					if (isset($original_conditions['flag.itemId']) && $this->TYPE=='user') { 
						if (!isset($joins['flags'])) { 

							if (isset($original_conditions['flag.id']) && $original_conditions['flag.id'] == "null") { 
								$joins['flags'] = "left join flags flag on flag.itemId=" . $this->table_shortname . ".id and flag.type='" . $this->TYPE . "'";
							} else {
								$joins['flags'] = "inner join flags flag on flag.userId=" . $this->table_shortname . ".id";
							}
							if (isset($original_conditions['flag.name'])) { 
								$joins['flags'] .= " and flag.name='" . mysql_real_escape_string($original_conditions['flag.name']) . "'";
								unset($conditions['flag.name']);
							}
							if (isset($original_conditions['flag.userId'])) { 
								$joins['flags'] .= " and flag.userId='" . mysql_real_escape_string($original_conditions['flag.userId']) . "'";
								unset($conditions['flag.userId']);
							}

						}						
					} else { 					
						// if we are querying another type	
						if (!isset($joins['flags'])) { 

							if (isset($original_conditions['flag.id']) && $original_conditions['flag.id'] == "null") { 
								$joins['flags'] = "left join flags flag on flag.itemId=" . $this->table_shortname . ".id and flag.type='" . $this->TYPE . "'";
								
							} else {
								$joins['flags'] = "inner join flags flag on flag.itemId=" . $this->table_shortname . ".id and flag.type='" . $this->TYPE . "'";
							
							}
							if (isset($original_conditions['flag.name'])) { 
								$joins['flags'] .= " and flag.name='" . mysql_real_escape_string($original_conditions['flag.name']) . "'";
								unset($conditions['flag.name']);
							}
							if (isset($original_conditions['flag.userId'])) { 
								$joins['flags'] .= " and flag.userId='" . mysql_real_escape_string($original_conditions['flag.userId']) . "'";
								unset($conditions['flag.userId']);
							}
						}			
						
					
					}

				} else if ($linked_table!=$this->table_shortname) {
					// handle fields in remote tables
					
					// find the join statement for this table.
					if ($this->JOINS[$linked_table]) {
						$obj= $this->referenceObject($linked_table);

						if (!$obj || $obj->isRealField($field)) {
							$joins[$linked_table] = $this->JOINS[$linked_table];
						} else {
						
							$joins[$linked_table] = $this->JOINS[$linked_table];
						
							if (!$existing_joins['meta_' . $linked_table]["{$linked_table}_m_{$field}"]) {
								$joins['meta_' . $linked_table]["{$linked_table}_m_{$field}"] = "{$join_type} JOIN meta {$linked_table}_m_{$field} on {$linked_table}_m_{$field}.itemId={$linked_table}.id and {$linked_table}_m_{$field}.type='" . $obj->TYPE . "' and {$linked_table}_m_{$field}.name='$field'";
							}
							unset($conditions["{$linked_table}.{$field}{$operator}"]);
							if ($value!='null') { 
								$conditions["{$linked_table}_m_{$field}.value{$operator}"] = $value;
							} else {
								$conditions["{$linked_table}_m_{$field}.id{$operator}"] = 'null';
							}
						
						}
					
					} else {
						$this->throwError("Not sure how to find the field $field from $linked_table when joining with {$this->table_shortname}");
					}				
				} else {
					// handle fields in the local table
	
	
					// skip fields where we just passed in a 1
					if (preg_match("/^\d+/",$field)) { continue; } 
					
					if (!$this->isRealField($field)) {
						
						if (!$existing_joins['meta_' . $linked_table]["{$linked_table}_m_{$field}"]) {
							$joins['meta_' . $linked_table]["{$linked_table}_m_{$field}"] = "{$join_type} JOIN meta {$linked_table}_m_{$field} on {$linked_table}_m_{$field}.itemId={$linked_table}.id and {$linked_table}_m_{$field}.type='" . $this->TYPE . "' and {$linked_table}_m_{$field}.name='$field'";
						}

						if ($computed_field != $field) { 
						
							unset($conditions["{$field},{$computed_field}{$operator}"]);
							if ($value!='null') { 
								$conditions["{$linked_table}_m_{$field}.value,{$computed_field}{$operator}"] = $value;
							} else {
								$conditions["{$linked_table}_m_{$field}.id,{$computed_field}{$operator}"] = 'null';
							}
								
						
						} else {
	
							unset($conditions["{$field}{$operator}"]);
							if ($value!='null') { 
								$conditions["{$linked_table}_m_{$field}.value{$operator}"] = $value;
							} else {
								$conditions["{$linked_table}_m_{$field}.id{$operator}"] = 'null';
							}
						}
					
					} else {

						if ($computed_field != $field) { 
							// add table name to unlabeled fields so field names don't bash into one another
							unset($conditions["{$field},{$computed_field}{$operator}"]);
							$conditions["{$linked_table}.{$field},{$computed_field}{$operator}"] = $value;

						} else { 
							// add table name to unlabeled fields so field names don't bash into one another
							unset($conditions["{$field}{$operator}"]);
							$conditions["{$linked_table}.{$field}{$operator}"] = $value;
						}
					}

				
				}				
			
			} // end foreach condition

			// do this all again, but ONLY for the or and and clauses
			foreach ($original_conditions as $field=>$value) { 
			
				// conditions come in the format
				// table_alias.field:operator
				
				$linked_table = null;
				$operator = null;				
				
				// split table alias from fieldname
				if (preg_match("/^(.*?)\.(.*)/",$field,$matches)) { 
					$linked_table = $matches[1];
					$field = $matches[2];
					$operator =  null;
					if (preg_match("/(.*?)\:(.*)/",$field,$matches)) { 
						$field = $matches[1];
						$operator = ":" . $matches[2];
					}						
				} else if (preg_match("/^(.*?):(.*)/",$field,$matches)) {
					$linked_table = $this->table_shortname;
					$field = $matches[1];
					$operator = ":".$matches[2];
				} else {
					// no operator
					$linked_table = $this->table_shortname;
				}

				// now we know what field we want
				// what table the field is in
				// and what operator we're going to call on it.		

				//echo "Handling $field from $linked_table<br />";
								
				// handle sub-clauses
				if (preg_match("/^or(\d+)?/i",$field)) { 
					// generate joins based on this OR
					//echo "Processing an OR<br />";
					$this->generateFrom($from,$value,'LEFT',$joins);
					$conditions[$field] = $value;
				} else if (preg_match("/^and(\d+)?/i",$field)) {
					// generate joins based on this AND
					//echo "Processing an AND<br />";
					$this->generateFrom($from,$value,'LEFT',$joins);
					$conditions[$field] = $value;
				} else if (preg_match("/^\!or(\d+)?/i",$field)) { 
					// generate joins based on this OR
					//echo "Processing an OR<br />";
					$this->generateFrom($from,$value,'LEFT',$joins);
					$conditions[$field] = $value;
				} else if (preg_match("/^\!and(\d+)?/i",$field)) {
					// generate joins based on this AND
					//echo "Processing an AND<br />";
					$this->generateFrom($from,$value,'LEFT',$joins);
					$conditions[$field] = $value;
				}
			}
			
			
			
			
			
			if ($this->TYPE == "user") { 
				if (isset($joins['g'])) { $joins['mem'] = null; }
			}
			if ($this->TYPE == "tags") { 
				if (isset($joins['d'])) { $joins['tr'] = null; }
				if (isset($joins['g'])) { $joins['tr'] = null; }
				if (isset($joins['f'])) { $joins['tr'] = null; }
				if (isset($joins['c'])) { $joins['tr'] = null; }
				if (isset($joins['u'])) { $joins['tr'] = null; }

			}
		
			$from = $from . " " . r_implode(" ",$joins);
			// update conditions array
			return $from;
		
		
		}
	
	
	
	
	
	
	
	
	
	
	
		function generateFrom2(&$from,&$conditions) { 
		
			// joins will contain a list of all the necessary joins
			$joins = array();

			// setup a default $from
			if(!$from) { 
				$from = "FROM " . $this->table_name . " " . $this->table_shortname;
			}
			
			$new_conditions = $conditions;
			
			foreach ($conditions as $field => $value) { 
			
				// is this condition asking for a field in a linked table?				
				if (preg_match("/^(.*?)\.(.*)/",$field,$matches)) { 
					$linked_table = $matches[1];
					if ($linked_table == $this->table_shortname) { continue; }
					$field = $matches[2];
					$operator =  null;
					if (preg_match("/(.*?)\:(.*)/",$field,$matches)) { 
						$field = $matches[1];
						$operator = ":" . $matches[2];
					}
					if ($linked_table != 'flag') { 
						if ($this->JOINS[$linked_table]) { 
							// is this a real field or a meta field?
							$obj = $this->referenceObject($linked_table);
							if (!$obj || $obj->isRealField($field)) {
								$joins[$linked_table] = $this->JOINS[$linked_table];					
							} else if ($obj && ($obj->TYPE=="user" || $obj->TYPE=="content" || $obj->TYPE=="group")) {
							
								// FIX THIS
								// if I reference a meta field, but do NOT reference any other fields from a table
								// I don't actually need to link in the real table, just the meta table.
								
								$joins[$linked_table] = $this->JOINS[$linked_table];
								if (isset($joins['meta_' . $linked_table])) {
									//$c = sizeof($joins['meta_' . $linked_table]);
									$c = sizeof(explode(" join meta ",$joins['meta_'.$linked_table]));

									if ($value=="null") { 
										$joins['meta_' . $linked_table] .= " left join meta {$linked_table}_m_{$field} on {$linked_table}_m_{$field}.itemId={$linked_table}.id and {$linked_table}_m_{$field}.type='" . $obj->TYPE . "'  and {$linked_table}_m_{$field}.name='$field'";
									} else {
										$joins['meta_' . $linked_table] .= " inner join meta {$linked_table}_m_{$field} on {$linked_table}_m_{$field}.itemId={$linked_table}.id and {$linked_table}_m_{$field}.type='" . $obj->TYPE . "' and {$linked_table}_m_{$field}.name='$field'";
									}
									unset($new_conditions["{$linked_table}.{$field}{$operator}"]);
									if ($value!='null') { 
										$new_conditions["{$linked_table}_m_{$field}.value{$operator}"] = $value;
									} else {
										$new_conditions["{$linked_table}_m_{$field}.id{$operator}"] = 'null';
									}

								} else {
									$c = 0;
									if ($value=="null") { 
										$joins['meta_' . $linked_table] = " left join meta {$linked_table}_m_{$field} on {$linked_table}_m_{$field}.itemId={$linked_table}.id and {$linked_table}_m_{$field}.type='" . $obj->TYPE . "' and {$linked_table}_m_{$field}.name='$field'";
									} else {
										$joins['meta_' . $linked_table] = " inner join meta {$linked_table}_m_{$field} on {$linked_table}_m_{$field}.itemId={$linked_table}.id and {$linked_table}_m_{$field}.type='" . $obj->TYPE . "' and {$linked_table}_m_{$field}.name='$field'";
									}
									unset($new_conditions["{$linked_table}.{$field}{$operator}"]);
									if ($value!='null') { 
										$new_conditions["{$linked_table}_m_{$field}.value{$operator}"] = $value;
									} else {
										$new_conditions["{$linked_table}_m_{$field}.id{$operator}"] = 'null';
									}
								}
							} else {
								$this->throwError("Not sure how to find the field $field from $linked_table when joining with {$this->table_shortname}");
							}
						
						} else {
							$this->throwError("Attempted to join {$this->table_shortname} to $linked_table for $field=$value, but couldn't!");
						}
					} else { 					
						// do flags join
						
						if (isset($conditions['flag.itemId']) && $this->TYPE=='user') { 
							if (!isset($joins['flags'])) { 

								if (isset($conditions['flag.id']) && $conditions['flag.id'] == "null") { 
									$joins['flags'] = "left join flags flag on flag.itemId=" . $this->table_shortname . ".id and flag.type='" . $this->TYPE . "'";
								} else {
									$joins['flags'] = "inner join flags flag on flag.userId=" . $this->table_shortname . ".id";
								}
								if (isset($conditions['flag.name'])) { 
									$joins['flags'] .= " and flag.name='" . mysql_real_escape_string($conditions['flag.name']) . "'";
									unset($new_conditions['flag.name']);
								}
								if (isset($conditions['flag.userId'])) { 
									$joins['flags'] .= " and flag.userId='" . mysql_real_escape_string($conditions['flag.userId']) . "'";
									unset($new_conditions['flag.userId']);
								}

							}						
						} else { 						
							if (!isset($joins['flags'])) { 

								if (isset($conditions['flag.id']) && $conditions['flag.id'] == "null") { 
									$joins['flags'] = "left join flags flag on flag.itemId=" . $this->table_shortname . ".id and flag.type='" . $this->TYPE . "'";
									
								} else {
									$joins['flags'] = "inner join flags flag on flag.itemId=" . $this->table_shortname . ".id and flag.type='" . $this->TYPE . "'";
								
								}
								if (isset($conditions['flag.name'])) { 
									$joins['flags'] .= " and flag.name='" . mysql_real_escape_string($conditions['flag.name']) . "'";
									unset($new_conditions['flag.name']);
								}
								if (isset($conditions['flag.userId'])) { 
									$joins['flags'] .= " and flag.userId='" . mysql_real_escape_string($conditions['flag.userId']) . "'";
									unset($new_conditions['flag.userId']);
								}
							}			
							
						
						}

					}

				
				} else {
				// this is a local field
					$operator = null;
					if (preg_match("/^(.*?):(.*)/",$field,$matches)) {
						$field = $matches[1];
						$operator = ":".$matches[2];
					}
					
					// skip fields where we just passed in a 1
					if (preg_match("/^\d+/",$field)) { continue; } 
					
					// skip special case where we specified an or clause
					// which HAVE to use real fields or else BAAAAAD!!!
					if (preg_match("/^or(\d+)?/i",$field)) { continue; } 
					if (preg_match("/^and(\d+)?/i",$field)) { continue; } 
					
					$linked_table = $this->table_shortname;
					if (!$this->isRealField($field)) {
						$c = 0;
						if (isset($joins['meta_' . $linked_table])) {
							$c = sizeof(explode(" join meta ",$joins['meta_'.$linked_table]));
						}
						
						if ($value=="null") { 
							if (isset($joins['meta_' . $linked_table])) {
								$joins['meta_' . $linked_table] .= " left join meta {$linked_table}_m_{$field} on {$linked_table}_m_{$field}.itemId={$linked_table}.id and {$linked_table}_m_{$field}.type='" . $this->TYPE . "'  and {$linked_table}_m_{$field}.name='$field'";
							} else {
								$joins['meta_' . $linked_table] = " left join meta {$linked_table}_m_{$field} on {$linked_table}_m_{$field}.itemId={$linked_table}.id and {$linked_table}_m_{$field}.type='" . $this->TYPE . "'  and {$linked_table}_m_{$field}.name='$field'";
							}
						} else {
							if (isset($joins['meta_' . $linked_table])) { 
								$joins['meta_' . $linked_table] .= " inner join meta {$linked_table}_m_{$field} on {$linked_table}_m_{$field}.itemId={$linked_table}.id and {$linked_table}_m_{$field}.type='" . $this->TYPE . "' and {$linked_table}_m_{$field}.name='$field'";
							} else {
								$joins['meta_' . $linked_table] = " inner join meta {$linked_table}_m_{$field} on {$linked_table}_m_{$field}.itemId={$linked_table}.id and {$linked_table}_m_{$field}.type='" . $this->TYPE . "' and {$linked_table}_m_{$field}.name='$field'";
							}
						}
						unset($new_conditions["{$field}{$operator}"]);
						if ($value!='null') { 
							$new_conditions["{$linked_table}_m_{$field}.value{$operator}"] = $value;
						} else {
							$new_conditions["{$linked_table}_m_{$field}.id{$operator}"] = 'null';
						}
					
					} else {
						// add table name to unlabeled fields so field names don't bash into one another
						unset($new_conditions["{$field}{$operator}"]);
						$new_conditions["{$linked_table}.{$field}{$operator}"] = $value;
					}
				
				}
			
			}

			if ($this->TYPE == "user") { 
				if (isset($joins['g'])) { $joins['mem'] = null; }
			}
			if ($this->TYPE == "tags") { 
				if (isset($joins['d'])) { $joins['tr'] = null; }
			}
		
		
			// update conditions array
			$conditions = $new_conditions;
			return $from . " " . implode(" ",$joins) . " ";
		
		
		}
	
	
		function specialSelect() {
			$extras = array();
			foreach ($this->FIELDS as $field) { 
				if (isset($this->FIELD_PROCESSORS[$field . "_select"])) { 
					$func = $this->FIELD_PROCESSORS[$field."_select"];
					$extras = array_merge($extras,$func($this,$field));
				}
			}
			
			if (sizeof($extras) >0) { 
				return "," . implode(",",$extras);
			} else {
				return '';
			}
		}


		function query($conditions,$sort=null,$count = 100, $offset=0,$from,$select,$glue = " AND ",$groupBy=null) { 
			$this->success = false;
			if (!$select) {
				$select = "SELECT DISTINCT " . $this->table_shortname . ".*,(TIME_TO_SEC(TIMEDIFF(NOW()," . $this->table_shortname . ".date)) / 60) as minutes ";
			}
			$select .= $this->specialSelect();

			if (!$sort) { 
				$sort = $this->table_shortname . ".date DESC";
			} else {
				$sort = "$sort";
			}
			
			if ($groupBy) { $groupBy = " GROUP BY $groupBy"; }
			if (!preg_match("/order by/i",$sort)) { 
				$sort = " ORDER BY $sort";
			}
			
			if ($offset == null || $offset == '') { $offset = 0; }
			if ($count == null || $count == '') { $count = 100; } 

			$from = $this->generateFrom($from,$conditions);
			$where = $this->generateWhere($conditions,$glue);
			if ($where) { $where = "WHERE $where"; }
			$sql = "$select $from $where $groupBy $sort LIMIT $offset,$count";
			$this->POD->tolog($sql,2);
			$results = mysql_query($sql,$this->POD->DATABASE);
			if (!$results) {
				$this->throwError("SQL Error in Query: " . mysql_error() . " QUERY: $sql");
				$this->error_code = 500;		
				$this->POD->tolog($this->error,2);
				return null;	
			}
			$num = mysql_num_rows($results);
			$return = array();
			$this->POD->tolog($this->TYPE . "->query Results: $num");
			if ($num > 0) {
				while ($row = mysql_fetch_assoc($results)) {
					array_push($return,$row);
				}
			} else {
				//$this->error = "Obj not found";
				//$this->error_code = 404;
			}
			
			$this->success = true;
			return $return;
					
		}


		function load($fields,$values,$select=null,$from=null,$sort=null,$glue=" AND ") { 
			$this->success = false;
			$this->POD->tolog("Load $fields => $values");
			if (!$select) {
				$select = "SELECT " . $this->table_shortname . ".*,(TIME_TO_SEC(TIMEDIFF(NOW()," . $this->table_shortname . ".date)) / 60) as minutes ";
			}
			if (!$from) {
				$from = "FROM " . $this->table_name . " ". $this->table_shortname;
			}
			if (!$sort) { 
				$sort = "ORDER BY date DESC";
			}
	
			$where = array();
			
			if (is_array($fields)) {
				for ($i = 0; $i < sizeof($fields); $i++) { 
					$f = mysql_real_escape_string($fields[$i]);
					$v = mysql_real_escape_string($values[$i]);
					array_push($where,"$f='$v'");
				} 
			} else {	
				$f = mysql_real_escape_string($fields);
				$v = mysql_real_escape_string($values);
				array_push($where,"$f='$v'");
			}
		
			$sql = "$select $from WHERE " . implode($glue,$where) . " $sort";
			$this->POD->tolog($sql,2);
			$results = mysql_query($sql,$this->POD->DATABASE);
			$num = mysql_num_rows($results);
			if ($results && $num > 0) {
				$this->POD->tolog("Load: Success");
				$doc = mysql_fetch_assoc($results);
				$this->DATA = $doc;
				$this->success = true;
			} else {
				$this->POD->tolog("Load: Failed");
				$this->success = false;
/* 				$this->throwError("Obj not found"); */
				$this->error_code = 404;
				return $this;
			}
			mysql_free_result($results);
			return $this;
					
		}
	
/* Flag Functions */
/* Bookmarks, watch list, friends, votes */

	
	// return a simple array of the names of any flags added to this object
	// 1 record per type of flag
	// to be used in connection with separate ->getContents or ->getPeople queries
	function getInFlags() { 
		$this->success = false;
		if (!$this->get('id')) {
			$this->throwError("Object not saved yet!");
			$this->error_code = 222;
			return null;
		}
	
		$sql = "SELECT distinct name as flag,type from flags where itemId={$this->get('id')} and type='{$this->TYPE}'";
		$this->POD->tolog($sql,2);
		$res = mysql_query($sql,$this->POD->DATABASE);
		$num = mysql_affected_rows($this->POD->DATABASE);
		if (!$res) {
			$this->throwError("SQL error on getFlags ($res,$num):" . mysql_error($this->POD->DATABASE));
			$this->POD->tolog($this->error);
			$this->error_code = 500;
			return null;	
		}
		
		$return = array();
		while ($flag = mysql_fetch_assoc($res)) {
			$return[$flag['flag']] = $flag['type'];
		}
				
		$this->success = true;
		return $return;
	
	}
	
	// return a simple array of the names of any flags issued by this object
	// 1 record per type of flag
	// to be used in connection with separate ->getContents or ->getPeople queries
	function getOutFlags() { 
		$this->success = false;
		if (!$this->get('id')) {
			$this->throwError("Object not saved yet!");
			$this->error_code = 222;
			return null;
		}
		if ($this->TYPE != 'user') {
			$this->throwError("Can't call this method on non-user object");
			$this->error_code = 500;
			return null;
		}	
		$sql = "SELECT distinct name as flag,type from flags where userId={$this->get('id')}";
		$this->POD->tolog($sql,2);
		$res = mysql_query($sql,$this->POD->DATABASE);
		$num = mysql_affected_rows($this->POD->DATABASE);
		if (!$res) {
			$this->throwError("SQL error on getFlags ($res,$num):" . mysql_error($this->POD->DATABASE));
			$this->POD->tolog($this->error);
			$this->error_code = 500;
			return null;	
		}
		
		$return = array();
		while ($flag = mysql_fetch_assoc($res)) {
			$return[$flag['flag']] = $flag['type'];
		}
				
		$this->success = true;
		return $return;
	
	}
	
	function addFlag($flag,$person,$value=true) { 
		$this->success = false;
		if (!$this->get('id')) {
			$this->throwError("Object not saved yet!");
			$this->error_code = 222;
			return null;
		}
		if (!$person || !$person->get('id')) {
			$this->throwError("Person not saved yet!");
			$this->error_code = 222;
			return null;
		}

	
		if (!$this->hasFlag($flag,$person)) {

			$sql = "INSERT INTO flags(type,itemId,name,value,userId,date) VALUES ('" . $this->TYPE . "'," . $this->get('id') . ",'" . mysql_real_escape_string($flag) . "','" . mysql_real_escape_string($this->POD->handleUTF8($value)) . "'," . $person->get('id') . ",NOW());";
			$this->POD->tolog($sql,2);
			$res = mysql_query($sql,$this->POD->DATABASE);
			$num = mysql_affected_rows($this->POD->DATABASE);
			if (!$res || $num < 1) {
				$this->throwError("SQL error on insert into flags ($res,$num):" . mysql_error($this->POD->DATABASE));
				$this->POD->tolog($this->error);
				$this->error_code = 500;
				return null;	
			}
			
		} else {

		
			if ($this->hasFlag($flag,$person)!=$value) { 
				$this->removeFlag($flag,$person);
				$this->addFlag($flag,$person,$value);
			}
		
		}	

		$fact = $person->get('id') . "-$flag-" . $this->get('id');
		$this->POD->cachefact($fact,$value);

		$fact = "$flag-" . $this->get('id');
		$this->POD->cachefact($fact,$value);


		// clear count cache
		$fact = $this->get('id') . "-$flag-count";
		$this->POD->cachefact($fact,null);

		$fact = $person->get('id') . "-$flag-date-" . $this->get('id');
		$this->POD->cachefact($fact,null);

		if (!($value === true)) { 
			$fact = $this->get('id') . "-$flag-count-$value";		
			$this->POD->cachefact($fact,null);
		}

		$this->success = true;
		return $this;
	
	}

	function removeFlag($flag,$person=null) { 
		$this->success = false;
		if (!$this->get('id')) {
			$this->throwError("Object not saved yet!");
			$this->error_code = 222;
			return null;
		}
		if (!$person || !$person->get('id')) {
			$this->throwError("Person not saved yet!");
			$this->error_code = 222;
			return null;
		}

		$value = $this->hasFlag($flag,$person);
		if (!($value===false || $value===null)) {

			if ($person) { 
				$sql = "DELETE FROM flags WHERE userId=" . $person->get('id') . " AND itemId = " . $this->get('id') . " and type='" . $this->TYPE . "' and name='" .mysql_real_escape_string($flag) ."';";
			} else {
				$sql = "DELETE FROM flags WHERE itemId = " . $this->get('id') . " and type='" . $this->TYPE . "' and name='" .mysql_real_escape_string($flag) ."';";			
			}
			$this->POD->tolog($sql,2);
			$res = mysql_query($sql,$this->POD->DATABASE);	
/* 			$num = mysql_affected_rows($this->POD->DATABASE); */

			if (!$res) {
			
				$this->throwError("SQL error on delete from flags $sql" . mysql_error($this->POD->DATABASE));
				$this->POD->tolog($this->error);
				$this->error_code = 500;
				return null;	
			}			

		}


		if ($person) { 
			$fact = $person->get('id') . "-$flag-" . $this->get('id');
			$this->POD->cachefact($fact,0);
		}

		$fact = "$flag-" . $this->get('id');
		$this->POD->cachefact($fact,0);

		// clear count cache
		$fact = $this->get('id') . "-$flag-count";
		$this->POD->cachefact($fact,null);

		$fact = $person->get('id') . "-$flag-date-" . $this->get('id');
		$this->POD->cachefact($fact,null);
		
		if (!($value===false)) { 
			$fact = $this->get('id') . "-$flag-count-$value";		

			$this->POD->cachefact($fact,null);
		}

		$this->success = true;


	}


	function flaggedCount($flag,$value=null) {

		$fact = $this->get('id') . "-{$flag}ed-count";
		$val = $this->POD->factcache($fact);
		if (!($val === null)) { 
			return $val;
		}

	
		if ($value) { 
			$sql = "SELECT count(1) as count FROM flags WHERE userId=" . $this->id . " and name='" . mysql_real_escape_string($flag) . "' and value='" . mysql_real_escape_string($value) . "'";
		} else {
			$sql = "SELECT count(1) as count FROM flags WHERE userId=" . $this->id . " and name='" . mysql_real_escape_string($flag) . "'";
		}
		
		$this->POD->tolog($sql,2);
		$res = mysql_query($sql,$this->POD->DATABASE);
		if (!$res) { 
			$this->POD->cachefact($fact,0);
			return 0;
		} else {
			$data = mysql_fetch_assoc($res);
			$this->POD->cachefact($fact,$data['count']);
			return $data['count'];	
		}	
			
	
	}


	function flagCount($flag,$value=null) {

		if ($value==null) { 
			$fact = $this->get('id') . "-$flag-count";
		} else {
			$fact = $this->get('id') . "-$flag-count-$value";		
		}
		$val = $this->POD->factcache($fact);
		if (!($val === null)) { 
			return $val;
		}

	
		if (isset($value)) { 
			$sql = "SELECT count(1) as count FROM flags WHERE type='" . $this->TYPE . "' and itemId=" . $this->id . " and name='" . mysql_real_escape_string($flag) . "' and value='" . mysql_real_escape_string($value) . "'";
		} else {
			$sql = "SELECT count(1) as count FROM flags WHERE type='" . $this->TYPE . "' and itemId=" . $this->id . " and name='" . mysql_real_escape_string($flag) . "'";
		}
		
		$this->POD->tolog($sql,2);
		$res = mysql_query($sql,$this->POD->DATABASE);
		if (!$res) { 
			$this->POD->cachefact($fact,0);
			return 0;
		} else {
			$data = mysql_fetch_assoc($res);
			$this->POD->cachefact($fact,$data['count']);
			return $data['count'];	
		}	
			
	
	}


	function flagDate($flag,$person) { 

		$this->success = false;
		if (!$this->get('id')) {
			$this->throwError("Object not saved yet!");
			$this->error_code = 222;
			return null;
		}
		if (!$person || !$person->get('id')) {
			$this->throwError("Person not saved yet!");
			$this->error_code = 222;
			return null;
		}	
		$fact = $person->get('id') . "-$flag-date-" . $this->get('id');
		$val = $this->POD->factcache($fact);
		if (!($val === null)) { 
			$this->success = true;
			return $val;
		}
		
		$sql = "SELECT date FROM flags WHERE userId=" . $person->get('id') . " AND itemId=" . $this->Get('id') . " AND type='" . $this->TYPE . "' and name = '" . mysql_real_escape_string($flag) . "';";
		$this->POD->tolog($sql,2);
		$res = mysql_query($sql,$this->POD->DATABASE);	
		if ($res) {
			$this->success = true;
			$num = mysql_num_rows($res);
			if ($num < 1) {
				$this->POD->cachefact($fact,0);
				return false;

			} else {
				$v = mysql_fetch_assoc($res);
				$this->POD->cachefact($fact,$v['date']);
				return $v['date'];
			} 
		} else {
		
			$this->throwError("SQL error: " . mysql_error($this->POD->DATABASE));
			$this->error_code = 500;
			$this->POD->tolog($this->error);
			$this->success = false;
		}		

	
	}



	function hasFlag($flag,$person=null) { 
		$this->success = false;
		if (!$this->get('id')) {
			$this->throwError("Object not saved yet!");
			$this->error_code = 222;
			return false;
		}
		if ($person && !$person->get('id')) {
			$this->throwError("Person not saved yet!");
			$this->error_code = 222;
			return false;
		}

		if ($person) { 
			$fact = $person->get('id') . "-$flag-" . $this->get('id');
		} else {
			$fact = "$flag-" . $this->get('id');
		}


		$val = $this->POD->factcache($fact);
		if (!($val === null)) { 
			$this->success = true;
			return $val;
		}
	
		if ($person) { 
			$sql = "SELECT value from flags WHERE userId=" . $person->get('id') . " AND itemId=" . $this->get('id') . " AND type='" . $this->TYPE . "' AND name='" . mysql_real_escape_string($flag) . "';";
		} else {
			$sql = "SELECT value from flags WHERE itemId=" . $this->get('id') . " AND type='" . $this->TYPE . "' AND name='" . mysql_real_escape_string($flag) . "';";		
		}	
		
		$this->POD->tolog($sql,2);
		$res = mysql_query($sql,$this->POD->DATABASE);	
		if ($res) {
			$this->success = true;
			$num = mysql_num_rows($res);
			if ($num < 1) {
				$this->POD->cachefact($fact,false);
				return false;

			} else {
				$v = mysql_fetch_assoc($res);
				$this->POD->cachefact($fact,$v['value']);
				return $v['value'];
			} 
		} else {
		
			$this->throwError("SQL error: " . mysql_error($this->POD->DATABASE));
			$this->error_code = 500;
			$this->POD->tolog($this->error);
			$this->success = false;
		}
	
	}
	
	function toggleFlag($flag,$person) { 
		if (!$this->hasFlag($flag,$person)===false) {
			$this->removeFlag($flag,$person);
			return 0;
		} else {
			$this->addFlag($flag,$person);
			return 1;
		}	
	
	}

	
/* Meta-field Functions */

		function removeMeta($key) {
			$this->addMeta($key);
		}

		function addMeta($key,$value=null,$strip_html = true) {
            $this->success = null;
			$metaid = null;
			
			if ($strip_html) { 
				$value = $this->POD->sanitizeInput($value);
			}
	
			$value = $this->POD->handleUTF8(stripslashes($value));

            if ($this->get('id')) {
                if ($value != "") {
                		$this->DATA[$key] = $value;

                        $key = mysql_real_escape_string($key);
                        $value = mysql_real_escape_string($value);
                        
                        $sql = "INSERT INTO meta (type,itemId,name,value) values ('" . $this->TYPE . "'," . $this->get('id') . ",'$key','$value');";
						$this->POD->tolog($sql,2);
    	                $res = mysql_query($sql,$this->POD->DATABASE);
    	                if (!$res || mysql_affected_rows($this->POD->DATABASE)<1) {
	                        $sql = "UPDATE meta SET value='" . $value . "' WHERE type='" . $this->TYPE . "' AND itemId=" . $this->get('id') . " AND name='$key';";
							$this->POD->tolog($sql,2);
    		                $res = mysql_query($sql,$this->POD->DATABASE);
    	                } else {
	         	            $metaid = mysql_insert_id($this->POD->DATABASE);        	                
    	                }
                } else {
                	if ($this->get($key)!='') { 
                        $sql = "DELETE FROM meta WHERE type='" . $this->TYPE . "' AND itemId=" . $this->get('id') . " AND name='" . $key . "';";
              			$this->POD->tolog($sql,2);
                        mysql_query($sql,$this->POD->DATABASE);
                    }
                    unset($this->DATA[$key]);
                }
                $this->success = true;
	        } else {
	        	$this->DATA[$key] = $value;
	        	if ($value==null) { 
	        		unset($this->UNSAVED_META[$key]);
	        	} else {
	        		$this->UNSAVED_META[$key] = $value;
	        	}
	        	$this->success = true;
	        
	        }
			$this->POD->cachestore($this);
    	    return $metaid;
		}


		function loadMeta() {
			$this->success = null;
			
			if ($this->get('id')) {
		
				$sql = "SELECT name,value FROM meta WHERE type='" . $this->TYPE . "' AND itemId=" . $this->get('id');
				$this->POD->tolog($sql,2);
				$res = mysql_query($sql,$this->POD->DATABASE);
				while ($meta = mysql_fetch_assoc($res)) {
					$this->DATA[$meta['name']] = $meta['value'];
				}
				$this->success = true;
			} 
			
			return $this->success;
		}
			

		function getMeta() {
			$this->success = null;
			$ret = array();
			if ($this->get('id')) {
		
				$sql = "SELECT name,value FROM meta WHERE type='" . $this->TYPE . "' AND itemId=" . $this->get('id');
				$this->POD->tolog($sql,2);
				$res = mysql_query($sql,$this->POD->DATABASE);
				while ($meta = mysql_fetch_assoc($res)) {
					if ($meta['name'] != 'adminUser') { 
						$ret[$meta['name']] = $meta['value'];
					}
				}
				$this->success = true;
			} 
			return $ret;
		}


		function tokenReplace($str) { 

			$str = preg_replace("/\{this\.(.*?)\}/e",'$this->get("\\1")',$str);
			$str = preg_replace("/@this\.(\w+)\b/e",'$this->get("\\1")',$str);
				
			$str = preg_replace("/\{author\.(.*?)\}/e",'$this->author()->get("\\1")',$str);
			$str = preg_replace("/@author\.(\w+)\b/e",'$this->author()->get("\\1")',$str);
			$str = preg_replace("/\{owner\.(.*?)\}/e",'$this->owner()->get("\\1")',$str);
			$str = preg_replace("/@owner\.(\w+)\b/e",'$this->owner()->get("\\1")',$str);
			$str = preg_replace("/\{creator\.(.*?)\}/e",'$this->creator()->get("\\1")',$str);
			$str = preg_replace("/@creator\.(\w+)\b/e",'$this->creator()->get("\\1")',$str);
			$str = preg_replace("/\{parent\.(.*?)\}/e",'$this->parent()->get("\\1")',$str);
			$str = preg_replace("/@parent\.(\w+)\b/e",'$this->parent()->get("\\1")',$str);

			return $str;
	
		}
		
		function table_shortname() {
			return $this->table_shortname;
		}
		
	}

	
	function r_implode( $glue, $pieces ) 
	{ 
	  $retVal = array();
	  foreach( $pieces as $r_pieces) { 
	    if( is_array( $r_pieces )) { 
	      $retVal[] = r_implode( $glue, $r_pieces ); 
	    } else { 
	      $retVal[] = $r_pieces; 
	    } 
	  } 
	  return implode( $glue, $retVal ); 
	} 
	
?>