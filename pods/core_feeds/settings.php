<?

	$rewrite_rules = array(
			'^feeds/(.*)'=>'core_feeds/feed.php?args=$1', // set up /feeds as the base url for all rss feeds
			'^lists/(.*)'=>'core_feeds/list.php?args=$1', // set up /lists as the base url for all html lists
			'^lists$'=>'core_feeds/list.php', // set up /lists handler that redirects to the homepage (no filters, all posts)
			'^feeds$'=>'core_feeds/feed.php', // set up /feeds as a feed of EVERYTHING

			);
	
	$POD->registerPOD('core_feeds','RSS Feeds',$rewrite_rules,array());

?>