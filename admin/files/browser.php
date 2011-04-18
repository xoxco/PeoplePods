<? 

	include_once("../../PeoplePods.php");
	
	$POD = new PeoplePod(array('lockdown'=>'adminuser','authSecret'=>@$_COOKIE['pp_auth']));
	$POD->changeTheme('admin');
	
	$mode = $_GET['mode'];
	
	if ($mode =='upload') { 
		// someone just uploaded or modified an image
	
	
	
	} else if ($mode=='list') {
	
		$content = $POD->getContent(array('id'=>$_GET['docId']));

		?>
		
		<div id="fileBrowser_list">
		
		<?
		// output an upload form
		$content->output('browser.upload');
				
		// output list of files
		$content->files()->output('browser.list','list_header','list_footer',null,'No files attached to this content.');
		?>
		
		</div>
		<div id="fileBrowser_details" style="display:none;">
			
		</div>		
		<?
	} else if ($mode=='details') { 

		$content = $POD->getContent(array('id'=>$_GET['docId']));
		$file = $content->files()->contains('id',$_GET['fileId']);
		$file->output('browser.details');		
	
	}