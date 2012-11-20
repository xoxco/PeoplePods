<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/header.php
* Defines what is in the header of every page, used by $POD->header()
*
* Special variables in this file are:
* $pagetitle
* $feedurl
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/themes
/**********************************************/
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title><?php if ($pagetitle) { echo $pagetitle . " - " . $POD->siteName(false); } else { echo $POD->siteName(false); } ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<link rel="icon" href="<?php $POD->templateDir(); ?>/img/peoplepods_favicon.png" type="image/x-icon">
	<link rel="shortcut icon" href="<?php $POD->templateDir(); ?>/img/peoplepods_favicon.png" type="image/x-icon">

	<script src="<?php $POD->templateDir(); ?>/js/jquery-1.8.3.js"></script>
	<script src="<?php $POD->templateDir(); ?>/js/jquery.validate.min.js"></script>
	<script src="<?php $POD->templateDir(); ?>/js/jquery-tagsinput/jquery.tagsinput.js"></script>
	<script src="<?php $POD->templateDir(); ?>/js//underscore.js"></script>
    <script src="<?php $POD->templateDir(); ?>/js/sjcl.js"></script>
    <script src="<?php $POD->templateDir(); ?>/js/moment.min.js"></script>
    <script src="<?php $POD->templateDir(); ?>/js/unauthorized.js"></script>

	<?php $POD->extraJS(); ?>
	
	<link rel="stylesheet" type="text/css" href="<?php $POD->templateDir(); ?>/styles.css" media="screen" />
	<link rel="stylesheet" type="ttext/css" href="<?php $POD->templateDir(); ?>/custom.css" media="screen"" />
	
	<?php $POD->extraCSS(); ?>
	
	<?php if ($feedurl) { ?>
		<link rel="alternate" type="application/rss+xml" title="RSS: <?php if ($pagetitle) { echo $pagetitle . " - " . $POD->siteName(false); } else { echo $POD->siteName(false); } ?>" href="<?php echo $feedurl; ?>" />
	<?php } else if ($POD->libOptions('enable_core_feeds')) { ?>	
		<link rel="alternate" type="application/rss+xml" title="RSS: <?php $POD->siteName();  ?>" href="<?php $POD->siteRoot(); ?>/feeds" />
	<?php } ?>		

	<script type="text/javascript">
		var siteRoot = "<?php $POD->siteRoot(); ?>";
		var podRoot = "<?php $POD->podRoot(); ?>";
		var themeRoot = "<?php $POD->templateDir(); ?>";
		var API = siteRoot + "/api/2";		
	</script>

	<!-- HTML5 fix for IE 6-8 -->
	<!--[if lt IE 9]>
	<script>
	  var e = ("abbr,article,aside,audio,canvas,datalist,details," +
	    "figure,footer,header,hgroup,mark,menu,meter,nav,output," +
	    "progress,section,time,video").split(',');
	  for (var i = 0; i < e.length; i++) {
	    document.createElement(e[i]);
	  }
	</script>
	<![endif]-->	

	<script type="text/javascript" src="<?php $POD->templateDir(); ?>/javascript.js"></script>

</head>
<body id="body">
	<?php if ($fb_api = $POD->libOptions('fb_connect_api')) { ?>
		<!-- Facebook API -->
		<script type="text/javascript" src="http://connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php"></script> 
		<script type="text/javascript">FB.init('<?php echo $fb_api; ?>','/xd_receiver.htm');</script>	
		<!-- End Facebook API -->
	<?php } ?>
	<!-- begin header -->
	<header>
			<!-- begin login status -->
			<section class="grid">
				<div class="two_thirds" id="siteName">
					<h1><?php $POD->siteName(); ?></h1>
				</div>
				<div class="one_third" id="login_status">
					<?php if ($POD->isAuthenticated()) { ?>
						Welcome, <a href="<?php $POD->currentUser()->write('permalink'); ?>" title="View My Profile"><?php $POD->currentUser()->write('nick'); ?></a> |
						<?php if ($POD->libOptions('enable_core_private_messaging')) { ?>
							<a href="<?php $POD->siteRoot(); ?>/inbox"><?php $i = $POD->getInbox(); if ($i->unreadCount() > 0) { echo $i->unreadCount(); ?> Unread <?php } else { ?>Inbox<?php } ?></a> |
						<?php } ?>
						<a href="<?php $POD->siteRoot(); ?>/logout" title="Logout">Logout</a>
					<?php } else { ?>
						Returning? <a href="<?php $POD->siteRoot(); ?>/login">Login</a>
					<?php } ?>
				</div>
			</section>
			<!-- end login status -->
			
			<!-- begin main navigation -->		
			
			<nav class="grid">
				<?php if ($POD->isEnabled('core_search')) { ?>
					<form method="get" action="<?php $POD->siteRoot(); ?>/search">
						<label for="nav_search_q">Search</label> <input name="q" id="nav_search_q" size="12" class="repairField" data-default="this site" />
					</form>
				<?php } ?>
				
				<ul><li><a href="<?php $POD->siteRoot(); ?>">Home</a></li>
					<?php if ($POD->libOptions('enable_contenttype_document_list')) { ?><li><a href="<?php $POD->siteRoot(); ?>/show">What's New?</a></li><?php } ?>
					<?php if ($POD->libOptions('enable_core_groups')) { ?><li><a href="<?php $POD->siteRoot(); ?>/groups">Groups</a></li><?php } ?>
					<?php if ($POD->isAuthenticated()) { ?>
						<?php if ($POD->currentUser()->get('adminUser')) { ?>
							<li><a href="<?php $POD->podRoot(); ?>/admin">Command Center</a></li>
						<?php } ?>
					<?php } else { ?>
						<?php if ($POD->libOptions('enable_core_authentication_creation')) {?><li><a href="<?php $POD->siteRoot(); ?>/join">Join</a></li><?php } ?>
					<?php } ?>						
					<li class="clearer"></li>
				</ul>
				<div class="clearer"></div>
			</nav>
			<!-- end main navigation -->
			<?php if (sizeof($POD->messages()) > 0) { ?>
				<section id="system_messages" class="grid" style="display:none;">
					<a href="#hideMessages" class="dismiss">OK</a>
					<ul>
					<?php foreach ($POD->messages() as $message) { ?>
						<li><?php echo $message; ?></li>
					<?php } ?>
					</ul>
				</section>
			<?php } ?>
		<!-- end header -->
	</header>
	<section id="main" class="content grid">