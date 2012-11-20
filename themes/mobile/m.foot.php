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
    </div><!-- closes data-role=content from m.head -->
        <div data-role="footer" data-theme="b" data-id="main_foot" data-position="fixed">
            <div data-role="navbar">
                <ul>
                    <li><a href="<?php $POD->siteRoot(); ?>">Dashboard</a></li>

                    <?php if($POD->isAuthenticated()){ ?>
                        <li><a href="<?php $POD->currentUser()->write('permalink');?>" >Profile</a></li>
                    <?php } ?>
                    
                    <li><a href="<?php $POD->siteRoot(); ?>/search" >Search</a></li>
                </ul>
            </div>
        </div>
        <div class="clearer"></div>
</section> <!-- end data-role=page from header -->
<?php $POD->output('main_nav'); ?>
