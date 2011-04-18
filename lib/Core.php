<?php 
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* lib/Core.php
* This file defines the core PeoplePod object!
*
* Documentation for this object can be found here:
* http://peoplepods.net/readme/peoplepods-object
/**********************************************/

// where is this library file?  we need it to include subordinate libraries
$libPath = dirname(__FILE__);

// require child libraries
require_once($libPath ."/Comment.php");
require_once($libPath ."/Tag.php");
require_once($libPath ."/Content.php");
require_once($libPath ."/Users.php");
require_once($libPath ."/Groups.php");

require_once($libPath ."/Msg.php");		
require_once($libPath ."/Messages.php");		
require_once($libPath ."/Activity.php");		
require_once($libPath ."/Alerts.php");		

require_once($libPath ."/Files.php");		
require_once($libPath ."/Stack.php");		
	
	
class PeoplePod {

	public $VERSION = 0.9;
	public $DATABASE;

	protected $error_code;
	protected $error;
	protected $success = true;
	protected $USE_CACHE = true;
	protected $USE_SESSIONS = true;
	protected $CACHE_STACKS = false;

	public $PODS; // contains info about installable pods
	
	protected $CSS_FILES = array();
	protected $JS_FILES =  array();
	protected $MESSAGES =  array();

	static protected $CURRENT_USER;
	static protected $IS_AUTHENTICATED = false;

	static private $LIBOPTIONS = array(); // 
	static private $DEBUG = null;  // 1 = debug messages 2 = sql commands
	static private $CACHE = array();

	static private $EXTRA_METHODS = array();
	
	function PeoplePod($PARAMETERS = null) {

		$this->useCache(false);
		
		if ($PARAMETERS == null) {
			$PARAMETERS = array();
		}

		if (isset($PARAMETERS['debug']) && $PARAMETERS['debug'] != "0") {
			$this->debug($PARAMETERS['debug']);
		}			

		if (!isset($PARAMETERS['authSecret']) && isset($_COOKIE['pp_auth'])) {
			$PARAMETERS['authSecret'] = $_COOKIE['pp_auth'];
		}


		$this->tolog("CREATING NEW PEOPLEPOD");

		if ($this->USE_SESSIONS) { 
			// turn on PHP session handling
			if (!session_id() && !session_start()) { 
				$this->USE_SESSIONS = false;
				$this->tolog("Tried to use PHP sessions but failed. Reverting to temporary mem cache");
			} else {
				session_cache_expire(10);
			}
		}		

		// where is this library file?  we need it to include subordinate libraries
		$libPath = dirname(__FILE__);
		
		if (!file_exists("$libPath/etc/options.php") || !include("$libPath/etc/options.php")) { 
			$this->throwError("Install Dir not set!");
			$this->error_code = 100;

			$this->success= false;
			return false;	
		}				
		
			
		if ($this->libOptions('installDir')) { 

			if (!$this->libOptions('default_document_path')) {
				$this->setLibOptions('default_document_path','show');
			}
			if (!$this->libOptions('default_document_editpath')) {
				$this->setLibOptions('default_document_editpath','edit');
			}

			# set up some default options if they haven't already been overridden by plugin pods.
			if (!$this->libOptions('profilePath')) {
				$this->setLibOptions('profilePath','/people');
			}
			if (!$this->libOptions('groupPath')) {
				$this->setLibOptions('groupPath','groups');
			}

			if (!$this->libOptions('imgPath')) {
				$this->setLibOptions('imgPath',$this->libOptions("siteRoot") . $this->libOptions('podRoot') ."/files/images");
			}
			if (!$this->libOptions('docPath')) {
				$this->setLibOptions('docPath',$this->libOptions("siteRoot") . $this->libOptions('podRoot') ."/files/docs");
			}

			if (!$this->libOptions('imgDir')) {
				$this->setLibOptions('imgDir',$this->libOptions("installDir") ."/files/images");
			}
			if (!$this->libOptions('docDir')) {
				$this->setLibOptions('docDir',$this->libOptions("installDir") ."/files/docs");
			}

			if (!$this->libOptions('cacheDir')) {
				$this->setLibOptions('cacheDir',$this->libOptions("installDir") ."/files/cache");
			}
			
			if (!$this->libOptions('currentTheme')) { 
				$this->setLibOptions('currentTheme','default');
			}
			
			$this->setLibOptions('templateDir',$this->libOptions("installDir") ."/themes/" . $this->libOptions('currentTheme'));
	
		} else {
			$this->throwError("Install Dir not set!");
			$this->error_code = 100;

			$this->success= false;
			return false;	
		}

		if ($this->libOptions('mysql_server') && $this->libOptions('mysql_user')) { 
			// connect to the database
			$this->DATABASE = $this->connectToDatabase($this->libOptions('mysql_server'),$this->libOptions('mysql_user'),$this->libOptions('mysql_pass'),$this->libOptions('mysql_db'));
		}
		
		if (!$this->DATABASE) { 
			$this->throwError("Unable to connect to the database " . $this->libOptions('mysql_server') . ' with user ' . $this->libOptions('mysql_user'));
			$this->success= false;
			$this->error_code = 101;
			return false;
		}
		
		if (!isset($PARAMETERS['cache']) || !in_array($PARAMETERS['cache'],array('false',false))) {		
			// now that we're sure everything is configured correctly, we can start using the cache
			$this->useCache(true);
		}
		
		
		// validate user and set auth status
		if (isset($PARAMETERS['authSecret'])) {
			self::$CURRENT_USER = $this->getPerson(array('authSecret'=>$PARAMETERS['authSecret']));
			if (self::$CURRENT_USER->success()) {
					self::$IS_AUTHENTICATED = true;
					$this->tolog("POD Authenticated!");
			} else {
				$this->tolog("POD NOT AUTHENTICATED");
				self::$IS_AUTHENTICATED = false;
				self::$CURRENT_USER = null;
			}
		} else {
			self::$IS_AUTHENTICATED = false;
			self::$CURRENT_USER = null;
		}

		$this->processIncludes();
		

		if (isset($PARAMETERS['lockdown']) && $PARAMETERS['lockdown'] == "login" && !self::$IS_AUTHENTICATED) {
			$this->tolog('POD LOGIN REQUIRED!');
			header("Location: " . $this->libOptions('siteRoot') . "/join?error=access+denied");	
			exit;

			$this->success = false;
		}

		if (isset($PARAMETERS['lockdown']) && $PARAMETERS['lockdown'] == "adminUser" && (!self::$IS_AUTHENTICATED || (self::$IS_AUTHENTICATED && $this->currentUser()->get('adminUser') == ''))) {
			$this->tolog('POD ADMINUSER REQUIRED!');
			header("Location: " . $this->libOptions('siteRoot') . "/login");	
			exit;
			$this->success = false;
		}

		if (isset($PARAMETERS['lockdown']) && $PARAMETERS['lockdown'] == "verified" && (!self::$IS_AUTHENTICATED || (self::$IS_AUTHENTICATED && $this->currentUser()->get('verificationKey') != ''))) {
			$this->tolog('POD VERIFICATION REQUIRED!');

			header("Location: " . $this->libOptions('siteRoot') . "/verify");	
			exit;
			$this->success = false;
		}

	}



	// load up any include files that were specified by plugin pods.
	// these included files add methods to the core objects
	function processIncludes() {
		foreach(self::$LIBOPTIONS as $field=>$val) { 
			if (preg_match('/include_(.*)/',$field,$matches)) {
				$pod = $matches[1];
				if ($this->libOptions('enable_'.$pod)) { 
					$this->tolog("POD: Booting {$pod} methods");
					require_once($val);
				}
			}	
		}
	}
	
	// find all the PODS that are installed on this server.
	// each POD has a settings.php that calls $POD->registerPOD with options.
	function loadAvailablePods() {
		// set the $POD variable so the settings files have it in scope.
		$POD=$this;

		$podInstallDir = opendir($this->libOptions('installDir') . "/pods/");
		while ($pod = readdir($podInstallDir)) {
			if (file_exists($this->libOptions('installDir') . "/pods/$pod/settings.php")) { 
				require($this->libOptions('installDir') . "/pods/$pod/settings.php");
			}
		}	 
	}
	
	// check to see if a pod is enabled
	function isEnabled($name) {
	
		return ($this->libOptions('enable_'.$name)=='true');
	}
	
	// turn on a single pod
	function enablePOD($name) {
	
		$POD = $this;
		$podling = $this->PODS[$name];
		$message = '';
		// has this pod requested an additional include file?
		if ($podling['include']) {
			$POD->setLibOptions('include_' . $name,$podling['include']);
			require_once($podling['include']);
		}

		if (!$POD->libOptions('enable_' . $name)) {
			if ($podling['installFunction']) { 
				$func = $podling['installFunction'];
				$message .= $func($POD);
			}	
		}
		
		$POD->setLibOptions('enable_' . $name,'true'); // tell the library this is turned on
		
		// has this pod specified 
		if ($podling['settings']) {
			$POD->setLibOptions('settings_' . $name,$podling['settings']);
		}
		
 		// add any variables that were requested by any enabled plugins
		foreach ($podling['libOptions'] as $option => $value) {
			$POD->setLibOptions($option,$value);
		}
		
		return $message;
	}
	
	// turn off a single pod
	function disablePOD($name) {

		$POD = $this;
		$podling = $this->PODS[$name];
		$message = '';

		$POD->setLibOptions('enable_' . $name,null);
		
		if (!$POD->libOptions('enable_' . $name)) {
			if ($podling['uninstallFunction']) { 
				$func = $podling['uninstallFunction'];
				$message .= $func($POD);
			}	
		}
		
		// if this pod is not turned on, purge its variables from our system.
		foreach ($podling['libOptions'] as $option => $value) {
			$POD->setLibOptions($option,null);
		}		
		return $message;

	}

	function writeHTACCESS($htaccessPath) {	

		$this->success = false;
		// iterate through each pod we know about.
	 	foreach ($this->PODS as $name => $podling) {
			if ($this->libOptions('enable_' . $name)) {
				foreach ($podling['rules'] as $pattern => $rewrite) {
					$REWRITE_RULES .= "\n# $name\n";			
					$rewrite = $this->libOptions('siteRoot') . $this->libOptions('podRoot') . "/pods/" . $rewrite;
					$REWRITE_RULES .= "RewriteRule $pattern\t$rewrite\t[QSA,L]\n";
				} 
			}
		}

		// create the .htaccess file
		$handle = fopen("$htaccessPath/.htaccess","r");
		if ($handle) {
			// if an .htaccess file already exists, find the current peopelpods rules and get rid of them.
			$htaccess = fread($handle,100000);
			fclose($handle);
			
			// find peoplepods chunk				
			preg_match("/(# BEGIN PEOPLEPODS RULES.*?# END PEOPLEPODS RULES)/is",$htaccess,$matches);
			if ($matches[1]) {
				$peoplepods_rules = $matches[1];
				$htaccess = preg_replace("/# BEGIN PEOPLEPODS RULES.*?# END PEOPLEPODS RULES/is","",$htaccess);			
			}
		} else {
			$this->throwError("Can't open .htaccess file!  Check file permissions on " . $htaccessPath . "/.htaccess"); 
			return $this->error();
		}
				
		$REWRITE_RULES = "# BEGIN PEOPLEPODS RULES\n#####################################\n" .
					 "# turn the RewriteEngine on so that these fancy rewrite rules work\nRewriteEngine On\n" .
					 $REWRITE_RULES .
					 "\n#####################################\n# END PEOPLEPODS RULES";

		$handle = fopen("$htaccessPath/.htaccess","w");
		if (!fwrite($handle,$REWRITE_RULES . "\n\n" . $htaccess )) {
			$this->throwError("Can't open .htaccess file!  Check file permissions on " . $htaccessPath . "/.htaccess"); 
			return $this->error();
		} else {
			$this->success = true;
			return "Successfully wrote to .htaccess";
		}

	}


	function connectToDatabase($mysql_server,$mysql_user,$mysql_pass,$mysql_db) {
		
		$db_handle = @mysql_pconnect($mysql_server,$mysql_user,$mysql_pass);
		if (!$db_handle) { return null; }
		if (@mysql_select_db($mysql_db,$db_handle)) { 
				return $db_handle;
		} else {
			return null;
		}
	}
	
/********************************************************************************************/
/* Plugin methods
// these functions allow plugin pods to add methods to the $POD object.
// and make themselves known to the setup tool
/*********************************************************************************************/

	function registerPOD($name,$description,$rewriteRules,$libOptions=null,$include=null,$settings=null,$installFunction=null,$uninstallFunction=null) {
		$this->PODS[$name]['name'] = $name;
		$this->PODS[$name]['description'] = $description;
		$this->PODS[$name]['rules'] = $rewriteRules;
		$this->PODS[$name]['libOptions'] = $libOptions;
		$this->PODS[$name]['include'] = $include;
		$this->PODS[$name]['settings'] = $settings;
		$this->PODS[$name]['installFunction'] = $installFunction;
		$this->PODS[$name]['uninstallFunction'] = $uninstallFunction;

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
		
	
/********************************************************************************************/
/* Buffer Control */
/********************************************************************************************/

	function startBuffer() {
		ob_start();
	}

	function endBuffer() { 
	
		$html = ob_get_contents();
		ob_end_clean();
		return $html;	
	}		


/********************************************************************************************/
/* Template Output */
/********************************************************************************************/
	
	function output($template,$backup_path=null) { 
	
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($template));
		}
		
		$POD = $this;
		if (file_exists($this->libOptions('templateDir') . "/$template.php")) {
			include($this->libOptions('templateDir') . "/$template.php");
		} else if ($backup_path && file_exists($backup_path."/{$template}.php")) { 
			include($backup_path . "/{$template}.php");
		} else {
			$this->tolog("ERROR: Tried to use template $template but could not find it.");
		}
	
	}
	
	

	function addCSS($file,$path=null) {
		if (!$path) { 
			$path = $this->templateDir(false);
		}		
		$this->CSS_FILES[] = "{$path}/{$file}";
	} 
	
	function addJS($file,$path=null) { 
		if (!$path) { 
			$path = $this->templateDir(false);
		}		
		$this->JS_FILES[] = "{$path}/{$file}";
	}
	
	function extraCSS() {
		foreach ($this->CSS_FILES as $file) { 
			echo '<link rel="stylesheet" type="text/css" href="' . $file . '" />' . "\n";
		}
	}

	function extraJS() {
		foreach ($this->JS_FILES as $file) { 
			echo '<script type="text/javascript" src="' . $file . '"></script>' . "\n";
		}
	}
	
	
	function addMessage($message) {
		$this->MESSAGES[] = $message;
	}
	
	function messages() {
		return $this->MESSAGES;
	}




/*********************************************************************************************
* Cache Helpers
*********************************************************************************************/

	function useCache($bool=null) { 
		if (!($bool===null)){
			$this->USE_CACHE= $bool;
		}
		return $this->USE_CACHE;
	}


	function cachestatus() { 
	
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array());
		}


		echo "<strong>Cache Status:</strong> ";
		if ($this->USE_SESSIONS) { 
			echo sizeof($_SESSION['cache']['facts']) . " facts, " . sizeof($_SESSION['cache']['data']) . " objects, ";
		} else {
			echo sizeof(self::$CACHE['facts']) . " facts, " . sizeof(self::$CACHE['data']) . " objects, ";		
		}
		
		$count = 0;
		$cacheDir = $this->libOptions('cacheDir');
		$dir = opendir($cacheDir);
		if ($dir) { 
			while ($file = readdir($dir)) {
				if (!is_dir($cacheDir . "/" . $file)) {
					$count++;
				}
			}
		}		
		echo "$count files";
		if ($this->USE_SESSIONS) { 
			echo " using PHP sessions";
		} else {
			echo " temporary memory cache";
		}
	}	
		

	function cacheflush() {
	

		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array());
		}

		// first, clear the memory cache
		if ($this->USE_SESSIONS) { 
			foreach ($_SESSION['cache'] as $key => $value) { 
				$_SESSION['cache'][$key] = array();			
			}
			session_destroy();
		} else {
			foreach (self::$CACHE as $key => $value) { 
				self::$CACHE[$key] = array();			
			}
		}
		
		// now, clear the file cache.
		$cacheDir = $this->libOptions('cacheDir') . "/";
		$dir = opendir($cacheDir);
		if ($dir) { 
			while ($file = readdir($dir)) {
				if (!is_dir($cacheDir . $file)) {
					unlink($cacheDir . $file);
				}
			}
		}		
	}
	
	function cachew($area,$key=null,$value) { 
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($area,$key,$value));
		}


		if ($this->USE_CACHE) { 
			if ($this->USE_SESSIONS) { 
				if (!isset($_SESSION['cache'])) { 
					// set up the cache for first use
					$_SESSION['cache'] = array();
					$_SESSION['cache']['data'] = array();
					$_SESSION['cache']['facts'] = array();
					array_push($_SESSION['cache']['data'],0);
				}
				
				if ($key == null) { 
					$ret = array_push($_SESSION['cache'][$area],$value);
					$ret--;
				} else {
					$ret = $_SESSION['cache'][$area][$key] = $value;
				}
				return $ret;
			} else {
				if (!self::$CACHE) { 
					self::$CACHE['data'] = array();
					self::$CACHE['facts'] = array();
					array_push(self::$CACHE['data'],0);
				}
				if ($key==null) { 
					$ret = array_push(self::$CACHE[$area],$value);
					$ret--;
				} else {
					$ret = self::$CACHE[$area][$key] = $value;			
				}
				return $ret;
			}
		} else {
			return null;
		}
	}
	
	function cacher($area,$key) {
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($area,$key));
		}


		if ($this->USE_CACHE) {  
			if ($this->USE_SESSIONS) { 
				if (isset($_SESSION['cache'][$area][$key])) { 
					return $_SESSION['cache'][$area][$key];
				} else {	
					return null;
				}
			} else {
				if (isset(self::$CACHE[$area][$key])) {
					return self::$CACHE[$area][$key];
				} else {
					return null;
				}
			}
		} else {
			return null;
		}	
	}
	
	
	// this function is only called by the install script to check and make sure
	// that the install script is not run after its supposed to be
	function hasAdminUser() { 
	
		$sql = "SELECT count(1) as adminUsers FROM users inner join meta on users.id=meta.itemId and meta.type='user' and meta.name='adminUser';";
		$res = mysql_query($sql,$this->DATABASE);
		$v = mysql_fetch_assoc($res);
		if ($v['adminUsers'] > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	function cachefact($fact,$value) { 
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($message));
		}


		$this->tolog("Caching $fact = $value");
		$this->cachew('facts',$fact,$value);
	}
	
	function factcache($fact) { 
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($fact));
		}


		$val = $this->cacher('facts',$fact);
		if ($val === null) { 
			return null;
		} else {

			$this->tolog("Cache found fact $fact = $val");
			return $val;

		}

	}
	
	
	
	function cacheclear($obj) {
	
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($obj));
		}

		if ($obj->get('id')) {
			$type = get_class($obj);
			if ($type == "Person") {
				if ($cachekey = $this->cacher('facts','users-id-' . $obj->get('id'))) {
					$this->tolog("DELETING FROM PERSON CACHE");
					$this->cachew('data',$cachekey,null);
					$this->cachew('facts','users-id-' . $obj->get('id'),null);

				} 
			}
			if ($type == "Content") {
				if ($cachekey = $this->cacher('facts','content-id-' . $obj->get('id'))) {
					$this->cachew('data',$cachekey,null);
					$this->cachew('facts','content-id-' . $obj->get('id'),null);
					$this->cachew('facts','content-stub-' . $obj->get('stub'),null);

				} 	
			}
			if ($type=="Group") {
			
				if ($cachekey = $this->cacher('facts','groups-id-' . $obj->get('id'))) {
					$this->cachew('data',$cachekey,null);
					$this->cachew('facts','groups-id-' . $obj->get('id'),null);
					$this->cachew('facts','groups-stub-' . $obj->get('stub'),null);

				} 
			}

			if ($type=="File") {
			
				if ($cachekey = $this->cacher('facts','file-id-' . $obj->get('id'))) {
					$this->cachew('data',$cachekey,null);
					$this->cachew('facts','file-id-' . $obj->get('id'),null);
				} 
			}

			if ($type=="Comment") {
			
				if ($cachekey = $this->cacher('facts','comment-id-' . $obj->get('id'))) {
					$this->cachew('data',$cachekey,null);
					$this->cachew('facts','comment-id-' . $obj->get('id'),null);
				} 
			}

		}	
	}
		
	function cachestore($obj) {

		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($obj));
		}

		if ($obj->get('id')) {
			$type = get_class($obj);
			if ($type == "Person") {
				if ($cachekey = $this->cacher('facts','users-id-' . $obj->get('id'))) {
					$this->cachew('data',$cachekey,$obj->DATA);
				} else {
					$this->tolog("INSERTING INTO PERSON CACHE");
					$cachekey = $this->cachew('data',null,$obj->DATA);
					$this->cachew('facts','users-id-' . $obj->get('id'),$cachekey);
					$this->cachew('facts','users-nick-' . $obj->get('nick'),$cachekey);
					$this->cachew('facts','users-stub-' . $obj->get('stub'),$cachekey);
					$this->cachew('facts','users-auth-' . $obj->get('authSecret'),$cachekey);

				}
			}
			if ($type == "Content") {
				if ($cachekey = $this->cacher('facts','content-id-' . $obj->get('id'))) {
					$this->cachew('data',$cachekey,$obj->DATA);
				} else {
					$this->tolog("INSERTING INTO CONTENT CACHE");
					$cachekey = $this->cachew('data',null,$obj->DATA);
					$this->cachew('facts','content-id-' . $obj->get('id'),$cachekey);
					$this->cachew('facts','content-stub-' . $obj->get('stub'),$cachekey);

				}			
			}
			if ($type == "File") {
				if ($cachekey = $this->cacher('facts','file-id-' . $obj->get('id'))) {
					$this->cachew('data',$cachekey,$obj->DATA);
				} else {
					$this->tolog("INSERTING INTO FILE CACHE");
					$cachekey = $this->cachew('data',null,$obj->DATA);
					$this->cachew('facts','file-id-' . $obj->get('id'),$cachekey);
				}			
			}
			if ($type == "Comment") {
				if ($cachekey = $this->cacher('facts','comment-id-' . $obj->get('id'))) {
					$this->cachew('data',$cachekey,$obj->DATA);
				} else {
					$this->tolog("INSERTING INTO COMMENT CACHE");
					$cachekey = $this->cachew('data',null,$obj->DATA);
					$this->cachew('facts','comment-id-' . $obj->get('id'),$cachekey);
				}			
			}
			if ($type=="Group") {
			
				if ($cachekey = $this->cacher('facts','groups-id-' . $obj->get('id'))) {
					$this->cachew('data',$cachekey,$obj->DATA);
				} else {
					$this->tolog("INSERTING INTO GROUP CACHE");
					$cachekey = $this->cachew('data',null,$obj->DATA);
					$this->cachew('facts','groups-id-' . $obj->get('id'),$cachekey);
					$this->cachew('facts','groups-stub-' . $obj->get('stub'),$cachekey);

				}			
			
			}
		}	
	}
	
	
	function checkcache($type,$key,$value) {
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($type,$key,$value));
		}

		if ($type == "Person") {
		
			if ($cachekey = $this->cacher('facts',"users-$key-$value")) {
				$this->tolog("core->checkcache(): USER FOUND");
				return $this->refreshCachedObjectTimestamps($this->cacher('data',$cachekey));
			} else {
				return null;
			}
		
		}
		if ($type == "Content") {
		
			if ($cachekey = $this->cacher('facts',"content-$key-$value")) {
				$this->tolog("core->checkcache(): CONTENT FOUND");
				return $this->refreshCachedObjectTimestamps($this->cacher('data',$cachekey));
			} else {
				return null;
			}
		
		}
		if ($type == "File") {
		
			if ($cachekey = $this->cacher('facts',"file-$key-$value")) {
				$this->tolog("core->checkcache(): FILE FOUND");
				return $this->refreshCachedObjectTimestamps($this->cacher('data',$cachekey));
			} else {
				return null;
			}
		
		}
		if ($type == "Comment") {
		
			if ($cachekey = $this->cacher('facts',"comment-$key-$value")) {
				$this->tolog("core->checkcache(): COMMENT FOUND");
				return $this->refreshCachedObjectTimestamps($this->cacher('data',$cachekey));
			} else {
				return null;
			}
		
		}
		if ($type == "Group") {
		
			if ($cachekey = $this->cacher('facts',"groups-$key-$value")) {
				$this->tolog("core->checkcache(): GROUP FOUND");
				return $this->refreshCachedObjectTimestamps($this->cacher('data',$cachekey));
			} else {
				return null;
			}
		
		}
	}




	function cacheHasExpired($file,$minutes = 60) { // cache expires by default after 1 hour
	
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($file,$minutes));
		}


		if ($this->USE_CACHE) { 
			$cacheDir = $this->libOptions('cacheDir') . "/";
		
			if (file_exists($cacheDir . $file)) { 
				if (time() - filemtime($cacheDir . $file) > ($minutes * 60)) {
				
					return true;
				
				} else { 
				
					return false;
				}
			} else {
				return true;
			}
		} else {
			return true;
		}	
	}

	function loadCache($file) {


		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($file));
		}

		if ($this->USE_CACHE) { 
			$cacheDir = $this->libOptions('cacheDir') . "/";
			if ($val = $this->factCache($file)) { 
				$this->tolog("File Cache: Found in memory $file");
				return $val;
			} 
			$this->tolog("File Cache: Loading $file");
		
			$handle = fopen($cacheDir . $file,"r");
			$contents = fread($handle, filesize($cacheDir.$file));
			fclose($handle);
			$this->cacheFact($file,$contents);
			return $contents;
		} else {
			return null;
		}
	}

	function writeCache($file,$contents) { 
		if ($this->hasMethod(__FUNCTION__)) { 
			return $this->override(__FUNCTION__,array($file,$contents));
		}

		
		if ($this->useCache()) { 
			$cacheDir = $this->libOptions('cacheDir') . "/";
			$this->tolog("File Cache: Writing $file");
			$handle = @fopen($cacheDir . $file,"w");
			if ($handle) { 
				fwrite($handle, $contents);
				fclose($handle);
			} else {
				$this->tolog("File Cache: Could not write to $file");
			}	
			$this->cacheFact($file,$contents);
		}
	}	

		function refreshCachedObjectTimestamps($data) { 
		
			if (isset($data['date']) && $data['date']!='') {
				$data['minutes'] = intval(time() - strtotime($data['date'])) / 60;
				$data['timesince'] = $this->timesince($data['minutes']);
			}	
			return $data;
		}
	
/*********************************************************************************************
* Library Options
*********************************************************************************************/
	
	
	
	function saveLibOptions($force = false) {
	
		$this->success = false;
		if (!$force) { 
			if (!self::$IS_AUTHENTICATED || !$this->currentUser()->get('adminUser')) { 
				$this->throwError("Access Denied.  Only admin users can save library options.");
				return false;
			}
		}
				
		$file = $this->libOptions('etcPath') . "/options.php";
		$fh = fopen($file, 'w');
		if (!$fh) { $this->throwError("Can't open config file!  Check file permissions on " . $this->libOptions('etcPath') . "/options.php"); return false; }
		fwrite($fh,"<?\n");
		fwrite($fh,"// This file was automagically generated by PeoplePods\n");
		ksort(self::$LIBOPTIONS);
		foreach (self::$LIBOPTIONS as $key => $value) { 
			if ($value != '') { 
				$value = preg_replace("/\'/","\\\'",$value);
				fwrite($fh,"\$this->setLibOptions('$key','$value');\n");
			}
		}
		fwrite($fh,"?>\n");
		fclose($fh);
		$this->success = true;
		return true;
	}
	
	
	function setLibOptions($key,$value) {
		self::$LIBOPTIONS[$key] = $value;
		return true;		
	}
	
	function libOptions($key) {
		if (isset(self::$LIBOPTIONS[$key])) {
			return self::$LIBOPTIONS[$key];
		} else {
			return false;
		}
	}



/*********************************************************************************************
* Theme 
*********************************************************************************************/


	
	function currentTheme() { 
		return $this->libOptions('currentTheme');
	}
	function changeTheme($newTheme) { 
		if ($this->doesThemeExist($newTheme)) { 
			$this->setLibOptions('currentTheme',$newTheme);
			$this->setLibOptions('templateDir',$this->libOptions("installDir") ."/themes/" . $this->libOptions('currentTheme'));
		} else {
			$this->throwError('Theme $newTheme does not exist');
		}
	}

	function doesThemeExist($theme){
	
	        $path= dirname(__FILE__);
	        $file= $path."/../themes/$theme";
	        
	        if(file_exists($file)){
	            $msg= true;
	        }
	        else $msg= false;
	
	        return $msg;
	}


	
/*********************************************************************************************
* People Helpers
*********************************************************************************************/
	
		
	function changeActor($PARAMETERS) { 
		self::$CURRENT_USER = $this->getPerson($PARAMETERS);
		if ($this->currentUser()->success()) {
			self::$IS_AUTHENTICATED = true;
			$this->success = true;
			return $this->currentUser();
		} else {
			self::$IS_AUTHENTICATED = false;
			$this->success = false;
			$this->throwError($this->currentUser()->error());
			return null;
		}
	}



	function getPerson($PARAMETERS=null) {
		return new Person($this,$PARAMETERS);
	}
	
	function getPeople($conditions=null,$sort="u.lastVisit DESC",$count=20,$offset=0,$cacheAs=null) {
		if ($conditions == null) {
			$conditions['1']=1;
		}
		return new Stack($this,'user',$conditions,$sort,$count,$offset,null,null,null,$cacheAs);
	}

	function getPeopleByVote($doc,$vote,$sort='flag.date DESC',$count=20,$offset=0) {
		return new Stack($this,'user',array('flag.name'=>'vote','flag.type'=>'content','flag.itemId'=>$doc->get('id'),'flag.value'=>$vote),$sort,$count,$offset);
	}
		
	function getPeopleByFavorite($doc,$sort='flag.date DESC',$count=20,$offset=0) {
		return new Stack($this,'user',array('flag.name'=>'favorite','flag.type'=>'content','flag.itemId'=>$doc->get('id')),$sort,$count,$offset);
	}

	function getPeopleByWatching($doc,$sort='flag.date DESC',$count=20,$offset=0) {
		return new Stack($this,'user',array('flag.name'=>'watching','flag.type'=>'content','flag.itemId'=>$doc->get('id')),$sort,$count,$offset);
	}
	

	function getMessage($PARAMETERS =null) { 
		return new Message($this,$PARAMETERS);
	}


	function getInbox($count=20,$offset=0) {
		return new Inbox($this,$count,$offset);
	}

	function getActivity($PARAMETERS = NULL) { 
		return new Activity($this,$PARAMETERS);
	}
	
	function getActivityStream($PARAMETERS=null,$sort="a.date DESC",$count=20,$offset=0) {
		return new Stack($this,'activity',$PARAMETERS,$sort,$count,$offset);
	}



	function getAlert($PARAMETERS = NULL) { 
		return new Alert($this,$PARAMETERS);
	}
	
	function getAlerts($PARAMETERS=null,$sort="x.date DESC",$count=20,$offset=0) {
		return new Stack($this,'alert',$PARAMETERS,$sort,$count,$offset);
	}


	function isValidInvite($code) { 
		if (preg_match("/[a-zA-Z0-9]{32}/",$code)) {
			$sql = "SELECT * FROM invites WHERE code='" . htmlspecialchars($code) ."'";
			$res = mysql_query($sql,$this->DATABASE);
			return mysql_fetch_assoc($res);
		}
		
		return false;
	}
	
/*********************************************************************************************
* Content Helpers
*********************************************************************************************/

	function getFile($PARAMETERS = null) { 
		return new File($this,$PARAMETERS);
	}	
	function getFiles($PARAMETERS = null,$sort="f.date DESC",$count=20,$offset=0) {
		if ($PARAMETERS == null) {
			$PARAMETERS['1']=1;
		}	
		return new Stack($this,'file',$PARAMETERS,$sort,$count,$offset);
	}
	function getContentStack($PARAMETERS=null,$sort="d.date DESC",$count=20,$offset=0) { 
		return $this->getContents($PARAMETERS,$sort,$count,$offset);
	}


	function getContent($PARAMETERS = null) {
	
		return new Content($this,$PARAMETERS);
	
	}
	
	function getContents($PARAMETERS = null,$sort="d.date DESC",$count=20,$offset=0) {
		if ($PARAMETERS == null) {
			$PARAMETERS['1']=1;
		}	
		return new Stack($this,'content',$PARAMETERS,$sort,$count,$offset);
	
	}
	
	function getFlagList() {
	
		$sql = " SELECT  name,type,count(1) as count FROM flags group by type,name order by type,name";
		$results = $this->executeSQL($sql);
		$list = array();
		
		if ($results) {
			while ($res = mysql_fetch_assoc($results)) { 
				$list[] = $res;
			}
		}	
	
		return $list;
	}


	function executeSQL($sql) { 
	
		$this->tolog($sql,2);
		$results = mysql_query($sql,$this->DATABASE);
		if (!$results) {
			$this->throwError("SQL Error in Query: " . mysql_error() . " QUERY: $sql");
			return null;	
		} else {
			return $results;
		}
	
	}


	function getTagCount($conditions=null,$sort="t.count DESC",$count = 100, $offset=0) { 
		if ($conditions==null) {
			$conditions['tr.type'] = 'content';
		}
//		$from = "FROM tags t inner join tagRef tr on t.id=tr.tagId";
		$select = "SELECT t.*,count(tr.itemId) as count,(TIME_TO_SEC(TIMEDIFF(NOW(),t.date)) / 60) as minutes ";

		return new Stack($this,'tag',$conditions,$sort,$count,$offset,null,$select,'t.id');		
	}

	function getTags($conditions=null,$sort="t.date ASC",$count = 100, $offset=0) { 
		if ($conditions==null) {
			$conditions['1']=1;
		}
		return new Stack($this,'tag',$conditions,$sort,$count,$offset);		
	}

	function getTag($PARAMETERS=null) { 
		return new Tag($this,$PARAMETERS);
	}


	function getComments($conditions=null,$sort="c.date ASC",$count = 100, $offset=0,$cacheAs=null) { 
		if ($conditions==null) {
			$conditions['1']=1;
		}
		return new Stack($this,'comment',$conditions,$sort,$count,$offset,null,null,null,$cacheAs);		
	}

	function getComment($PARAMETERS=null) { 
		return new Comment($this,$PARAMETERS);
	}



/*********************************************************************************************
* Group Helpers
*********************************************************************************************/

	
	function getGroup($PARAMETERS = null) {
		return new Group($this,$PARAMETERS);
	}
	
	function getGroups($conditions=null,$sort="g.date ASC",$count=20,$offset=0) {
		if ($conditions==null) { 
			$conditions['1']='1';
		}
		return new Stack($this,'group',$conditions,$sort,$count,$offset);
	}
	
	
/*********************************************************************************************
* Accessors
*********************************************************************************************/

	function errorCode() { 
		return $this->error_code;
	}
	
	function throwError($error) {
		$this->error = $error;
		error_log("Error [POD]: $this->error");		
	}
	
	function error() {	
		return $this->error;
	}

	function success() { 
		return $this->success;
	}

	function currentUser() {
		return self::$CURRENT_USER;	
	}

	function isAuthenticated() { 
		return self::$IS_AUTHENTICATED;
	}

	function siteName($echo = true) {
		if ($echo ) { 
			echo $this->libOptions('siteName');	
		} else {
			return $this->libOptions('siteName');
		}
	}
	function templateDir($echo = true) { 
		
		$dir = $this->libOptions('server') . $this->libOptions('siteRoot') . $this->libOptions('podRoot') . '/themes/' . $this->libOptions('currentTheme');
	
		if ($echo) {
			echo $dir;
		} else {
			return $dir;
		}
	
	}
	
	function podRoot($echo = true) {
		if ($echo) { 
			echo $this->libOptions('server');
			echo $this->libOptions('siteRoot');
			echo $this->libOptions('podRoot');	
		} else {
			return $this->libOptions('server') . $this->libOptions('siteRoot') . $this->libOptions('podRoot');
		}
	}
	function siteRoot($echo = true) {
		if ($echo) { 
			echo $this->libOptions('server');
			echo $this->libOptions('siteRoot');
		} else {
		
			return $this->libOptions('server') . $this->libOptions('siteRoot');
		}	
	}

/*********************************************************************************************
* Formatting Helpers
*********************************************************************************************/


	function header($pagetitle = null,$feedurl=null) {
		
		$POD = $this;
		include($this->libOptions('templateDir') . "/header.php");
	
	}
	
	function footer() {

		$POD=$this;
		include($this->libOptions('templateDir') . "/footer.php");
	
	}
	
	function timesince($minutes) {
		$str = "";
		$hours = 0;
		$days = 0;
		if ($minutes > 60) {
		
			$hours = intval($minutes / 60);
			$minutes = intval($minutes % 60);
			if ($hours > 24) {
				$days = intval($hours / 24);
				$hours = intval($hours % 24);
				$str = $this->pluralize($days,'@number day','@number days');
				if ($days > 30) { 
					$weeks = intval($days / 7);
					$str = $this->pluralize($weeks,'@number week','@number weeks');				
				}
				if ($days > 365) {
					$years = intval($days / 365);
					$str = $this->pluralize($years,'@number year','@number years');				
				}
			} else {		
				$str = $this->pluralize($hours,'@number hour','@number hours');
			}
		} else {
			$minutes=intval($minutes);
			$str = $this->pluralize($minutes,'@number minute','@number minutes');
		}
		$str .= ' ago';
		return $str;
	}

	
	function pluralize($num,$singular,$plural,$zero=null) {
		
		if ($num == 1) {
			return preg_replace("/@number/",$num,$singular);
		} else if (isset($zero) && $num == 0) {
			return preg_replace("/@number/",$num,$zero);
		} else {
			return preg_replace("/@number/",$num,$plural);
		}
	}
		
	function tokenize($string) { 
		return strtolower(strip_tags(preg_replace("/\W/","_",preg_replace("/\s+/","_",$string))));
	}


	function debug($level) { 
		$this->tolog("Setting debug level to $level");
		self::$DEBUG = $level;
	}

	function tolog($message,$level=1) {
		if (self::$DEBUG == $level) { 
			if ($this->isAuthenticated()) { 
				error_log("DEBUG: " . $this->currentUser()->get('nick') . ": $message");
			} else {
			 	error_log("DEBUG: $message");
			 }
		}
		if (self::$DEBUG == 3) {
			echo "DEBUG: $message<br />\n";
		}
		if (self::$DEBUG == 4) {
			error_log("DEBUG: $message");
		}

	}


	function formatText($string,$add_p_tags=true) {
		if (!$this->isFormattedText($string)) { 
			$string = preg_replace("/\r/","",$string);
			
			$string = preg_replace("/^([a-z]*\:\/\/.*?)$/m",'<a href="$1">$1</a>',$string);
			$string = preg_replace("/\n\n/","</p><p>",$string);
			$string = preg_replace("/\n/","<br />",$string);
	
			if ($add_p_tags) { 
				$string = "<p>$string</p>";
			}
		}
		return $string;
	}

	// a VERY simple test to see if we are dealing with formatted "Body" text.
	// if it starts with a <p> tag, then TRUE.
	// else, false.
	function isFormattedText($string) { 
	
		return preg_match("/^\<p/i",$string);
	}	
	
	function shorten($string,$chars = 25) {

    // Change to the number of characters you want to display
    	$string = preg_replace("/<.*?>/","",$string);
		$text = $string;
        $text = $text." ";

        $text = substr($text,0,$chars);

        $text = substr($text,0,strrpos($text,' '));
		if ($text != $string) { 
	        $text = $text."...";
	    }
        return $text;

    }

	function sanitizeInput($str) { 
	
		$str = strip_tags($str,'<p><pre><blockquote><h1><h2><h3><h4><h5><h6><ol><ul><li><dl><dt><dd><table><tr><td><th><br><em><strong><i><u><b><strike><a><img><object><param><embed><div>');
		return $str;
	
	}


	function handleUTF8($str) {

	// make sure whatever string we have is encoded in utf8
	  $cur_encoding = mb_detect_encoding($str) ;
	  if($cur_encoding == "UTF-8" && mb_check_encoding($str,"UTF-8"))
   		 return $str;
	  else
    	return utf8_encode($str); 
	}

	// this code kindly submitted by Cory Heart
	function GetVideoEmbedCode($url, $width, $height, $fullScreen, $scriptAccess)
		{
			$obj = parse_url($url);
			$garbage = array(".", "www", "com", "video");
			$host =	str_replace($garbage, "", $obj['host']);
			
			$vUrl = "";
			
			switch (strtolower($host))
			{
				case "youtube":
					$vUrl = $this->GetYouTubeVideoUrl($url);
				break;
				case "vimeo":
					$vUrl = $this->GetVimeoVideoUrl($url);
				break;
				case "veoh":
					$vUrl = $this->GetVeohVideoUrl($url);
				break;
				case "google":
					$vUrl = $this->GetGoogleVideoUrl($url);
				break;
				
				default:
					return NULL;
				break;
			}
			
			return $this->GetEmbedCode($vUrl, $width, $height, $fullScreen, $scriptAccess);
		}
		
		function GetEmbedCode($url, $width, $height, $fullScreen, $scriptAccess)
		{
			if ($url == NULL)
				return NULL;
				
			$code = "<object width=\"$width\" height=\"$height\">
					 <param name=\"movie\" value=\"$url\"></param>
					 <param name=\"allowFullScreen\" value=\"$fullScreen\"></param>
					 <param name=\"allowscriptaccess\" value=\"$scriptAccess\"></param>
					 <embed src=\"$url\"  allowFullScreen=\"$fullScreen\" allowScriptAccess=\"$scriptAccess\" type=\"application/x-shockwave-flash\"  width=\"$width\" height=\"$height\"></embed>
					 </object>";
			return $code;
		}
		
		// Ideally, for these methods, you'll want to extract the video ID and hardcode the actual url you want to use.
		// This is just a temporary solution
		
		function GetYouTubeVideoUrl($url)
		{
			$url = preg_replace("/\&.*/","",$url);
			$url = str_replace("watch?v=", "v/", $url);
			$url .= "&hl=en&fs=1";
			return $url;
		}
		function GetVimeoVideoUrl($url)
		{
			return str_replace(".com/", ".com/moogaloop.swf?clip_id=", $url);
		}
		function GetVeohVideoUrl($url)
		{
			return str_replace("videos/", "veohplayer.swf?permalinkId=", $url);
		}
		function GetGoogleVideoUrl($url)
		{
			return str_replace("videoplay", "googleplayer.swf", $url);
		}



} // EOP
		


?>
