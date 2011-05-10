<?php
  	require_once('external/Symfony/Component/EventDispatcher/EventDispatcher.php');
  	require_once('external/Symfony/Component/EventDispatcher/EventDispatcherInterface.php');
	require_once('external/Symfony/Component/EventDispatcher/Event.php');

	/* Wraps a dispatcher class, currently the Symphony 2 EventDispatcher class
	* All users will share the same dispatcher class, makes it more powerfull I hope ;) 
	*/
	Class StaticEventDispatcher {
		private static $instance;
		
		// "Private" constructor; prevents direct creation of object
		private function __construct() {
		}

		// "Private" clone; prevents direct creation of object
		private function __clone() {
		}

		public static function getInstance() {
			if (!isset(self::$instance)) {
				self::$instance = new EventDispatcher();
			}
			return self::$instance;
		}
	}

	/* Add some additional functionality to the Symphony 2 Event class
	*  the RequestEvent class also contains the request (the url) from the user
	*/
	class RequestEvent extends Event
	{
	    protected $request;

	    public function __construct($request)
	    {
		$this->request = $request;
	    }

	    public function getRequest()
	    {
		return $this->request;
	    }
	}
