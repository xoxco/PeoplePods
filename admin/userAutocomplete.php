<? 
	include_once("../lib/Core.php");	
	$POD = new PeoplePod(array('lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));

	$q = $_GET['q'];
	$q = mysql_real_escape_string($q);
	$sql = "SELECT distinct id,nick FROM users WHERE nick like '$q%' limit 10;";
	$res = mysql_query($sql,$POD->DATABASE);
	while ($r = mysql_fetch_assoc($res)) { 
		echo "{$r['nick']}|{$r['id']}\n";
	}
	