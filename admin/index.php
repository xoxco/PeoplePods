<? 

	require_once("../PeoplePods.php");	
	$POD = new PeoplePod(array('debug'=>0,'lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));
	$POD->changeTheme('admin');
	if (!$POD->success()) {
		error_log($POD->error());
	}
		
	if (isset($_GET['flushcache'])) { 
		$POD->cacheflush();
		$message = "Cache Flushed!";
	}
	$POD->header();

	// lets generate some stats!
	
	$today = date("Y-m-d");
	
/*
  	$totalMembers = $POD->getPeople(array('memberSince:lte'=>$today));  
	$membersToday = $POD->getPeople(array('lastVisit:gt'=>$today),'lastVisit desc',10);
	$membersJoined = $POD->getPeople(array('memberSince:gt'=>$today));
	$docsCreated = $POD->getContents(array('date:gt'=>$today));
	$newComments = $POD->getComments(array('date:gt'=>$today),'date desc',5);
	$total = $membersJoined->totalCount()  + $totalMembers->totalCount();
*/

	$this_database_update = 0.9;
	
	$last_version = $POD->libOptions('last_database_update');
	if (!$last_version) { $last_version = 0; }


	// load each type of thing
	// get total, total for today, total for the last week

	// members, content, comments, files, groups

	$members = $POD->getPeople(array('memberSince:gte'=>date('Y-m-d') . ' 00:00:00'));
	$stats['members_today'] = $members->totalCount();
	$members = $POD->getPeople(array('memberSince:gte'=>date('Y-m-d',strtotime('-7 days'))));
	$stats['members_week'] = $members->totalCount();
	$members = $POD->getPeople(array(),'memberSince DESC',10);
	$stats['members_total'] = $members->totalCount();

	$visitors = $POD->getPeople(array('lastVisit:gte'=>date('Y-m-d') . ' 00:00:00'));
	$stats['visits_today'] = $visitors->totalCount();
	$visitors = $POD->getPeople(array('lastVisit:gte'=>date('Y-m-d',strtotime('-7 days'))));
	$stats['visits_week'] = $visitors->totalCount();
	$visitors = $POD->getPeople(array('lastVisit:gte'=>date('Y-m-d',strtotime('-30 days'))),'lastVisit DESC',10);
	$stats['visits_total'] = $visitors->totalCount();


	$content = $POD->getContents(array('date:gte'=>date('Y-m-d') . ' 00:00:00'));
	$stats['content_today'] = $content->totalCount();
	$content = $POD->getContents(array('date:gte'=>date('Y-m-d',strtotime('-7 days'))));
	$stats['content_week'] = $content->totalCount();
	$content = $POD->getContents(array(),'date desc',10);
	$stats['content_total'] = $content->totalCount();
	$active_content = $POD->getContents(array(),'commentDate DESC',10);

	$comments = $POD->getComments(array('date:gte'=>date('Y-m-d') . ' 00:00:00'));
	$stats['comments_today'] = $comments->totalCount();
	$comments = $POD->getComments(array('date:gte'=>date('Y-m-d',strtotime('-7 days'))));
	$stats['comments_week'] = $comments->totalCount();
	$comments = $POD->getComments(array(),'date desc',10);
	$stats['comments_total'] = $comments->totalCount();
	
	$files = $POD->getFiles(array('date:gte'=>date('Y-m-d') . ' 00:00:00'));
	$stats['files_today'] = $files->totalCount();
	$files = $POD->getFiles(array('date:gte'=>date('Y-m-d',strtotime('-7 days'))));
	$stats['files_week'] = $files->totalCount();
	$files = $POD->getFiles(array(),'date desc',10);
	$stats['files_total'] = $files->totalCount();
	
	$groups = $POD->getGroups(array('date:gte'=>date('Y-m-d') . ' 00:00:00'));
	$stats['groups_today'] = $groups->totalCount();
	$groups = $POD->getGroups(array('date:gte'=>date('Y-m-d',strtotime('-7 days'))));
	$stats['groups_week'] = $groups->totalCount();	
	$groups = $POD->getGroups(array(),'date desc',10);
	$stats['groups_total'] = $groups->totalCount();

	?>
			<script type="text/javascript">	
				function doSearch() { 
				
					window.location="<? $POD->podRoot(); ?>/admin/" + $('#search_type').val()+ "/search.php?q=" + escape($('#search_q').val());
					return false;
				}
			</script>	
	<div id="tools">
		<ul>
			<li id="section_name">Command Center</li><li><a href="content/" title="Create new content...">Add Content</a></li><li><a href="people/" 	 title="Create a new member...">Add Person</a></li><li><a href="groups/" 	 title="Create a new group...">Add Group</a></li><li class="last">	
				<form method="get" onsubmit="return doSearch();">
				<input name="q" id="search_q" default="Search" class="repairField" size="15" />
				<select name="type" id="search_type">
					<option value="people">People</option>
					<option value="content">Content</option>
				</select>
				</form>
			</li>
		</ul>
	</div>
	<div class="list_panel">
		<div id="update_check">
			<script type="text/javascript" src="http://peoplepods.net/versioncheck/<? echo $POD->libOptions('peoplepods_api'); ?>?version=<?= $POD->VERSION; ?>"></script>
		</div>

		<h1 style="margin:0px"><? $POD->siteName(); ?></h1>
	</div>

	<? if (isset($message)) { ?>
		<div class="info"><?= $message; ?></div>	
	<? } ?>
	<? if ($last_version < $this_database_update) { ?>
		<div class="info">
			Updates to the database schema are required!  <a href="options/upgrade.php">Click to upgrade.</a>
		</div>
	<? } ?>	
	
	<div id="options">
		<div class="stats option_set">
			<div class="stat">
				<a href="<? $POD->podRoot(); ?>/admin/people/search.php?mode=newest"><?= $POD->pluralize($stats['members_total'],'<span class="number">@number</span> member','<span class="number">@number</span> members'); ?></a>
				<?= $stats['members_today']; ?> joined today<br /> 
				<?= $stats['members_week']; ?> over the last week
			</div>
			<div class="stat">
				<a href="<? $POD->podRoot(); ?>/admin/people/search.php?mode=last" title="Members active during the last 30 days"><?= $POD->pluralize($stats['visits_total'],'<span class="number">@number active</span>','<span class="number">@number active</span>'); ?></a>
				<?= $stats['visits_today']; ?> visited today<br />
				<?= $stats['visits_week']; ?> over the last week
			</div>
			<div class="stat">
				<a href="<? $POD->podRoot(); ?>/admin/content/search.php"><?= $POD->pluralize($stats['content_total'],'<span class="number">@number</span> post','<span class="number">@number</span> posts'); ?></a>
				<?= $stats['content_today']; ?> created today<br />
				<?= $stats['content_week']; ?>  over the last week
			</div>	
			<div class="stat">
				<a href="<? $POD->podRoot(); ?>/admin/comments/"><?= $POD->pluralize($stats['comments_total'],'<span class="number">@number</span> comment','<span class="number">@number</span> comments'); ?></a>
				<?= $stats['comments_today']; ?> posted today<br />
				<?= $stats['comments_week']; ?>  over the last week
			</div>	
			<div class="stat">
				<a href="<? $POD->podRoot(); ?>/admin/files/"><?= $POD->pluralize($stats['files_total'],'<span class="number">@number</span> file','<span class="number">@number</span> files'); ?></a>
				<?= $stats['files_today']; ?> uploaded today<br />
				<?= $stats['files_week']; ?> over the last week
			</div>	
			<div class="stat">
				<a href="<? $POD->podRoot(); ?>/admin/groups/search.php"><?= $POD->pluralize($stats['groups_total'],'<span class="number">@number</span> group','<span class="number">@number</span> groups'); ?></a>
				<?= $stats['groups_today']; ?> created today<br />
				<?= $stats['groups_week']; ?> over the last week
			</div>	
		</div>	
		<div class="option_set">		
			<? $POD->cachestatus(); ?>
			<a href="?flushcache=now">Flush Cache</a>	
		</div>
		<div class="option_set">
			<h3>Documentation</h3>
			<ul>
				<li><a href="http://peoplepods.net/readme">README</a></li>
				<li><a href="http://peoplepods.net/readme/sdk">SDK</a></li>
				<li><a href="http://peoplepods.net/readme/themes">Themes</a></li>
				<li><a href="http://peoplepods.net/readme/object-definitions">Object Definitions</a></li>
				<li><a href="http://peoplepods.net/readme/admin-tools">Admin Tools</a></li>
			</ul>
		</div>
	</div>
	<div class="list_panel panel_with_options">
		
		<h1>Recent Activity</h1>
		
		<div class="quick_view" id="quick_people" >
			<h2>People</h2>	
			
			<h3>New Signups</h3>
			<? $members->output('list_item','list_header','list_footer'); ?>
			<h3>Recent Visitors</h3>
			<? $visitors->output('list_item','list_header','list_footer'); ?>	
		</div>


		<div class="quick_view" id="quick_content">
			<h2>Content</h2>
			<h3>Recently Added Content</h3>
			<? $content->output('list_item','list_header','list_footer'); ?>

			<h3>Recently Active Content</h3>
			<? $active_content->output('list_item','list_header','list_footer'); ?>
		</div>

		<div class="quick_view" id="quick_comments">
			<h2>Comments</h2>
			<? $comments->output('comment.preview'); ?>
		</div>
	
		<br clear="left" />

	
	</div>
	<?
	
	$POD->footer();
	?>
