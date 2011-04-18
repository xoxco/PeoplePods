<? 
	include_once("../lib/Core.php");	
	$POD = new PeoplePod(array('lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));

	$v = $_GET['q'];
	
	$v = mysql_real_escape_string($v);
	$sql = "SELECT distinct name FROM meta WHERE name like '$v%' and name !='adminUser' limit 10;";
	$res = $POD->executeSQL($sql);
	if ($res) { 
		while ($r = mysql_fetch_assoc($res)) { 
			echo "{$r['name']}\n";
		}
	} else {
		echo "FUCKLE";
	}
	