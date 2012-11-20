<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/people/dashboard_replies.php
* Used by the dashboard module to create /replies
* Displays a list of content the current user has in his watched() list 
* using the theme/content/new_comments template
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/person-object
/**********************************************/
?>

<?php	

	$offset = 0;	
	if (isset($_GET['offset'])) {
		$offset = $_GET['offset'];
	}

	// load up a list of groups that this user is a member of
	$groups = $POD->getGroups(array('mem.type:!='=>'invitee','mem.userId'=>$user->get('id')));
	
	?>
	
	
	<div class="column_8">
		<?php if (isset($msg)) { ?>	
			<div class="info">
				<?php echo $msg; ?>
			</div>
		<?php } ?>
		<!-- this is where posts with new comments show up -->
		<div id="replies">
			<?php 
				$watched = $user->watched()->getOtherPage($offset);
				// since the watched list results might be cached, 
				// we should resort the list by the commentDate, 
				// which should have been updated in the cache
				$watched->sortBy('commentDate');
				$watched->output('new_comments','header','pager','New Comments','There are no new comments yet!'); 
			?>
		</div>	
	</div>
	<div class="column_4 last">
		<?php 
			$user->output('member_info');
			
		?>	

			<ul id="navigator">
				<li class="dashboard_navigator"><a href="<?php $POD->siteRoot(); ?>">New Stuff</a></li>
				<li class="dashboard_navigator dashboard_navigator_last dashboard_active"><a href="<?php $POD->siteRoot(); ?>/replies">Activity</a></li>
				<?php if ($POD->libOptions('enable_core_groups')) { 
					$groups->output('group_navigator',null,null); 
					?>
					<li class="group_navigator"><a href="<?php $POD->siteRoot(); ?>/groups">More Groups...</a></li>					
					<?php						
				} ?>
			</ul>					

			<?php $POD->output('sidebars/activity_stream'); ?>


	</div>


	<div class="clearer"></div>