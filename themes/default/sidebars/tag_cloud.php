<div class="padded sidebar" id="tag_cloud_sidebar">
<?php 
	$tags = $POD->getTags();
	
	while ($tag = $tags->getNext()) { 
	
		$tag->output('tag.cloud');
	}

?>
</div>