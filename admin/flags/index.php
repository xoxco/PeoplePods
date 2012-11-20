<?php 

	include_once("../../PeoplePods.php");	
	
	$POD = new PeoplePod(array('debug'=>0,'lockdown'=>'adminUser','authSecret'=>@$_COOKIE['pp_auth']));
	$POD->changeTheme('admin');

	$offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
		
		$content = null;
		$oflags = null;
		$iflags = null;

		$user = null;
		$message = null;
		
		if (isset($_GET['removeFlag'])) { 
			
			$flag = $_GET['removeFlag'];
			$type = $_GET['type'];
			$itemId = $_GET['itemId'];
			$userId = $_GET['userId'];
			
			if ($type=="user") { 
				$obj = $POD->getPerson(array('id'=>$itemId));
			} else {
				$obj = $POD->getContent(array('id'=>$itemId));
			}
			
			$u = $POD->getPerson(array('id'=>$userId));
			
			$obj->removeFlag($flag,$u);
			if (!$obj->success()) { 
				$ret['error'] = $obj->error();
			} else {
				$ret = $_GET;
				$ret['flag'] = $flag;
				$ret['new_action'] = "add";
				$ret['old_action'] = "remove";

			}
			
			echo json_encode($ret);
			exit;
		
		} else if (isset($_GET['addFlag'])) { 
			$flag = $_GET['addFlag'];
			$type = $_GET['type'];
			$itemId = $_GET['itemId'];
			$userId = $_GET['userId'];
			
			if ($type=="user") { 
				$obj = $POD->getPerson(array('id'=>$itemId));
			} else {
				$obj = $POD->getContent(array('id'=>$itemId));
			}
			
			$u = $POD->getPerson(array('id'=>$userId));
			
			$obj->addFlag($flag,$u);
			if (!$obj->success()) { 
				$ret['error'] = $obj->error();
			} else {
				$ret = $_GET;
				$ret['flag'] = $flag;
				$ret['new_action'] = "remove";
				$ret['old_action'] = "add";

			}
			
			echo json_encode($ret);
			exit;		
		  exit;
		}
		
		if (isset($_GET['contentId'])) { 
		
			$content = $POD->getContent(array('id'=>$_GET['contentId']));
			if (!$content->success()) { 
				$message = $content->error();			
			} else {
				$iflags = $content->getInFlags();	
				if (!$content->success()) { 
					$message = $content->error();
				}
			}
		}
		
		if (isset($_GET['userId']) && $_GET['userId']!='') { 
		
			$user = $POD->getPerson(array('id'=>$_GET['userId']));
			if (!$user->success()) { 
				$message = $user->error();
			} else {
				$iflags = $user->getInFlags();
				$oflags = $user->getOutFlags();
				if (!$user->success()) { 
					$message = $user->error();
				}

			}			
		
		}
		
		
		if ($user || $content) { 
			
			if ($_GET['flag'] && $_GET['type'] && $_GET['direction']) { 
	
				$options = array();
				$options['flag.name'] = $_GET['flag'];
				$description = '';
				if ($_GET['direction'] == "in") { 
					if ($user) { 
						$options['flag.itemId'] = $user->get('id');
						$description = "People who flagged {$user->get('nick')} with the '" . $_GET['flag'] . "' flag";
					} else {
						$options['flag.itemId'] = $content->get('id');
						$description = "People who have flagged this content with the '" . $_GET['flag'] . "' flag";
					}
				} else {
					$options['flag.userId'] = $user->get('id');
					if ($_GET['type']=="user") {
						$description = "People that {$user->get('nick')} flagged with the '" . $_GET['flag'] . "' flag";
					} else {
						$description = "Content that {$user->get('nick')} flagged with the '" . $_GET['flag'] . "' flag";
					}
	
				}
	
				if ($_GET['type'] == "user") { 
				
					$results = $POD->getPeople($options,'flag.date desc',20,$offset);
				} else {
					$results = $POD->getContents($options,'flag.date desc',20,$offset);
				}
			
			}
		} else {
		
			if (isset($_GET['flag']) && isset($_GET['type'])) { 
	
				$options = array();
				$options['flag.name'] = $_GET['flag'];
				$description = '';
				if ($_GET['type']=="user") {
					$description = "People flagged with the '" . $_GET['flag'] . "' flag";
				} else {
					$description = "Content flagged with the '" . $_GET['flag'] . "' flag";
				}
	
				if ($_GET['type'] == "user") { 
					$results = $POD->getPeople($options,'flag.date desc',20,$offset);
				} else {
					$results = $POD->getContents($options,'flag.date desc',20,$offset);
				}
			
			}	
		
		
		
		}
	
	
		$POD->header();
		include_once("tools.php");
		if (isset($message)) { ?>
		
			<div class="info">
				<?php echo $message; ?>
			</div>
		
		<?php } ?>

		<div class="list_panel">
			<?php if ($oflags || $iflags) { ?>
			<ul id="content_type">
			
				<li>Choose Flag:</li>
				<?php if ($oflags) foreach ($oflags as $flag=>$type) { ?>
					<li <?php if ($_GET['flag']==$flag && $_GET['direction']=="out") {?>class="active"<?php } ?>><a href="?userId=<?php echo $_GET['userId']; ?>&flag=<?php echo $flag; ?>&type=<?php echo $type ?>&direction=out"><?php echo $flag; ?> &rarr;</a></li>
				<?php } ?>
				<?php if ($iflags) foreach ($iflags as $flag=>$type) { ?>
					<li <?php if ($_GET['flag']==$flag && $_GET['direction']=="in") {?>class="active"<?php } ?>><a href="?userId=<?php echo $_GET['userId']; ?><?php if ($_GET['contentId']){?>&contentId=<?php echo $_GET['contentId']; ?><?php } ?>&flag=<?php echo $flag; ?>&type=user&direction=in"><?php echo $flag; ?> &larr;</a></li>
				<?php } ?>
			</ul>
			<?php } ?>
	<?php	
		if (isset($results)) { ?>
			<h1 class="column_padding"><a href="<?php $POD->podRoot(); ?>/admin/flags/">Flags</a>: <a href="index.php?<?php  if(isset($content)) { echo 'contentId='.$content->id; } else if (isset($user)) { echo 'userId=' . $user->id; }?>"><?php if (isset($content)) { echo $content->headline; } else if (isset($user)) { echo $user->nick; } ?></a> &#187; <?php echo $description; ?></h1>
			<?php foreach ($results as $result) { 
					$result->set('flag',$_GET['flag'],false);
					if ($content || $user) {
						if ($content) { 
							$result->set('flag_itemId',$content->get('id'),false);
							$result->set('flag_type','content',false);
							$result->set('flag_userId',$result->get('id'),false);
		
						} else {
							$result->set('flag_type','user',false);
							if ($_GET['direction']=="out") {
								$result->set('flag_itemId',$result->get('id'),false);
								$result->set('flag_userId',$user->get('id'),false);
							} else {
								$result->set('flag_itemId',$user->get('id'),false);
								$result->set('flag_userId',$result->get('id'),false);
							}
						}
					}
				}
				
				$parameters = '';
				if (isset($_GET['userId'])) { 
					$parameters .= '&userId=' . $_GET['userId'];
				}
				if (isset($_GET['contentId'])) { 
					$parameters .= '&contentId=' . $_GET['contentId'];
				}
				if (isset($_GET['flag'])) { 
					$parameters .= '&flag=' . $_GET['flag'];
				}
				if (isset($_GET['type'])) { 
					$parameters .= '&type=' . $_GET['type'];
				}
				if (isset($_GET['direction'])) { 
					$parameters .= '&direction=' . $_GET['direction'];
				}
				$results->output('flagged','header','pager',null,'Nothing with this flag',$parameters); 
			?>
		<?php } else { 
		
			if ($oflags || $iflags) { ?>
				<div class="empty_list">
					
					<h1><a href="<?php $POD->podRoot(); ?>/admin/flags/">Flags</a>: <?php echo $content ? $content->headline : $user->nick; ?></h1>
					<?php if ($oflags) { ?>
						<p>This person has added flags to things:</p>
						<ul class="flag_list">
						<?php foreach($oflags as $flag=>$type) { ?>
							<li><a href="?userId=<?php echo $_GET['userId']; ?>&flag=<?php echo $flag; ?>&type=<?php echo $type ?>&direction=out"><strong><?php echo $flag; ?></strong></a> <?php if ($type=="content") { echo $POD->pluralize($user->flaggedCount($flag),"@number piece of content","@number pieces of content"); } else { echo $POD->pluralize($user->flaggedCount($flag),"@number person","@number people"); } ?></li>	
						<?php } ?>
						</ul>
					<?php }
					
					if ($iflags) { ?>
						<p>Flags have been added to this <?php echo $content ? 'content' : 'person'; ?>:</p>
						<ul class="flag_list">
						<?php foreach($iflags as $flag=>$type) { ?>
							<li><a href="?userId=<?php echo $_GET['userId']; ?>&contentId=<?php echo $_GET['contentId']; ?>&flag=<?php echo $flag; ?>&type=user&direction=in"><strong><?php echo $flag; ?></strong></a> Added by  <?php echo $content ? $POD->pluralize($content->flagCount($flag),'@number person','@number people') : $POD->pluralize($user->flagCount($flag),'@number person','@number people'); ?></li>					
						<?php } ?>
						</ul>
					<?php } ?>
				
				</div>
				
			<?php } else { ?>
				<div class="empty_list">

					<?php if ($content || $user) { ?>
					<h1>Flags: <?php echo $content ? $content->headline : $user->nick; ?></h1>

					<p>No flags</p>
					
					<?php } else { ?>
					
						<h1>Flags:</h1>			
						<ul class="flag_list">		
						<?
						
							$flags = $POD->getFlagList();
							foreach ($flags as $flag) { ?>
								<li><a href="<?php $POD->podRoot(); ?>/admin/flags/?flag=<?php echo $flag['name']; ?>&type=<?php echo $flag['type']; ?>"><?php if ($flag['type']=="content") { echo $POD->pluralize($flag['count'],'@number piece of content','@number pieces of content'); } else { echo $POD->pluralize($flag['count'],'@number person','@number people'); } ?> flagged <?php echo $flag['name']; ?></a></li>
							<?php } ?>					
						<?php } ?>
						</ul>

				</div>
			<?php } ?>
		
		
		<?php } ?>
		</div>
	<?
		$POD->footer(); 	
	?>