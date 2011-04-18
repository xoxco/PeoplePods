<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/footer.php
* Defines what is in the footer of every page, used by $POD->footer()
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/themes
/**********************************************/
?>

		<div class="clearer"></div>
	</section> <!-- main -->
	<footer class="grid">
		<div class="column_4">
			<div class="lightblue">
				By using this site you agree to <b class="highlight">not be lame.</b>
			</div>
		</div>
		<div class="column_4">
			<div class="pink" style="text-align: center;">
				Powered by <a href="http://peoplepods.net" title="PeoplePods">PeoplePods</a>
			</div>
		</div>
		<div class="column_4">
			<div class="lightblue">
				<a href="<? $POD->siteRoot(); ?>">Home</a>
				<? if ($POD->libOptions('enable_contenttype_document_list')) { ?> | <a href="<? $POD->siteRoot(); ?>/show">What's New?</a><? } ?>
				<? if ($POD->libOptions('enable_core_groups')) { ?> | <a href="<? $POD->siteRoot(); ?>/groups">Groups</a><? } ?>
				<? if ($POD->isAuthenticated()) { ?>
					<? if ($POD->currentUser()->get('adminUser')) { ?>
						| <a href="<? $POD->podRoot(); ?>/admin">Admin Tools</a>
					<? } ?>
				<? } ?>	
			</div>
		</div>
		<div class="clearer"></div>
	</footer>
</body>
</html>
