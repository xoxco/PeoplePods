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
        <meta name="viewport" content="user-scalable=no, width=device-width" />
        <meta name="apple-mobile-web-app-capable" content="yes">
        
	<link rel="icon" href="<?php $POD->templateDir(); ?>/img/peoplepods_favicon.png" type="image/x-icon" rel="external">
	<link rel="shortcut icon" href="<?php $POD->templateDir(); ?>/img/peoplepods_favicon.png" type="image/x-icon" rel="external">

        <link rel="stylesheet" type="text/css" href="<?php $POD->templateDir(); ?>/styles.css" media="screen" charset="utf-8" />
        <link rel="stylesheet" href="<?php $POD->templateDir(); ?>/jquery.mobile.css" />
        
	<script type="text/javascript" src="<?php $POD->templateDir(); ?>/js/jquery-1.5.2.min.js"></script>

        <script type="text/javascript">
               $(document).bind("mobileinit", function(){
                    //define jqmobile over-rides here
                   $.extend(  $.mobile , {
                       ajaxFormsEnabled: false
                    });
                }); 
        </script>
        <script type="text/javascript" src="<?php $POD->templateDir(); ?>/javascript.js"></script>
	<script type="text/javascript" src="<?php $POD->templateDir(); ?>/js/jquery.mobile.js"></script>
    
	<script src="<?php $POD->templateDir(); ?>/js/jquery.validate.min.js"></script>
	<script src="<?php $POD->templateDir(); ?>/js/jquery-tagsinput/jquery.tagsinput.js"></script>

	<?php $POD->extraJS(); ?>

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
      
	</header>
        <?php
        //begin mobile output
            $POD->output('m.head');
        ?>


    