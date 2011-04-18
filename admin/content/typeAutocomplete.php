<? 
	include_once("../lib/Core.php");	
	$POD = new PeoplePod(array('lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));

	$v = $_POST['type'];	
	$v = mysql_real_escape_string($v);
	$sql = "SELECT distinct type FROM content WHERE type like '$v%' limit 10;";
	$res = mysql_query($sql,$POD->DATABASE);
	echo "<ul>";
	while ($r = mysql_fetch_assoc($res)) { 
		echo "<li>" . $r['type'] . "</li>\n";
	}
	echo "</ul>";
	