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
        <meta name="viewport" content="user-scalable=no, width=device-width" />
        <meta name="apple-mobile-web-app-capable" content="yes">
        
	<link rel="icon" href="<? $POD->templateDir(); ?>/img/peoplepods_favicon.png" type="image/x-icon" rel="external">
	<link rel="shortcut icon" href="<? $POD->templateDir(); ?>/img/peoplepods_favicon.png" type="image/x-icon" rel="external">

        <link rel="stylesheet" type="text/css" href="<? $POD->templateDir(); ?>/styles.css" media="screen" charset="utf-8" />
        <link rel="stylesheet" href="<? $POD->templateDir(); ?>/jquery.mobile.css" />
        
	<script type="text/javascript" src="<? $POD->templateDir(); ?>/js/jquery-1.5.2.min.js"></script>

        <script type="text/javascript">
               $(document).bind("mobileinit", function(){
                    //define jqmobile over-rides here
                   $.extend(  $.mobile , {
                       ajaxFormsEnabled: false
                    });
                }); 
        </script>
        <script type="text/javascript" src="<? $POD->templateDir(); ?>/javascript.js"></script>
	<script type="text/javascript" src="<? $POD->templateDir(); ?>/js/jquery.mobile.js"></script>
    
	<script src="<? $POD->templateDir(); ?>/js/jquery.validate.min.js"></script>
	<script src="<? $POD->templateDir(); ?>/js/jquery-tagsinput/jquery.tagsinput.js"></script>

	<? $POD->extraJS(); ?>

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
      
	</header>
        <?
        //begin mobile output
            $POD->output('m.head');
        ?>


    