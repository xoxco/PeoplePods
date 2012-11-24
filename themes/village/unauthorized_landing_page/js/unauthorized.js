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
	
	//reorganize main buttons to link to their childrens' a href attributes
	$( '.demoButton' ).click( function(){
		alert( $( this ).children( 'a:first' ).attr( 'href' ) ); //todo clearly we need to actually handle the click with a routed request...
	} );
	
	$( 'a' ).click( function( event ){
		event.preventDefault();
		event.stopImmediatePropagation();
		alert( $( this ).attr( 'href' ) ); //todo clearly we need to actually handle the click with a routed request...
	} );
	
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
		
		//todo in the meantime, just go ahead and submit the form
		var username = $( '#username' ).val();
		var password = $( '#password' ).val();
		var whereToSend = $( this ).attr( 'action' );
		var redirect = 'dashboard';
		
		console.log( username, password, whereToSend, redirect );
		
		$.post( whereToSend, { 'username': username, 'password': password, 'redirect': redirect }, function( response ){
			//console.log( response );
			window.location.replace( 'http://nickolasnikolic.com/sn/pp3/dashboard' );
		} );
	} );
	
	$( document ).keyup( function( event ){
		//just a one-liner to submit the login if the username has been changed
		if( event.which == 13 && $( '#loginBox input:first' ).val() != 'username' ) $( '#loginBox input:last' ).click()
	} );
	
	//load and display recent village project commits
	//recreating the following document structure:
//        <div class="news_box"> <img src="images/calendar.png" alt="" title="" border="0" class="feat_thumb" />
//          <div class="news_details">
//            <h2><a href="#">Date of entry</a></h2>
//            <p class="feat_text">This is expected to the the GitHub.com updates through their API</p>
//          </div>
//        </div>
	
	$.getJSON( 'https://api.github.com/repos/nickolasnikolic/PeoplePods/commits', function( commits ){
			//refresh and clear the news box
			$( '#latest_news' ).show().html( '<h1>Software Updates</h1>' );
						
			var firstFewCommits = commits.slice( 0, 5 );
			
			console.log( firstFewCommits );
			
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
} );