<? 

	include_once("../../PeoplePods.php");	
	
	$POD = new PeoplePod(array('debug'=>0,'lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));
	$POD->changeTheme('admin');
	

	if (isset($_GET['delete'])) { 
		// delete this comment
		
		$comment = $POD->getComment(array('id'=>$_GET['delete']));
		if ($comment->success()) { 
			$comment->delete();
			if (!$comment->success()) { 
				$msg = $comment->error();
			} else {
				$msg = "Comment deleted.";
			}
		} else {
			$msg = $comment->error();
		}
		
		
	}

	$types = array();
	$sql = "SELECT DISTINCT type FROM comments";
	$res = mysql_query($sql,$POD->DATABASE);
	if (mysql_num_rows($res)>0) { 
	
		while ($type = mysql_fetch_row($res)) {
			if ($type[0]=='') { 
				$type[0] = 'comment';
			}
			
			array_push($types,$type[0]);
		}
	}


	$POD->header('Comments');
	include_once('tools.php');
	
	if (isset($_GET['id'])) { 
		// edit this comment
		$comment = $POD->getComment(array('id'=>$_GET['id']));
		if ($comment->success()) { 
			$comment->output('comment.edit');		
		} else { ?>
			<div class="info">
				<?= $comment->error(); ?>
			</div>
 		<? }
		
	} else if ($_POST) { 
	
		$comment = $POD->getComment(array('id'=>$_POST['id']));
		if ($comment->success()) { 
		
			$comment->comment = $_POST['comment'];
			$comment->type = $_POST['type'];
		
			$comment->save();
			if ($comment->success()) {
			
				$msg = "Comment saved.";
			
				foreach ($_POST as $field => $value) {

					if (preg_match("/meta_(.*)/",$field,$matches)) { 

						// if this field is a meta field, add it!
						$field = $matches[1];
						if (is_numeric($field)) { // this is a new meta field name (meta_1 meta_2 meta_3) 
						// we need to match this with its value (meta_value_1)
							$new_field = $value;
							$new_value = $_POST['meta_value_' . $field];
							if ($new_field) { 
								$comment->addMeta($new_field,$new_value);
							}

						} else if (strpos($field,"value")===0) { // this is a new meta value... we don't need to do anything
							next;
						} else { // this is an existing field, it has its value with it.  	
							$comment->addMeta($field,$value);
						}						
						
					}				
				
				}

			
			} else {
				$msg = $comment->error();
			}	
			

		} else {
			$msg = $comment->error();
		} 
	
		if (isset($msg)) { ?>
			<div class="info">
				<?= $msg; ?>
			</div>
 		<? }
		
		$comment->output('comment.edit');
	
	} else { 
	

		if (isset($_GET['type'])) { 	
			$type = $_GET['type'];
			if ($type=='comment') {
				$type='null';
			}
			$comments = $POD->getComments(array('type'=>$type),'date desc');

		} else {
			$comments = $POD->getComments(array(),'date desc');
		}

		if (isset($_GET['offset'])) { 
			$comments->getOtherPage($_GET['offset']);
		}

		if (isset($msg)) { ?>
			<div class="info">
				<?= $msg; ?>
			</div>
 		<? } ?>
 		
		<div class="list_panel">
			<ul id="content_type">
			<li>Comment Type:</li>
			<li <? if (!isset($_GET['type'])) { ?>class="active"<? } ?>><A href="index.php">All</a></li>
			<? foreach ($types as $type) { ?>
				<li <? if (isset($_GET['type']) && $_GET['type']==$type) {?>class="active"<? } ?>><a href="?type=<?= $type; ?>"><?= $type; ?></a></li>
			<? } ?> 		
			</ul>
			<? $comments->output('comment.list','comment_header','table_pager',null,'No comments','&type='.$type); ?>
		</div>
	<? }
	$POD->footer();