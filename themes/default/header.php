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
	<title><? if ($pagetitle) { echo $pagetitle . " - " . $POD->siteName(false); } else { echo $POD->siteName(false); } ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<link rel="icon" href="<? $POD->templateDir(); ?>/img/peoplepods_favicon.png" type="image/x-icon">
	<link rel="shortcut icon" href="<? $POD->templateDir(); ?>/img/peoplepods_favicon.png" type="image/x-icon">

	<script src="<? $POD->templateDir(); ?>/js/jquery-1.4.2.min.js"></script>
	<script src="<? $POD->templateDir(); ?>/js/jquery.validate.min.js"></script>
	<script src="<? $POD->templateDir(); ?>/js/jquery-tagsinput/jquery.tagsinput.js"></script>

	<? $POD->extraJS(); ?>
	
	<link rel="stylesheet" type="text/css" href="<? $POD->templateDir(); ?>/styles.css" media="screen" />
	
	<? $POD->extraCSS(); ?>
	
	<? if ($feedurl) { ?>
		<link rel="alternate" type="application/rss+xml" title="RSS: <? if ($pagetitle) { echo $pagetitle . " - " . $POD->siteName(false); } else { echo $POD->siteName(false); } ?>" href="<? echo $feedurl; ?>" />
	<? } else if ($POD->libOptions('enable_core_feeds')) { ?>	
		<link rel="alternate" type="application/rss+xml" title="RSS: <? $POD->siteName();  ?>" href="<? $POD->siteRoot(); ?>/feeds" />
	<? } ?>		

	<script type="text/javascript">
		var siteRoot = "<? $POD->siteRoot(); ?>";
		var podRoot = "<? $POD->podRoot(); ?>";
		var themeRoot = "<? $POD->templateDir(); ?>";
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

	<script type="text/javascript" src="<? $POD->templateDir(); ?>/javascript.js"></script>

</head>
<body id="body">
	<? if ($fb_api = $POD->libOptions('fb_connect_api')) { ?>
		<!-- Facebook API -->
		<script type="text/javascript" src="http://connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php"></script> 
		<script type="text/javascript">FB.init('<?= $fb_api; ?>','/xd_receiver.htm');</script>	
		<!-- End Facebook API -->
	<? } ?>
	<!-- begin header -->
	<header>
			<!-- begin login status -->
			<section class="grid">
				<div class="column_8" id="siteName">
					<h1><? $POD->siteName(); ?></h1>
				</div>
				<div class="column_4" id="login_status">
					<? if ($POD->isAuthenticated()) { ?>
						Welcome, <a href="<? $POD->currentUser()->write('permalink'); ?>" title="View My Profile"><? $POD->currentUser()->write('nick'); ?></a> |
						<? if ($POD->libOptions('enable_core_private_messaging')) { ?>
							<a href="<? $POD->siteRoot(); ?>/inbox"><? $i = $POD->getInbox(); if ($i->unreadCount() > 0) { echo $i->unreadCount(); ?> Unread <? } else { ?>Inbox<? } ?></a> |
						<? } ?>
						<a href="<? $POD->siteRoot(); ?>/logout" title="Logout">Logout</a>
					<? } else { ?>
						Returning? <a href="<? $POD->siteRoot(); ?>/login">Login</a>
					<? } ?>
				</div>
			</section>
			<!-- end login status -->
			
			<!-- begin main navigation -->		
			
			<nav class="grid">
				<? if ($POD->isEnabled('core_search')) { ?>
					<form method="get" action="<? $POD->siteRoot(); ?>/search">
						<label for="nav_search_q">Search</label> <input name="q" id="nav_search_q" size="12" class="repairField" data-default="this site" />
					</form>
				<? } ?>
				
				<ul><li><a href="<? $POD->siteRoot(); ?>">Home</a></li>
					<? if ($POD->libOptions('enable_contenttype_document_list')) { ?><li><a href="<? $POD->siteRoot(); ?>/show">What's New?</a></li><? } ?>
					<? if ($POD->libOptions('enable_core_groups')) { ?><li><a href="<? $POD->siteRoot(); ?>/groups">Groups</a></li><? } ?>
					<? if ($POD->isAuthenticated()) { ?>
						<? if ($POD->currentUser()->get('adminUser')) { ?>
							<li><a href="<? $POD->podRoot(); ?>/admin">Command Center</a></li>
						<? } ?>
					<? } else { ?>
						<? if ($POD->libOptions('enable_core_authentication_creation')) {?><li><a href="<? $POD->siteRoot(); ?>/join">Join</a></li><? } ?>
					<? } ?>						
					<li class="clearer"></li>
				</ul>
				<div class="clearer"></div>
			</nav>
			<!-- end main navigation -->
			<? if (sizeof($POD->messages()) > 0) { ?>
				<section id="system_messages" class="grid" style="display:none;">
					<a href="#hideMessages" class="dismiss">OK</a>
					<ul>
					<? foreach ($POD->messages() as $message) { ?>
						<li><?= $message; ?></li>
					<? } ?>
					</ul>
				</section>
			<? } ?>
		<!-- end header -->
	</header>
	<section id="main" class="content grid">