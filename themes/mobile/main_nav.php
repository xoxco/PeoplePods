<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/people/output.php
* Default output template for a person object. 
* Defines what a user profile looks like
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/person-object
/**********************************************/
?>
   <div data-role="page" id="main_nav">
			<!-- begin login status -->
        <section>
            <div id="siteName" data-role="header" data-theme="b" data-position="fixed" >
                <h1><a href="<?php $POD->siteRoot(); ?>" rel="external"><?php $POD->siteName(); ?></a></h1>
            </div>
            <div id="header_menu" data-role="content">
                <div class="one_third" id="login_status">
                     <ul data-role="listview">
                        <li><a href="<?php $POD->siteRoot(); ?>">Home</a></li>
                        <?php if ($POD->libOptions('enable_core_dashboard') && $POD->isAuthenticated()){ ?>
                            <li><a href="<?php $POD->siteRoot();?>/replies">Replies</a></li>
                        <?php } ?>
                        <?php if ($POD->libOptions('enable_contenttype_document_list')) { ?><li><a href="<?php $POD->siteRoot(); ?>/show">What's New?</a></li><?php } ?>
                        <?php if ($POD->isAuthenticated()) { ?>
                        <li><a href="<?php $POD->currentUser()->write('permalink'); ?>" title="View My Profile" rel="external">my profile:&nbsp;<?php $POD->currentUser()->write('nick'); ?></a></li>
                            <?php if ($POD->libOptions('enable_core_private_messaging')) { ?>
                                <li><a href="<?php $POD->siteRoot(); ?>/inbox" rel="external"><?php $i = $POD->getInbox(); if ($i->unreadCount() > 0) { echo $i->unreadCount(); ?> Unread <?php } else { ?>Inbox<?php } ?></a></li>
			<?php } ?>
                        <li><a href="<?php $POD->siteRoot(); ?>/logout" title="Logout" rel="external">Logout</a></li>
			<?php } else { ?>
                            <li><a href="<?php $POD->siteRoot(); ?>/login" rel="external">Login</a></li>
			<?php } ?>
                        <?php if ($POD->isAuthenticated()) { ?>
                            <?php if ($POD->currentUser()->get('adminUser')) { ?>
                                <li><a href="<?php $POD->podRoot(); ?>/admin" rel="external">Command Center</a></li>
                            <?php } ?>
                        <?php } else { ?>
                                <?php if ($POD->libOptions('enable_core_authentication_creation')) {?>
                                    <li><a href="<?php $POD->siteRoot(); ?>/join">Join</a></li>
                                <?php } ?>
                            <?php } ?>
                    </ul>
                </div>
             </div>
	</section>
                
        <div data-role="footer" data-theme="b" data-id="nav_foot" data-position="fixed">
            <div data-role="navbar">
                <ul>
                    <li><a href="<?php $POD->siteRoot(); ?>">Dashboard</a></li>
                    
                    <?php  if($POD->isAuthenticated()){ ?>
                           <li><a href="<?php $POD->currentUser()->write('permalink');?>" >Profile</a></li>
                    <?php } ?>
                    <li><a href="<?php $POD->siteRoot(); ?>/search" >Search</a></li>
                </ul>
            </div>
        </div>
    </div>
