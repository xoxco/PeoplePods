<?php
include( '../template_helpers/mobile_detect/mobile_detect.php' );
$detect = new Mobile_Detect();

if ($detect->isMobile()) {
    //@todo redirect to mobile web login
}
$pathToPeoplePods = realpath( "../../../PeoplePods.php"  );
require_once( $pathToPeoplePods );
	
//set up central object with info to query regarding the person and their login
$POD = new PeoplePod( array( 'debug' =>0, 'authSecret' => @$_COOKIE['pp_auth'] ) ); //todo this line check to see if auth is current, must change to opposite

//if they have already logged in
if( $POD->isAuthenticated() ){
	//send them to their respective dashboard //todo make smarter - needs to route between healer dashboards, patient, and family/friend dashboards.
	header( 'Location: http://nickolasnikolic.com/sn/pp3/dashboard' );	
}
?>

<!DOCTYPE html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>village.rs | real insight into wellness</title>
<link rel="stylesheet" type="text/css" href="../js/bootstrap/css/bootstrap.min.css" media="all"/>
<link rel="stylesheet" type="text/css" href="css/style.css" media="screen" />
<script src="../js/jquery-1.8.3.min.js"></script>
<script src="../js/underscore.js"></script>
<script src="../js/sjcl.js"></script>
<script src="../js/moment.min.js"></script>
<script src="../js/bootstrap/js/bootstrap.min.js"></script>
<script src="js/unauthorized.js"></script>
</head>

<body>
<div id="main_container">
  <noscript id="javascriptAnnouncment">
  <div>
    <p>This website relies upon modern technology of the web for it's features and security. While we believe in accessiblility to all who may come, but you must have JavaScript, HTML5 features such as Local Storage, and Cookies turned on to use the site.</p>
    <p>Technologically speaking, meet us halfway. We've done our best to accomodate those that don't have a choice, for instance, taken stock of public library offerings and other resources used by many patients within the community-care approach - with special consideration for the homeless patient. But, the services we offer require features that can help assure privacy and an all around good experience.</p>
    <p>Turn the features on, please. Thank you for your understanding. </p>
  </div>
  </noscript>
  <div class="header">
    <div class="logo"><a tabindex="5" href="index.php"><img src="images/logo.png" alt="logo"></a></div>
  </div>
  <div class="home_center">
    <div class="pack_descr">
      <h1>If this is what your patient's experience is like after she leaves your office, you need to know about it. We help with that.</h1>
      <p class="home_intro">village.rs is a connection point between healers, patients, and the people that love them. It is a new kind of kinder service.</p>
      <div class="buttons">
        <div class="demoButton"><a href="/join/">join</a></div>
        <div class="demoButton"><a href="/demo/healer/">demo as doctor or social worker</a></div>
        <div class="demoButton"><a href="/demo/patient/">demo as patient</a></div>
        <div class="demoButton"><a href="/demo/family/">demo as family of patient</a></div>
      </div>
    </div>
    <div class="pack_pic"></div>
  </div>
  <div class="center_content">
    <div class="left_content">
      <h2>Top Features</h2>
      <div class="feat_box"> <img src="images/Lock.png" alt="" title="" border="0" class="feat_thumb" />
        <div class="feat_details">
          <h3>Locked-down social environment</h3>
          <p class="feat_text"> HIPAA compliance is at the core of the village.rs design. We make sure you have contact with the patient while others involved take a supporting role with medically-related information only present between you and your patient. You are in control of who sees what, and when. </p>
        </div>
      </div>
      <div class="feat_box"> <img src="images/Home.png" alt="" title="" border="0" class="feat_thumb" />
        <div class="feat_details">
          <h3>Facilitates the community-based care paradigm</h3>
          <p class="feat_text"> We use a village metaphor for the size and purpose of each group served. The groups expected to be 
            served in the software amount to a rough useful maximum of 15 or so. This is not the maximum that can 
            be served in the social network, it is just that each village will be separated outside of the doctor 
            present, or special cases. Most villages have little reason to believe that others exist. </p>
        </div>
      </div>
      <div class="feat_box"> <img src="images/Mobile.png" alt="" title="" border="0" class="feat_thumb" />
        <div class="feat_details">
          <h3>Multi-screen</h3>
          <p class="feat_text"> village.rs features both an online component and a mobile web component. This multi-screen approach is 
            selected because the benefits apply to this design problem. In the case of the online component, a 
            larger screen displaying larger sets of information with a faster transfer speed – particularly to the 
            doctor in the form of symptom analytics – is quite useful. In the case of the mobile web, the simple 
            ability to have constant contact on the person of the healer makes a strong case for going with a mobile 
            approach. </p>
        </div>
      </div>
      <div class="feat_box"> <img src="images/Chat.png" alt="" title="" border="0" class="feat_thumb" />
        <div class="feat_details">
          <h3>Pay-as-you-will</h3>
          <p class="feat_text">We run this service for the philosophy of good care, care about the dignity of those we serve, and want to do the best we can in the world. We have bills, however.
            We provide the opportunity to offset our costs on a pay-as-you-will basis. If it is affordable, please help. If it isn't, use our services to do good in the world, anyway.</p>
        </div>
      </div>
      <div class="feat_box"> <img src="images/Graph.png" alt="" title="" border="0" class="feat_thumb" />
        <div class="feat_details">
          <h3>Symptom analytics</h3>
          <p class="feat_text">We give you and your patients tools to gauge the patterns present. Be they timing or activity related, we help you both make your best decisions.</p>
        </div>
      </div>
      <div class="feat_box"> <img src="images/Chat.png" alt="" title="" border="0" class="feat_thumb" />
        <div class="feat_details">
          <h3>Will remain free and ad-free for your patients</h3>
          <p class="feat_text">We understand why the community-care based approach is used for the chronically ill, we also understand conflicts of interest. We am to provide supporting services to you while presenting none of the common pitfalls of a service. If pay-as-you-will is not working out and the service is in danger of failing because of it, we will put it to our users the best way to handle it. Your patients will never see an ad or be asked to help with the cost of running this service. This is not a marketing delivery service, this is a place of care.</p>
        </div>
      </div>
      <div id="latest_news" class="latest_news">
        <h1>Software Updates</h1>
        <div class="news_box"> <img src="images/calendar.png" alt="" title="" border="0" class="feat_thumb" />
          <div class="news_details">
            <h2><a href="#">Date of entry</a></h2>
            <p class="feat_text">This is expected to the the GitHub.com updates through their API</p>
          </div>
        </div>
        <div class="news_box"> <img src="images/calendar.png" alt="" title="" border="0" class="feat_thumb" />
          <div class="news_details">
            <h2><a href="#">Date of entry</a></h2>
            <p class="feat_text">This is expected to the the GitHub.com updates through their API</p>
          </div>
        </div>
      </div>
    </div>
    <!--end of left content-->
    
    <div class="right_content">
      <div id="userTestimonial">
        <h2>What do our users say?</h2>
        <div class="testimonial_box">
          <p>I am the creator and first user of the software. I created it so that others could build for themselves the same type of support system that I have in my own life. Essentially, I hope that I have codified all of the good things that I have to depend upon. It has been a global effort in the making and I'm glad to see it finally exist. I keep my family physician and psychiatrist updated as to symptoms and side-effects of my illness and medications. My neighbors and close family are also part of my village. This community is effective and by design.</p>
          <div class="testimonial_details">
            <p>Nickolas Nikolic, founder and patient</p>
          </div>
        </div>
      </div>
      <div id="contactForm">
        <h2>Get in touch! Especially let us know of your thoughts of and experiences with village.rs!</h2>
        <div class="form_content">
          <form id="form1" method="post" action="">
            <div class="form_row">
              <label>name:</label>
              <input tabindex="6" type="text" class="form_input" name="name" />
            </div>
            <div class="form_row">
              <label>email:</label>
              <input tabindex="7" type="text" class="form_input" name="email" />
            </div>
            <div class="form_row">
              <label>message:</label>
              <textarea cols="26" tabindex="8" class="form_textarea" name="comment"></textarea>
            </div>
            <div class="form_row">
              <input tabindex="9"  type="submit" class="form_submit" value="Send" />
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!--end of right content-->
  
  <div class="clear"></div>
</div>
<!--end of center content-->

<div id="footer"> This is a location for regular links to information regarding the service. (placeholder)</div>

<!--start of login box-->
<div id="login">
  <form name='loginBox' id="loginBox" method="post" action="<?php $POD->siteRoot(); ?>/login" class="valid">
    <div><label class="tooltip">username:</label><input tabindex="1" class="required email text input-large" type="text" name="email" id="userName" placeholder="username is your registered email"/></div>
    <div><label class="tooltip">password:</label><input tabindex="2" class="password required text input-large" type="password" id="password" name="password" placeholder="password (don't give others this)" /></div>
    <div><label class="tooltip">safeword:</label><input class="safeword input-large" tabindex="3" type="text" id="pit" size="25" name="pit" placeholder="your village safeword (optional)" /></div>
    <input type="hidden" name="redirect" id="redirect" value="dashboard" />
    <div><input tabindex="4" type="button" id="submitLogin" value="login" /></div>
    <div><a href="/forgottenPass/">forgot password or safeword?</a></div>
  </form>
</div>
<!--end of login box-->

<!-- Modal join dialogue -->
<div id="joinModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="Join" aria-hidden="true">
<!--START Alert Messages for join form-->
	<div class="hidden alert alert-success">
	    <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h4><i class="icon-ok-circle"></i> Registration started!</h4>
        <p>Now you should check your email in a few minutes to verify your account.</p>
	</div>
	<div class="hidden alert alert-error">
	    <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h4><i class="icon-minus-sign"></i> Registration didn't work</h4>
        <p>Try again a few seconds later, if it doesn't work again...</p>
        <p><a class="btn btn-danger" href="#hypothetical-contact-form">Let us know</a> <a class="btn" href="#">Or just cancel</a></p>
	</div>
<!--END Alert Messages for join form-->
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Join village.rs!</h3>
    <div id="joinChoice">Join as:
    	<div id="toggleJoinForm" class="btn-group" data-toggle="buttons-radio">
          <button type="button" class="btn">healer</button>
          <button type="button" class="btn">patient</button>
          <button type="button" class="btn">family</button>
		</div>
    </div>
  </div>
  <div class="modal-body">
  	<h1 id="hideMe">Please be sure to select a role in the social network. Be honest, we check.</h1>
    <form id="healerJoin" class="joinForm form-horizontal">
      <fieldset>
            <legend>Join our network as a doctor or social worker</legend>
            
            <label>name</label>
            <input name="name" type="text" placeholder="first and last name">
            
            <label>email</label>
            <input name="email" type="text" placeholder="valid email address">
            <span class="help-block">All email addresses will be verified.</span>
            
            <label>password</label>
            <input name="password" type="password">
            <span class="help-block">Make it a good one.</span>
            
            <label>safeword</label>
            <input name="meta_safeword" type="text" placeholder="Type something…">
            <span class="help-block">A safeword is like a group password. We tell you when someone has logged on without one.</span>
    
            <label class="checkbox">
              <input name="meta_joinVillageWithoutApproval" type="checkbox">Allow patients to join my villages without my approving them first?</input><br />
              <input name="meta_requireSafeword" type="checkbox">Require a safeword of all joining and entering my villages?</input>
            </label>
      </fieldset>
    </form>
    
    <form id="patientJoin" class="joinForm form-horizontal">
      <fieldset>
            <legend>Join our network as a patient</legend>
            
            <label>name</label>
            <input name="name" type="text" placeholder="first and last name">
            
            <label>email</label>
            <input name="email" type="text" placeholder="valid email address">
            <span class="help-block">All email addresses will be verified.</span>
            
            <label>password</label>
            <input name="password" type="password">
            <span class="help-block">Make it good.</span>
    
            <label>safeword</label>
            <input name="meta_safeword" type="text" placeholder="Type something…">
            <span class="help-block">A safeword is like a group password. We tell you when someone has logged on without one.</span>
      </fieldset>
    </form>
    
    <form id="familyJoin" class="joinForm">
      <fieldset>
            <legend>Join our network as a family member</legend>
            
            <label>name</label>
            <input name="name" type="text" placeholder="first and last name">
            
            <label>email</label>
            <input name="email" type="text" placeholder="valid email address">
            <span class="help-block">All email addresses will be verified.</span>
            
            <label>password</label>
            <input name="password" type="password">
            <span class="help-block">Make it good.</span>
    
            <label>safeword</label>
            <input name="meta_safeword" type="text" placeholder="Type something…">
            <span class="help-block">A safeword is like a group password. We tell you when someone has logged on without one.</span>
      </fieldset>
    </form>

  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    <button id="joinSubmit" class="btn btn-primary">Save changes</button>
  </div>
</div>
<!-- end Modal join dialogue -->
</div>
</body>
</html>