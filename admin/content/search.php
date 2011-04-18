<? 
	include_once("../../PeoplePods.php");	
	$POD = new PeoplePod(array('lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));
	$POD->changeTheme('admin');

	$mode = 'fullscreen';
	
	$conditions = array();
	if (isset($_GET['q']) && $_GET['q'] != 'Search') { 
		$conditions['headline:like'] = '%' . $_GET['q'] . '%';	
	}	

	if (isset($_GET['tag'])) {
		$conditions['tag'] = $_GET['tag'];
	}
	if (isset($_GET['type'])) {
		$conditions['type'] = $_GET['type'];
	}
	if (isset($_GET['userId'])) {
		$conditions['userId'] = $_GET['userId'];
	}
	if (isset($_GET['status'])) {
		$conditions['status'] = $_GET['status'];
	}
	if (isset($_GET['mode'])) {
		$mode = $_GET['mode'];
	}
	foreach ($conditions as $field=>$value) {
		$permalink = "$field=$value&";
	}

	if (isset($_GET['offset'])) {
		$offset = $_GET['offset'];
	} else {
		$offset = 0;
	}
	
	if (sizeof($conditions) > 0) {
		$docs = $POD->getContents($conditions,'date DESC',20,$offset);	
	} else {	
		$docs = $POD->getContents(null,'date DESC',20,$offset);
	}
	
	// serialize parameters
	$params = $_GET;
	$parameters = '';
	$tparameters ='';
	unset($params['offset']);
	foreach ($params as $key=>$val) { 
		$parameters .= "&{$key}={$val}";
		if ($key !='type') {
			$tparameters .= "&{$key}={$val}";
		}
	}
	
	
	if (isset($_GET['message'])) {
		$message = $_GET['message'];
	}

	if ($mode=='fullscreen') { 
		$POD->header();
		include_once("tools.php");
		
		$types = array();
		$sql = "SELECT DISTINCT type FROM content";
		$res = mysql_query($sql,$POD->DATABASE);
		if (mysql_num_rows($res)>0) { 
		
			while ($type = mysql_fetch_row($res)) {
				array_push($types,$type[0]);
			}
		}
		
		?>


	<div class="list_panel">
		
		<? if (@$_GET['userId']) { 
		
			$person = $POD->getPerson(array('id'=>$_GET['userId']));
			?><h1>Content created by <?= $person->nick; ?></h1><?
		} ?>

		<div id="searchoptions" style="display: none;">
			<form method="get" action="<? $POD->podRoot(); ?>/admin/content/search.php">
			<input type="hidden" name="type" value="<? if (isset($_GET['type'])) { echo $_GET['type']; } ?>" />
			<div class="column_3">
				<div class="column_padding">
					<input type="text"  value="Search" onfocus="if(this.value=='Search') { this.value=''; };" class="text" name="q" />
				</div>
			</div>
			<div class="column_3">
				<div class="column_padding">
					 Tag: <input name="tag" id="tag" value="" >
					<div id="tag_complete" class="autocomplete"></div>
				</div>
			</div>
			<div class="column_3">
				<div class="column_padding">
					<select name="status"><option value="">Any</option><option value="new">New (Unmoderated)</option><option value="approved">Approved</option><option value="featured">Featured</option></select>	
				</div>
			</div>	
			<div class="column_1 last">
				<div class="column_padding">
					<input type="submit" value="Search" />
				</div>
			</div>	
			</form>
			<div class="clearer"></div>
		</div>


		<ul id="content_type">
			<li>Content Type:</li>
			<li <? if (!isset($_GET['type'])) { ?>class="active"<? } ?>><A href="search.php?<?= $tparameters; ?>">All</a></li>
			<? foreach ($types as $type) { ?>
				<li <? if (isset($_GET['type']) && $_GET['type']==$type) {?>class="active"<? } ?>><a href="?type=<?= $type; ?><?= $tparameters; ?>"><?= $type; ?></a></li>
			<? } ?>
		</ul>

						
		<? if (isset($message)) { ?>
		
			<div class="info">
				<? echo $message; ?>
			</div>
		
		<? } ?>
		
		<? $docs->output('short','content_header','table_pager',null,'No content found',$parameters); ?>
		
	</div>
		
	<? $POD->footer();

	} else if ($mode=="addChild") {
		if (isset($message)) { ?>
		
			<div class="info">
				<? echo $message; ?>
			</div>
		
		<? } 
		
		if ($docs->count() > 0 ) {
		
			 while ($doc = $docs->getNext()) { 
			 	$doc->output('addChild');
			 }
				 
		} else {
			echo '<h3 class="column_padding">Oops, no content found.</h3>';
		}
				
	}
?>
<script>
		$('#userId_autofill').autocomplete(PODROOT+'/admin/userAutocomplete.php',{
		}).result(function(event,data,formatted) {
			$('#userId').val(data[1]);
		});
</script>