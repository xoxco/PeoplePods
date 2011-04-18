<? 
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* lib/Files.php
* This file defines the file object.
* This file also handles all the file storage, resizing, and serving
*
* Documentation for this object can be found here:
* http://peoplepods.net/readme/file-object
/**********************************************/


	require_once("Obj.php");
	class File extends Obj {
		
		var $isImage = false;


		// base database configuration for this object.
		static private $DEFAULT_FIELDS = array('id','file_name','original_name','description','extension','mime_type','userId','contentId','groupId','changeDate','date');
		static private $IGNORE_FIELDS = array('original_file','original','resized','thumbnail','permalink','minutes','tmp_name');
		static private $DEFAULT_JOINS = array(
						'u'=>'inner join users u on u.id=f.userId', // link to files owner
						'd'=>'inner join content d on d.id=f.contentId', // link to file's content
						't' => 'inner join tagRef tr on tr.itemId=f.id and tr.type="file" inner join tags t on tr.tagId=t.id', // link to tags

					);
						
		static private $FIELD_PROCESSORS = array();			
	
		static private $EXTRA_METHODS = array();
		protected $GROUP;
	
		function File($POD,$PARAMETERS = null) { 
			parent::Obj($POD,'file',array(
				'table_name' => "files",
				'table_shortname' => "f",
				'fields' => self::$DEFAULT_FIELDS,
				'ignore_fields'=>self::$IGNORE_FIELDS,				
				'joins' => self::$DEFAULT_JOINS,
				'field_processors'=>self::$FIELD_PROCESSORS		
			));

			if (!$this->success()) {
				return null;
			}
			
			
			if (isset($PARAMETERS['id']) && (sizeof($PARAMETERS)==1)) {
				if ($d = $POD->checkcache('File','id',$PARAMETERS['id'])) {
					$this->DATA = $d;				
				} else {
					$this->load('id',$PARAMETERS['id']);
					$this->loadMeta();

				}
			} else if ($PARAMETERS) {	
				foreach ($PARAMETERS as $key=>$value) {
					if ($key != 'POD') {
						$this->set($key,$value);
					}
				}
				$this->loadMeta();

			}	


			if ($this->get('id')) { 
			// if this is an existing file, set up some path stuff

				if (preg_match("/^image/",$this->get('mime_type'))) { 
					$this->isImage = true;
				}

				$this->generatePermalink();			
			}

			$POD->cachestore($this);
			
			$this->success = true;
			return $this;
			
		}


		function save($local_ok = false) { 
			$this->success = false;
			
			if (!$this->POD->isAuthenticated()) { 
				$this->throwError("Permission Denied");
				return null;
			}		
			if (!$this->get('file_name')) {
				$this->throwError("Could not save file. Required field file_name missing.");
				return;
			}
			if (!$this->get('original_name')) {
				$this->throwError("Could not save new file. Required field original_name missing.");
				return;
			}

			$this->set('original_name',basename($this->get('original_name')));
			
			$this->set('extension',strtolower(array_pop(explode('.',$this->get('original_name')))));

			if ($this->get('extension')=="jpeg") {
				$this->set('extension','jpg');
			}


			$this->set('mime_type',$this->mime_content_type($this->get('original_name')));

			if (!$this->get('mime_type')) {
				$this->throwError("Could not save file. Required field mime_type missing.");
				return;
			}	

			if (!$this->get('id')) {
				if (!$this->get('tmp_name')) {
					$this->throwError("Could not save new file. Required field tmp_name missing.");
					return;
				}
			}

			if (!$this->get('userId')) { 
				$this->set('userId',$this->POD->currentUser()->get('id'));
			}
			
			if (!$this->saved()) {
				$this->set('date','now()');
				$this->set('changeDate','now()');
			} else {
				$this->set('changeDate','now()');
			}
			
			parent::save();
			
			if ($this->get('tmp_name')) { 
				// do file operations
				
				$this->isImage = false;
				
				// is this an image or some other sort of file?
				if (preg_match("/^image/",$this->get('mime_type'))) { 
					$fileDir = $this->POD->libOptions('imgDir');
					$this->isImage = true;
					$this->POD->tolog("file->save() isImage!");
				} else {
					$fileDir = $this->POD->libOptions('docDir');
				}

				$new_name = "$fileDir/" . $this->get('id') . ".original." . $this->get('extension');
								
				$this->POD->tolog("file->save() New file name: $new_name");
				
				// clean up old versions
				$this->cleanup();
				
				// is it an uploaded file?
				if (is_uploaded_file($this->get('tmp_name'))) {
					// move uploaded file
					if (!move_uploaded_file($this->get('tmp_name'),$new_name)) { 
						$this->delete();
						$this->success = false;
						$this->throwError("file->save() Could not move uploaded file to $new_name");
						return null;
					}
				} else if ($local_ok) { // is it a local file?
					// move normal file
					if (!copy($this->get('tmp_name'),$new_name)) { 
						$this->delete();
						$this->success = false;
						$this->throwError("file->save() Could not move local file to $new_name!");
						return null;					
					}
				} else {
						$this->delete();
						$this->success = false;
						$this->throwError("file->save() Could not handle local file (local_ok = false)!");
						return null;											
				}
				
				// is it an image?
				if ($this->isImage) { 
					// crop and resize image
					$this->POD->tolog("file->save() Time to crop an image!");
					
					$this->createThumbs($new_name,$this->get('type'));
					if (!$this->success()) { 
						$error = $this->error();
						$this->delete();
						$this->throwError($error);
						$this->success = false;
						return null;
					}
				}
				
				// did we succeed?
				// if not, undo db stuff
			} else {
				
				$this->POD->tolog("file->save() Updated file info without changing file");
				
			
			}
			$this->generatePermalink();
			
			
			$this->clearCaches();


			$this->POD->cachestore($this);

			$this->success = true;

		}
		
		function clearCaches() {
			if ($this->get('contentId')) { 
				$this->POD->cachefact($this->contentId.'-content-files',null);			
				$this->POD->cachefact($this->contentId.'-content-files_totalcount',null);			

			} else if ($this->get('groupId')) { 
				$this->POD->cachefact($this->groupId.'-group-files',null);					
				$this->POD->cachefact($this->groupId.'-group-files_totalcount',null);					

			} else {
				$this->POD->cachefact($this->userId.'-person-files',null);					
				$this->POD->cachefact($this->userId.'-person-files_totalcount',null);
			}
		
		}
		
		
		function generatePermalink() {

			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array());
			}


			// is this an image or some other sort of file?
			if ($this->isImage) { 
				$fileDir = $this->POD->libOptions('imgDir');
			} else {
				$fileDir = $this->POD->libOptions('docDir');
			}

			$this->set('local_file',"$fileDir/" . $this->get('id') . ".original." . $this->get('extension'),false);
			$this->set('path',"$fileDir/" . $this->get('id') . ".original." . $this->get('extension'),false);

			if ($this->POD->libOptions('enable_core_files')) {
				$path = $this->POD->libOptions('default_files_path');
				$filePath = $this->POD->siteRoot(false) . "/$path/" . $this->get('id');
				$this->set('original_file',"$filePath/original",false);
				if ($this->isImage) { 
					$this->set('resized',"$filePath/resized",false);
					$this->set('thumbnail',"$filePath/thumbnail",false);
				}				
			} else {

				if ($this->isImage) { 
					$filePath = $this->POD->libOptions('imgPath');
				} else {
					$filePath = $this->POD->libOptions('docPath');
				}
				$this->set('original_file',"$filePath/" . $this->get('id') . ".original." . $this->get('extension'),false);
				if ($this->isImage) { 
					$this->set('resized',"$filePath/" . $this->get('id') . ".resized." . $this->get('extension'),false);
					$this->set('thumbnail',"$filePath/" . $this->get('id') . ".thumbnail." . $this->get('extension'),false);
				}
			}

		
		}
		
		
		function cleanup() {

			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array());
			}

		
			if ($this->isImage) { 
				$fileDir = $this->POD->libOptions('imgDir');
/*
				unlink("$fileDir/" . $this->get('id') . ".original." . $this->get('extension'));
				unlink("$fileDir/" . $this->get('id') . ".resized." . $this->get('extension'));
				unlink("$fileDir/" . $this->get('id') . ".thumbnail." . $this->get('extension'));
				

*/
				// find any dynamically generated resizes
				$files = opendir($fileDir);
				while ($file = readdir($files)) { 
					if (preg_match("/" . $this->id . "\./",$file)) {
						unlink($fileDir . "/" . $file);
					}
				}

			} else {
				$fileDir = $this->POD->libOptions('docDir');
				unlink("$fileDir/" . $this->get('id') . ".original." . $this->get('extension'));
			}
		
		}
		
		function delete() { 


			$this->success = false;
			if (!$this->POD->isAuthenticated()) { 
				$this->throwError("Permission Denied");
				return null;
			}
			if (!$this->get('id')) {
				$this->throwError("File not saved yet.");
				return null;
			}
			if (($this->get('userId') != $this->POD->currentUser()->get('id')) && ($this->parent('userId') != $this->POD->currentUser()->get('id')) && (!$this->POD->currentUser()->get('adminUser'))) { 
			// the only people who can delete a comment are the commenter, the owner of the document commented upon, or an admin user
			// if this person is none of those people, fail!
				$this->throwError("Permission Denied");
				return null;
			}		
		
			$this->cleanup();

			$sql = "DELETE FROM files WHERE id = " . $this->get('id');
			$this->POD->tolog($sql,2);
			mysql_query($sql);
		

			$this->clearCaches();
		
			$this->POD->cacheclear($this);
				
			$this->DATA = array();
		
			$this->success = true;
			return true;		
		
		}
		
		
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
		
		function download($size = "original") {

			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array());
			}

			 $this->success = false;
			 
			if ($this->isImage) { 
				$filePath = $this->POD->libOptions('imgDir');
			} else {
				$filePath = $this->POD->libOptions('docDir');
			}

			$filePath .= "/" . $this->get('id') . ".$size." . $this->get('extension');
			$fsize = filesize($filePath);
			if ($fsize > 0) { 	
				 header('Content-Type: ' . $this->get('mime_type'));
				 header('Content-Disposition: attachment; filename="'.$this->get('original_name').'"');
				 header("Content-Transfer-Encoding: binary");
		  	 	 header('Content-Length: ' . $fsize);
		  	 	 ob_clean();
			     flush();
			     readfile($filePath);
		  	 	 $this->success = true;
		  	 	
			} else {
				$this->throwError("Couldn't open file $filePath");
			 	$this->success = false;
			 
			}
  	
  			return $this->success;
		}
	
		function isImage() { return $this->isImage; }
		
		function src($max_width_or_preset='resized',$square=false) { 

			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array($max_width_or_preset,$square));
			}


			$preset = false;
			
			$this->success = false;
			if (preg_match("/\d+/",$max_width_or_preset)) {
				$name = $max_width_or_preset;
				$max_width = $max_width_or_preset;
				if ($square) { 
					$name .="-square";
				}
			} else {
				$preset = true;
				$name = $max_width_or_preset;
			}
			$fileDir = $this->POD->libOptions('imgDir');
			$file_name = "$fileDir/" . $this->get('id') . ".{$name}." . $this->get('extension');
			if (!file_exists($file_name)) {
				if (!$preset) { 
					$this->resizeImage($max_width,$square,$name);
					if (!$this->success()) {
						return false;	
					}
				} else {
					// presets are automatically generated
					// but for some reason this one wasn't found
					// so regenerating probably won't help.
					// so just fail.
					return false;
				}
			}

			if ($this->POD->libOptions('enable_core_files')) {
				$path = $this->POD->libOptions('default_files_path');
				$filePath = $this->POD->siteRoot(false) . "/$path/" . $this->get('id') . "/$name";
			} else {
				$path = $this->POD->libOptions('imgPath');
				$filePath = "$path/" . $this->get('id') . ".$name." . $this->get('extension');
			}

			return $filePath;
		}

		function imageSize($size='original') {

			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array($size));
			}
			
			if ($this->isImage()) { 
				$source_name = $this->POD->libOptions('imgDir') ."/" .  $this->get('id') . "." . $size . "." . $this->get('extension');
				return getimagesize($source_name);
			} else {
				return array(0,0);
			}
		
		}


		function resizeImage($max_width,$square=false,$name=null) { 

			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array($max_width,$square,$name));
			}
			$max_image_size = 3000*3000;



			$this->success = false;
			if ($this->isImage()) { 
	

				// make sure we have the proper functions to handle an image
				if (!function_exists('imagecreatefromjpeg') || !function_exists('imagecreatefrompng') || !function_exists('imagecreatefromgif')) {
					$this->throwError("file->resizeImage() image processing functions not present!");
					$this->error_code = 500;
					return false;
				}		
	
				// locate the source image
				$fileDir = $this->POD->libOptions('imgDir');
				$source_name = "$fileDir/" . $this->get('id') . ".original." . $this->get('extension');

				list($width,$height) = getimagesize($source_name);
	
				if (($width * $height) > $max_image_size) { 
					$this->throwError("{$width}x{$height} is bigger than the maximum size of 3000x3000");
					return false;
				}


				// load the image into memory
				if ($this->get('extension') == "jpg") {
					$source = imagecreatefromjpeg($source_name);
				} else if ($this->get('extension') == "png") {
					$source = imagecreatefrompng($source_name);
				} else if ($this->get('extension') == "gif") {
					$source = imagecreatefromgif($source_name);
				}
				
				$resized = false;


				// if we want a square image, first, we center crop the image into a square
				if ($square) { 
					$xoff = 0;
					$yoff = 0;
					if ($height >= $width) {
						$yoff = intval(($height - $width) / 2);
						$side = $width;
					} else if ($width >= $height) {
						$xoff = intval(($width - $height) / 2);
						$side = $height;
					} else {
						$side = $width;
						$xoff = 0; 
						$yoff = 0;
					}
					
					$dest = imagecreatetruecolor($side,$side);
					imagealphablending($dest, false);
					
					imagecopyresampled($dest,$source,0,0,$xoff,$yoff,$width,$height,$width,$height);
					imagedestroy($source);
					$source = $dest;
					$height = $side;
					$width = $side;
					$resized = true;

				}
				
				// now, we need to resize this image down to the right size.
				if ($width > $max_width) { 
								
					$width_percent = $max_width / $width;
					$newHeight = intval($height * $width_percent);								
					$dest = imagecreatetruecolor($max_width,$newHeight);
					imagealphablending($dest, false);

					imagecopyresampled($dest,$source,0,0,0,0,$max_width,$newHeight,$width,$height);	
					$resized = true;

				}
				
				if (!$resized) { 
					$dest = $source;
				}
				

				if ($name==null) { 
					$name = $max_width;
					if ($square) { 
						$name .="-square";
					}
				}
				
				$resized_name = "$fileDir/" . $this->get('id') . ".{$name}." . $this->get('extension');

				if ($this->get('extension') == "jpg") {
					$res = imagejpeg($dest,$resized_name,100);
				} else if ($this->get('extension') == "png") {
					imagesavealpha($dest, true);
					$res = imagepng($dest,$resized_name,0);
				} else if ($this->get('extension') == "gif") {
					$res = imagegif($dest,$resized_name);
				}	
				

				if (!$res) { 
					$this->throwError("file->resizeImage() Could not create image $resized_name");
					$this->error_code = 500;
					return false;				
				}
				
				$this->success = true;
						
			} else {
				$this->throwError("Can't resize a file that isn't an image.");
			}

			return $this->success;			
		
		}


	
		function createThumbs() {

			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array());
			}
		
			$this->success = false;
			
			// we have different settings for photos when they are attached to user accounts vs posts
			$type = "people";
			if ($this->get('contentId')) {
				$type="document";
			}	
			
			if ($this->POD->libOptions($type . "ImageResize")) { 
				$large_width = $this->POD->libOptions($type . "ImageMaxWidth");
				$small_width = $this->POD->libOptions($type . "IconMaxWidth");
				$square = ($this->POD->libOptions($type."IconSquare")!='');
						
				$this->resizeImage($large_width,false,'resized');
				if (!$this->success()) { 
					return false;
				}
				$this->resizeImage($small_width,$square,'thumbnail');	
				if (!$this->success()) { 
					return false;
				}
			
			}
			
			return true;

		}
	
		function render($template = 'output',$backup_path=null) {
		
			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array($template,$backup_path));
			}
			return parent::renderObj($template,array('file'=>$this),'files',$backup_path);
	
		}
	
		function output($template = 'output',$backup_path=null) {
			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array($template,$backup_path));
			}
		
			parent::output($template,array('file'=>$this),'files',$backup_path);
	
		}
		
	    function mime_content_type($filename) {
			if ($this->hasMethod(__FUNCTION__)) { 
				return $this->override(__FUNCTION__,array($filename));
			}
	
	        $mime_types = array(
	
	            'txt' => 'text/plain',
	            'htm' => 'text/html',
	            'html' => 'text/html',
	            'php' => 'text/html',
	            'css' => 'text/css',
	            'js' => 'application/javascript',
	            'json' => 'application/json',
	            'xml' => 'application/xml',
	            'swf' => 'application/x-shockwave-flash',
	            'flv' => 'video/x-flv',
	
	            // images
	            'png' => 'image/png',
	            'jpe' => 'image/jpeg',
	            'jpeg' => 'image/jpeg',
	            'jpg' => 'image/jpeg',
	            'gif' => 'image/gif',
	            'bmp' => 'image/bmp',
	            'ico' => 'image/vnd.microsoft.icon',
	            'tiff' => 'image/tiff',
	            'tif' => 'image/tiff',
	            'svg' => 'image/svg+xml',
	            'svgz' => 'image/svg+xml',
	
	            // archives
	            'zip' => 'application/zip',
	            'rar' => 'application/x-rar-compressed',
	            'exe' => 'application/x-msdownload',
	            'msi' => 'application/x-msdownload',
	            'cab' => 'application/vnd.ms-cab-compressed',
	
	            // audio/video
	            'mp3' => 'audio/mpeg',
	            'qt' => 'video/quicktime',
	            'mov' => 'video/quicktime',
	
	            // adobe
	            'pdf' => 'application/pdf',
	            'psd' => 'image/vnd.adobe.photoshop',
	            'ai' => 'application/postscript',
	            'eps' => 'application/postscript',
	            'ps' => 'application/postscript',
	
	            // ms office
	            'doc' => 'application/msword',
	            'rtf' => 'application/rtf',
	            'xls' => 'application/vnd.ms-excel',
	            'ppt' => 'application/vnd.ms-powerpoint',
	
	            // open office
	            'odt' => 'application/vnd.oasis.opendocument.text',
	            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
	        );
	
	        $ext = strtolower(array_pop(explode('.',$filename)));
	        if (array_key_exists($ext, $mime_types)) {
	            return $mime_types[$ext];
	        }
	        elseif (function_exists('finfo_open')) {
	            $finfo = finfo_open(FILEINFO_MIME);
	            $mimetype = finfo_file($finfo, $filename);
	            finfo_close($finfo);
	            return $mimetype;
	        }
	        else {
	            return 'application/octet-stream';
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
				
	
} # end of class



		
		



?>