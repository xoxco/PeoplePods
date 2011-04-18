<? 
	include_once("../lib/Core.php");	
	$POD = new PeoplePod(array('lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));

	$v = $_GET['q'];
	$v = mysql_real_escape_string($v);
	$sql = "SELECT distinct value FROM tags WHERE value like '$v%' limit 10;";
	$res = mysql_query($sql,$POD->DATABASE);
	while ($r = mysql_fetch_assoc($res)) { 
		echo $r['value'] . "\n";
	}	