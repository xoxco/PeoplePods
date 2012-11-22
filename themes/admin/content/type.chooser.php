<?php

	// we're gonna do some raw sql stuff here to pull all the different values for type
	$sql = "SELECT distinct type FROM content;";
	$res = mysql_query($sql,$POD->DATABASE);
	$types = array();
	while ($t = mysql_fetch_assoc($res)) {
	
		$types[$t['type']] = $t['type'];
	} 
	mysql_free_result($res);
	$types['document']='document';

?>

<div class="panel">

<h1>Create Content</h1>


<p>What type of content would you like to create?</p>

<?php foreach ($types as $type) { ?>

	<p><a href="?type=<?php echo urlencode($type); ?>"><?php echo ucfirst($type); ?></a></p>

<?php } ?>

	<form method="get">
		<input name="type" class="repairField" default="new content type" />
		<input type="submit" value="Create" />	
	</form>

</div>