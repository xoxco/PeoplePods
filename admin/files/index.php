<? 

	include_once("../../PeoplePods.php");	
	
	$POD = new PeoplePod(array('debug'=>0,'lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));
	$POD->changeTheme('admin');


	if (isset($_GET['id'])) { 
	
		$file = $POD->getFile(array('id'=>$_GET['id']));
		if (!$file->success()) { 
			$message = $file->error();
		} else {
	
			if ($file->parent()) { 
				$content = $file->parent();
			} else if ($file->group()) { 
				$group = $file->group();
			} else {
				$user = $file->author();
			}
	
		}
	
		$POD->header();
		include_once("tools.php");
		if (isset($message)) { ?>
		
			<div class="info">
				<? echo $message; ?>
			</div>
		
		<? } 
		if ($file->success()) {
			$file->output();
		}			
		$POD->footer(); 
	
	
	} else {

		// load files based on a userId
		// or based on a docId
		// allow new files to be uploaded
		
		$content = null;
		$files = null;
		$user = null;
		$group = null;
		$message = null;
		$newfile = null;
		
		if (isset($_GET['contentId'])) { 
		
			$content = $POD->getContent(array('id'=>$_GET['contentId']));
			if (!$content->success()) { 
				$message = $content->error();			
			} else {
				$files = $content->files();
				$title = $content->headline;
			}
			$newfile = $POD->getFile(array('contentId'=>$content->get('id')));
		} else if (isset($_GET['userId'])) { 
		
			$user = $POD->getPerson(array('id'=>$_GET['userId']));
			if (!$user->success()) { 
				$message = $user->error();
			} else {
				$files = $user->files();
				$title = $user->nick;
			}
			$newfile = $POD->getFile(array('userId'=>$user->get('id')));
		
		} else if (isset($_GET['groupId'])) { 
		
			$group = $POD->getGroup(array('id'=>$_GET['groupId']));
			if (!$group->success()) { 
				$message = $group->error();
			} else {
				$files = $group->files();
				$title = $group->groupname;
			}
			$newfile = $POD->getFile(array('groupId'=>$group->get('id')));
		
		} else { 
		
			$files = $POD->getFiles();
			$title = "All Files";		
		}
		
		if (isset($_POST['delete'])) { 
			$f = $POD->getFile(array('id'=>$_POST['id']));
			if ($f->success()) { 
				$f->delete();
				if (!$f->success()) { 
					$message = $f->error();
				} else {
					$message = "File deleted!";
				}
			} else {
				$message = $f->error();
			}
		
		} 		
	
		if (isset($_POST['name'])) { 


				if (isset($_GET['contentId'])) { 
				
					$content->addFile($_POST['name'],$_FILES['file'],stripslashes($_POST['description']));
					if (!$content->success()) {
						$message = $content->error();
					} else {
						$message = "File uploaded!";
					}

				}							
				if (isset($_GET['userId'])) { 
				
					$user->addFile($_POST['name'],$_FILES['file'],stripslashes($_POST['description']));
					if (!$user->success()) {
						$message = $user->error();
					} else {
						$message = "File uploaded!";
					}

				}
				if (isset($_GET['groupId'])) { 
				
					$group->addFile($_POST['name'],$_FILES['file'],stripslashes($_POST['description']));
					if (!$group->success()) {
						$message = $user->error();
					} else {
						$message = "File uploaded!";
					}

				}
			
			
						
			$files->fill();
		}
	
		if (isset($_GET['offset'])) {
			$files->getOtherPage($_GET['offset']);
		}
	
		$POD->header();
		include_once("tools.php");
		if ($message) { ?>
		
			<div class="info">
				<? echo $message; ?>
			</div>
		
		<? } ?>
		<div class="list_panel">
			<h1>Files: <?= $title; ?></h1>
			<?	
				
				$files->output('short','file_header','table_pager');
				
			?>
		</div>
		<?	if ($newfile) { ?>
			<div class="panel">
				<? $newfile->output('upload'); ?>
			</div>
		<? } ?>

	<?	
		$POD->footer(); 
	
	?>
<? } // if !$_GET['id'] ?>