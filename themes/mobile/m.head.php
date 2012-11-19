<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/m.head.php
*  
* Defines what a user profile looks like
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/person-object
/**********************************************/
?>
     <section id="" class="content" data-role="page">
            <div data-role="header" data-theme="b" data-position="fixed">
                <a href="#main_nav" data-role="button" data-icon="grid">Menu</a>
                <h1><a class="mainpagelink" href="<? $POD->siteRoot(); ?>" rel="external" ><? $POD->siteName(); ?></a></h1>
            </div>
            <div data-role="fieldcontain">
                <form method="get" action="<? $POD->siteRoot(); ?>/search">
                    <input type="search" name="q" id="search" value="" placeholder="Search"/>
                </form>
             </div>
            <div class="clearer"></div>
          
           <div data-role="content">
            	<? if (sizeof($POD->messages()) > 0) { ?>
				<section id="system_messages" style="display:none;">
                                    <div data-inline="true">
					<a href="#hideMessages" data-ajax="false" data-role="button" class="dismiss">OK</a>
                                    </div>
					<ul>
					<? foreach ($POD->messages() as $message) { ?>
						<li><?= $message; ?></li>
					<? } ?>
					</ul>
				</section>
			<? } ?>