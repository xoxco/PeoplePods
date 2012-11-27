$( document ).ready( function(){
	//used in outputting encrypted data
	var person = new Object();
	person.safeWord = '';
	person.pit = '';
	person.localMistrusted = '';
	
	//clear form elements upon focus
	var rememberFormValue;
	var currentText;
	var previousStyle;
	 //disabled for the moment. Relying on bootstrap default styles and behavior for testing
	$( 'form div' ).children( 'input:text, input:password, textarea' ).focus( function(){
		rememberFormValue = $( this ).val();
		$( this ).val( '' );
		
		previousStyle = $( this ).css( 'box-shadow' );
		$( this ).css( 'box-shadow', '0px 0px 30px #F77727' );
	} );
	//restore form elements upon blur
	$( 'form div' ).children( 'input:text, input:password, textarea' ).blur( function(){
		$( this ).css( 'box-shadow', previousStyle );
		currentText = $( this ).val();
		if( !currentText ) $( this ).val( rememberFormValue );
	} );
	
	/* //disabled for the moment - using custom interactivity for this form as there is a styles conflict. //todo maybe namespaced styles?
	$( 'label.tooltip' ).next().each( function(){
		var whichTooltip = $( this ).attr( 'id' );
		
		switch( whichTooltip ){
			case 'userName': $( this ).popover( {
					'placement'	: 'left',
					'trigger'	: 'focus',
					'title'		: 'Your username is your registered email address',
					'content'	: 'Please only register one email with the service. If you have forgotten the email that you registered, contact us through the form on this page.'				
				} );
				break;
			
			case 'password': $( this ).popover( {
					'placement'	: 'left',
					'trigger'	: 'focus',
					'title'		: 'Your password is only to be known by you, period! No one will ever ask for your password from village.rs!',
					'content'	: 'If someone asks you for your password let your doctor know so that she or he may report the individual.'					
				} );
				break;
			
			case 'pit': $( this ).popover( {
					'placement'	: 'left',
					'trigger'	: 'focus',
					'title'		: 'Your village safeword',
					'content'	: 'This is a shared password within your village. It grants you first access to the village. Later on, lets others know that you are "in the know" because only those people that are in the village will know it. If someone you don\'t know asks you for your safeword, immediately report it to your doctor, and, if appropriate, to the police.'					
				} );
				break;	
		}
		
	} );
	*/
	
	//reorganize main buttons to link to their childrens' a href attributes
	$( '.demoButton' ).click( function(){
		$( this ).children( 'a:first' ).click();
	} );
	
	//set up a general handler to demo paths to which links will send the user
	$( 'a' ).click( function( event ){
		event.preventDefault();
		event.stopImmediatePropagation();
		
		//switch between routes and post to demo installation
		switch( $( this ).attr( 'href' ) ){
			case '/join/'			: $('#joinModal').modal( 'show' ); break;
			case '/demo/healer/'	: goToDemo( 'healer@nickolasnikolic.com', 'healer' ); break;
			case '/demo/patient/'	: goToDemo( 'patient@nickolasnikolic.com', 'patient' ); break;
			case '/demo/family/'	: goToDemo( 'family@nickolasnikolic.com', 'family' ); break;
					
		}
		
	} );
	
	//the form id to operate upon, and ultimately submit
	var whichFormId;
	var role;
	//show only one of the following forms when the corresponding radio button is clicked
	$( "#toggleJoinForm > button.btn" ).click( function(){
		
		role = String( this.innerHTML );
		whichFormId = '#' + role + 'Join';
		
		//hide all
		$( '.joinForm, #hideMe' ).hide();
		
		//then show the winner! ;-)
		$( whichFormId ).show();
			
	});
	
	//upon sending form
	$( '#joinSubmit' ).click( function(){
		if( !whichFormId ) return console.log( 'whichFormId is empty.' );
		
		//new submission state: hide all notices
		$( '.tempShowHide, .alert' ).hide();
		
		//remember a reference for multiple contexts of 'this'
		var $joinSubmit = $(this);
		
		//set button to loading state
		$joinSubmit.button( 'loading' );
        
		//spool an object with properties and values from the form
		var postSubmit = new Object();
		
		postSubmit.meta_role = role;
		
		$( whichFormId ).find( 'input' ).each( function(){
			postSubmit[ $( this ).attr( 'name' ) ] = $( this ).val();	
		} );
				
		//todo fixme change this in final implementation to siteroot/join
		$.post( 'http://nickolasnikolic.com/sn/pp3/join', postSubmit, function( response ){
			//game on (do a little dance...)
			
			//an annoying parse bug caused the need for this line, nothing else...
			var irritable = JSON.parse( response );
			
			//if the response says 200, then a account has been created.
			if( irritable.status == '200' ){
				$( '.alert-success' ).removeClass( 'tempShowHide' ).show();
				$joinSubmit.button( 'complete' ).addClass( 'btn-success' )
				$joinSubmit.attr( 'disabled', 'disabled' );
			}else{
				$( '.alert-error' ).removeClass( 'tempShowHide' ).show();
				$joinSubmit.button( 'reset' );
				console.log( response );//todo this object should be sent to logs to decipher what may be the problem.
				console.log( response.status );
			}
			//if we needed a redirect, we wouldn't have commented the following line...
			//window.history.replace( 'http://village.rs' );
		} );
	} );
	
	$( '#submitLogin' ).click( function(){
		//just a one-liner to submit the login if the username has been changed
		if( event.which == 13 && $( '#loginBox input:first' ).val() != 'username' || $( '#loginBox input:eq( 2 )' ).val() != 'password' ) $( '#loginBox' ).trigger( 'submit' );	
	} );
	
	/*
	//send login to custom route inside of PeoplePods
	$( 'form' ).submit( function( event ){
		//stop form from sending to action file
		event.preventDefault();//todo clearly we need to actually handle the click with a routed request...
	} );
	//continuation of last intent
	$( '#loginBox input:last' ).click( function( event ){
		
		//stop any propogation or submission, we are sending special... ;-)
		event.stopImmediatePropagation();
		event.preventDefault();
		
		//if everything is in it's stock position, return
		if( $( '#loginBox input:first' ).val() == 'username' || rememberFormValue == 'undefined' ) return;
		
		//safeword is a simple concept: a shared password in the village that will be just a simply extra layer of security.
		//members of a village get this information FROM ONE ANOTHER, this means that if a village selects to have a safeword
		//and an attacker wishes to find it out, the attacker must contact a small, well-integrated group that actually knows each other
		//from a security standpoint, it's little more then an inversion of social engineering - I love it. We'll see if it proves to irritate people.
		person.pit = $( '#pit' ).val();//@todo - see if this might just be more convenient to pull from local storage/cookie and then match with known user agents
		
		var submission = new Array();
		
		$( '#loginBox div' ).children( 'input:text, input:password' ).each( function( index, element ){
			submission.push( $( this ).val() );
		} );
		
		console.log( submission );
		
		//this encryption method requires a flat structure, so a string works...
		var submitString = JSON.stringify( submission );
		
		person.safeWord = sjcl.encrypt( person.pit, submitString );
		console.log( person.safeWord ); //@todo this is all that should ever be seen by the server...
		//@todo send securely to PeoplePods auth while evaluating jquery object to assure that it is coming from the right place
		
		//@todo - decrypt test only
		person.localMistrusted = sjcl.decrypt( person.pit, person.safeWord );
		console.log( person.localMistrusted );
	} );
	
	
	$( document ).keyup( function( event ){
		//just a one-liner to submit the login if the username has been changed
		if( event.which == 13 && $( '#loginBox input:first' ).val() != 'username' || $( '#loginBox input:eq( 2 )' ).val() != 'password' ) $( '#loginBox input:last' ).click()
	} );
	*/
	
	//load and display recent village project commits
	//recreating the following document structure:
//        <div class="news_box"> <img src="images/calendar.png" alt="" title="" border="0" class="feat_thumb" />
//          <div class="news_details">
//            <h2><a href="#">Date of entry</a></h2>
//            <p class="feat_text">This is expected to the the GitHub.com updates through their API</p>
//          </div>
//        </div>
	
	$.getJSON( 'https://api.github.com/repos/nickolasnikolic/PeoplePods/commits?callback=?', function( commits ){
			//if all is good with the request
			if( commits.meta.status ==  200 ){
				//refresh and clear the news box while preparing to populate the project updates
				$( '#latest_news' ).show().html( '<h1>Software Updates</h1>' );
			}else{
				//return with my tail between my legs
				return console.log( 'There has been a problem with the request to GitHub. Response code is: ' + commits.meta.status );
			}
			
			//get the last 10 updates from GitHub
			var firstFewCommits = commits.data.slice( 0, 10 );

			$.each( firstFewCommits, function( index, elementAtIndex ){
				//apparently all of the commits from on campus are only attributed to the machine that made them due to firewall/ad restrictions
				//still get to make the commit, however it seems that git has to do a little oAuth tango to get it done
				//consequently, this create a null error in the api parse on author...
				//good to know
				//either way, if we get here, then we know that we got a response of 200:ok and can move on
				if( !!elementAtIndex.author ) var author = elementAtIndex.commit.author.name;
				else var author = 'Authorized user';
				
				var didWhat = elementAtIndex.commit.message;
				var when = moment( elementAtIndex.commit.author.date ).fromNow();

				//create an individual id for all generated newsboxes
				var individualValue = 'thisOne_' + index;
				var individualSelector = '#' + individualValue;
				var subSelector = individualSelector + ' div.news_details';
				
				$( '#latest_news' ).append( '<div id="' + individualValue + '"  class="news_box"></div>' );
				
				$(  individualSelector ).append( '<img src="images/calendar.png" alt="" title="" border="0" class="feat_thumb" />' );
				$(  individualSelector ).append( '<div class="news_details"></div>' );
				$(  subSelector ).append( '<h2 class="gitHub">' + when + ' | ' +  author + '</h2>' );
				$(  subSelector ).append( '<p class="feat_text">' + didWhat + '</p>' );
				
			} );
	} );
	
	function goToDemo( username, pass ){
		//todo in the meantime, just go ahead and submit the form
		$( '#userName' ).val( username );
		$( '#password' ).val( pass );
		
		
		$( '#loginBox' ).submit();
		
		//todo add a use for the safeword...
		
		/* //to ajax, or not to ajax, that is the question... for now, just submit the form as above
		var whereToSend = $( '#loginBox' ).attr( 'action' );
		
		//default value if needed
		var redirect =  redirect || 'dashboard';
		
		$.post( whereToSend, { 'username': username, 'password': password, 'redirect': redirect }, function( response ){
			//console.log( response );
			window.location.replace( 'http://nickolasnikolic.com/sn/pp3/dashboard' );
		} );
		*/	
	}
} );