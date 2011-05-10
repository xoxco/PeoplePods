<?

    /* Subscribing to events on the core_dispatch api
    *
    */
    $POD = new PeoplePod(array('authSecret'=>@$_COOKIE['pp_auth'],'debug'=>2));
    $dispatcher = $POD->getDispatcher();
    $request = 'personFriends';
    $dispatcher->addListener($request, function(Event $event) {
	// will be executed when the onFooAction event is dispatched
	echo 'Alrighty, we have just trapped a request<br /><br />'; 
	echo 'The dump of the request is:<br />';
	var_dump($event->getRequest());
	echo '<br /><br />'; 

	echo 'The complete event dump is:<br />';
	var_dump($event);
	echo '<br /><br />'; 
	  die(); 
    });
  
