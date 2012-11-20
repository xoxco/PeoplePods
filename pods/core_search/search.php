<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* search/index.php
* Search content or people
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/messaging
/**********************************************/
	include_once("../../PeoplePods.php");
	$POD = new PeoplePod(array('debug'=>0,'authSecret'=>@$_COOKIE['pp_auth']));
	if (!$POD->libOptions('enable_core_search')) { 
		header("Location: " . $POD->siteRoot(false));
		exit;
	}
	
	if (isset($_GET['offset'])) {
		$offset = $_GET['offset'];
	} else {
		$offset = 0;
	}

	$POD->tolog("DOING SEARCH"); 
	if ($keyword = @$_GET['q']) { 
		$docs = $POD->getContents(array('or'=>array('headline:like'=>"%$keyword%",'body:like'=>"%$keyword%")),null,20,$offset);
	} else if ($keyword = @$_GET['p']) { 
		$people = $POD->getPeople(array('or'=>array('nick:like'=>"%$keyword%",'email:like'=>"%$keyword%")),null,20,$offset);
	} 	
	$POD->tolog("DONE");

	$POD->header('Search results for "' . $keyword . '"','/feeds/search/' . $keyword);
	?>
	<div class="column_8">


				<form method="get" action="<?php $POD->siteRoot(); ?>/search">
				<p>
					<label for="q">Search:</label>
					<input name="q" id="q" value="<?php echo htmlspecialchars(@$_GET['q']); ?>" />
					<input type="submit" class="button" value="Search" />
				</p>
			</form>	
		<?php if (isset($_GET['q'])) { ?>	
				<?php 
					if ($docs->success()) {
						$docs->output('search_results','header','pager','Search Results','No posts found!',"&q=$keyword"); 
					} else { ?>
						<div class="info">
							<?php echo $docs->error(); ?>
						</div>
					<?php }
				?>
		<?php } ?>
		<?php if (isset($_GET['p'])) { ?>
				<?php 
					if ($people->success()) { 
						$people->output('search_results','header','pager','People Search','Nobody found!',"&p=$keyword");
					} else {?>
						<div class="info">
							<?php echo $people->error(); ?>
						</div>
					<?php }
				 ?>		
		<?php } ?>
	</div>
	
	<div class="column_4 structure_only">
		
		<?php $POD->output('sidebars/ad_unit'); ?>

		<?php $POD->output('sidebars/tag_cloud'); ?>

		<?php $POD->output('sidebars/recent_visitors'); ?>
		
	</div>	

	<?php $POD->footer(); ?>

