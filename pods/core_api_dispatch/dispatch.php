<? 
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* core_api_dispatch/index.php
* Handles simple requests to /dispatch
*
* Documentation for this pod can be hopefully found here at some time in the future:
* http://peoplepods.net/readme
/**********************************************/

	include_once("../../PeoplePods.php");
	
	$POD = new PeoplePod(array('authSecret'=>@$_COOKIE['pp_auth'],'debug'=>2));
	
	if ($POD->libOptions('enable_core_api_dispatch')) { 

		/* Get the request from the url */
		$request = $_GET['request'];
		$POD->tolog("API CALL REQUEST TO DISPATCH: $request");
	
		/* Process the request and create and event for it */
		$requestArray = explode('/',$request);
		// INFO: http://symfony.com/doc/2.0/book/internals/event_dispatcher.html
		$dispatcher = $POD->getDispatcher();
		$event = $POD->newRequestEvent($requestArray);

		if (isset($requestArray[0]) && isset($requestArray[1])) {
			// Make an event type based on the fist two url 'arguments' which indicate the type of user api request
			// camelCase the request, and set it as the type of event
			$requestArray[1][0] = strtoupper($requestArray[1][0]);
			$dispatcher->dispatch($requestArray[0].$requestArray[1] , $event);
		}
		else {
			// Fail silently, or produce an error
			// You can hardcode it here, but we'll dispatch an event, in case the pods want to decide this
			$dispatcher->dispatch('malFormedRequest', $event);  
		}
	} // if pod is enabled
	
?>