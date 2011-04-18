 <? 
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* core_api_simple/index.php
* Handles simple requests to /api
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme
/**********************************************/

	include_once("../../PeoplePods.php");
	$POD = new PeoplePod(array('authSecret'=>@$_COOKIE['pp_auth'],'debug'=>2));


	if ($POD->libOptions('enable_core_api_simple')) { 
		$method = $_GET['method'];
	
		$POD->tolog("API CALL METHOD: $method");
	
	
		if ($method=="alert.markAsRead") { 
			
			if ($POD->isAuthenticated()) {
				$alert = $POD->getAlert(array('id'=>$_GET['id']));
				$alert->markAsRead();
				if ($alert->success()) { 
					echo json_encode($alert->asArray());
				} else {
					echo json_encode(array('error'=>$alert->error(),'id'=>$_GET['id']));
				}
			} else {
				echo json_encode(array('error'=>'PERMISSION DENIED','id'=>$_GET['docId']));
			}			
				
		}
	
		if ($method == "markAsRead") { 
	
			if ($POD->isAuthenticated()) {
				$doc = $POD->getContent(array('id'=>$_GET['docId']));
				if ($doc->success()) { 
					$doc->markCommentsAsRead();
					if ($doc->success()) { 
						echo json_encode($doc->asArray());
					} else {
						echo json_encode(array('error'=>$doc->error(),'id'=>$_GET['docId']));
					}
				} else {
					echo json_encode(array('error'=>$doc->error(),'id'=>$_GET['docId']));
				}
			} else {
				echo json_encode(array('error'=>'PERMISSION DENIED','id'=>$_GET['docId']));
			}
		}
	
	
		if ($method == "addWatch") { 
	
			if ($POD->isAuthenticated()) {
				$doc = $POD->getContent(array('id'=>$_GET['docId']));
				if ($doc->success()) { 
					$POD->currentUser()->addWatch($doc);
					if ($POD->currentUser()->success()) {
						echo json_encode($doc->asArray());
					} else {
						echo json_encode(array('error'=>$POD->currentUser()->error(),'id'=>$_GET['docId']));
					}
				} else {
						echo json_encode(array('error'=>$doc->error(),'id'=>$_GET['docId']));
				}
			} else {
				echo json_encode(array('error'=>'PERMISSION DENIED','id'=>$_GET['docId']));
			}
		}	
		if ($method == "removeWatch") { 
	
			if ($POD->isAuthenticated()) {
				$doc = $POD->getContent(array('id'=>$_GET['docId']));
				if ($doc->success()) { 
					$POD->currentUser()->removeWatch($doc);
					if ($POD->currentUser()->success()) {
						echo json_encode($doc->asArray());
					} else {
						echo json_encode(array('error'=>$POD->currentUser()->error(),'id'=>$_GET['docId']));
					}
				} else {
						echo json_encode(array('error'=>$doc->error(),'id'=>$_GET['docId']));
				}
			} else {
				echo json_encode(array('error'=>'PERMISSION DENIED','id'=>$_GET['docId']));
			}
		}	
	
		if ($method=="addComment") { 
		
			if ($POD->isAuthenticated() && $POD->currentUser()->get('verificationKey')=='') {
				
				$did = $_GET['docId'];
				$comment = $_GET['comment'];
				$userId = $POD->currentUser()->get('id');
							
				$doc = $POD->getContent(array('id'=>$did));
				if ($doc->success()) {
					$comment = $doc->addComment($comment);
					if ($doc->success()) { 
						echo json_encode($comment->asArray());
					} else {
						echo json_encode(array('error'=>$comment->error(),'id'=>$_GET['docId']));
					}
				} else {
				echo json_encode(array('error'=>$doc->error(),'id'=>$_GET['docId']));
			}
			} else {
				echo json_encode(array('error'=>'PERMISSION DENIED. You must be logged in and verified to comment.','id'=>$_GET['docId']));
			}		
		}
		if ($method=="getComments") { 
	
			$data = array();	
			if ($POD->isAuthenticated()) {
				
				$did = $_GET['docId'];
				$doc = $POD->getContent(array('id'=>$did));
				if ($doc->success()) {
					ob_start();
					while ($comment = $doc->comments()->getNext()) { 
							$comment->output();
					}
					$data['comments_as_html'] = ob_get_contents();
					ob_end_clean();
				} else {
					$data['error'] = $doc->error();		
				}
			} else {
					$data['error'] = 'PERMISSION DENIED';		
			}		
//			echo json_encode($data);
			echo $data['comments_as_html'];
	
		}	
		if ($method=="getCommentsSince") { 
	
			$data = array();	
			if ($POD->isAuthenticated()) {
				
				$did = $_GET['docId'];
				$lastComment = $_GET['lastComment'];
				$userId = $POD->currentUser()->get('id');
				$doc = $POD->getContent(array('id'=>$did));
				$last = 0;
				$count = 0;
				if ($doc->success()) {
					while ($last <= $lastComment) { 
						while ($comment = $doc->comments()->getNext()) { 
							$last = $comment->get('id');
						}
						$doc->comments()->fill();
						sleep(1);
						$count++;
						if ($count > 3) { 
							$data['last'] = $last;
							echo json_encode($data);
							return;
						}
					}			
					
					$doc->comments()->reset();
					$data['last'] = $last;
					$data['comments'] = $doc->comments()->asArray();
					ob_start();
					while ($comment = $doc->comments()->getNext()) { 
						if ($comment->get('id') > $lastComment) { 
							$comment->output();
						}
					}
					$data['comments_as_html'] = ob_get_contents();
					ob_end_clean();
				} else {
					$data['error'] = $doc->error();		
				}
			} else {
					$data['error'] = 'PERMISSION DENIED';		
			}		
			echo json_encode($data);
	
		}
		
		if ($method == "removeComment") { 
						
			if ($POD->isAuthenticated()) { 
				$comment = $POD->getComment(array('id'=>$_GET['comment']));
				$comment->delete();
				if ($comment->success()) { 
					echo json_encode(array('id'=>$_GET['comment']));
				} else {
					echo json_encode(array('error'=>$comment->error(),'id'=>$_GET['comment']));
				}
			} else {
					echo json_encode(array('error'=>'PERMISSION DENIED','id'=>$_GET['comment']));
			}
		
		}
		if ($method == "deleteDocument") {
		
			$doc = $POD->getContent(array('id'=>$_GET['id']));
			if ($doc->success()) { 
				$doc->delete();
				if ($doc->success()) {
					echo json_encode(array('id'=>$_GET['id']));
				} else {
					echo json_encode(array('error'=>$doc->error(),'id'=>$_GET['id']));
				}
			} else {
				echo json_encode(array('error'=>$doc->error(),'id'=>$_GET['id']));
			}
		}
		if ($method == "addFavorite") { 
	
			if ($POD->isAuthenticated()) {
				$doc = $POD->getContent(array('id'=>$_GET['docId']));
				if ($doc->success()) { 
					$POD->currentUser()->addFavorite($doc);
					if ($POD->currentUser()->success()) {
						echo json_encode($doc->asArray());
					} else {
						echo json_encode(array('error'=>$POD->currentUser()->error(),'id'=>$_GET['docId']));
					}
				} else {
						echo json_encode(array('error'=>$doc->error(),'id'=>$_GET['docId']));
				}
			} else {
				echo json_encode(array('error'=>'PERMISSION DENIED','id'=>$_GET['docId']));
			}
		}	
		if ($method == "removeFavorite") { 
	
			if ($POD->isAuthenticated()) {
				$doc = $POD->getContent(array('id'=>$_GET['docId']));
				if ($doc->success()) { 
					$POD->currentUser()->removeFavorite($doc);
					if ($POD->currentUser()->success()) {
						echo json_encode($doc->asArray());
					} else {
						echo json_encode(array('error'=>$POD->currentUser()->error(),'id'=>$_GET['docId']));
					}
				} else {
						echo json_encode(array('error'=>$doc->error(),'id'=>$_GET['docId']));
				}
			} else {
				echo json_encode(array('error'=>'PERMISSION DENIED','id'=>$_GET['docId']));
			}
		}	
	
		if ($method == "vote") {
			if ($POD->isAuthenticated()) {
				$doc = $POD->getContent(array('id'=>$_GET['docId']));
				if ($doc->success()) {
					if ($doc->vote($_GET['vote'])) { 
						$data = $doc->asArray();
						$data['lastVote'] = $_GET['vote'];
						echo json_encode($data);					
					} else {
						echo json_encode(array('error'=>$doc->error(),'id'=>$_GET['docId']));
					}
				} else {
					echo json_encode(array('error'=>$doc->error(),'id'=>$_GET['docId']));
				}
			} else {
				echo json_encode(array('error'=>'PERMISSION DENIED','id'=>$_GET['docId']));
			}	
		}
	
	
		
		
		if ($method == "addFriend") {
	
			if ($POD->isAuthenticated()) {
				$newfriend = $POD->getPerson(array('id'=>$_GET['id']));
				$POD->currentUser()->addFriend($newfriend);
				if ($POD->currentUser()->success()) { 
					echo json_encode($newfriend->asArray());
				} else {
					echo json_encode(array('error'=>$POD->currentUser()->error(),'id'=>$_GET['id']));
				}
			} else {
					 echo json_encode(array('error'=>'PERMISSION DENIED','id'=>$_GET['id']));
			}
		
		}
		
		if ($method == "removeFriend") {
		
			if ($POD->isAuthenticated()) {
				$newfriend = $POD->getPerson(array('id'=>$_GET['id']));
				$POD->currentUser()->removeFriend($newfriend);
				if ($POD->currentUser()->success()) { 
					echo json_encode($newfriend->asArray());
				} else {
					echo json_encode(array('error'=>$POD->currentUser()->error(),'id'=>$_GET['id']));
				}
			} else {
					echo json_encode(array('error'=>'PERMISSION DENIED','id'=>$_GET['id']));
			}
		
		}	
		
	
		if ($method == "removeMember") {
		
			if ($POD->isAuthenticated()) {
				$group = $POD->getGroup(array('id'=>$_GET['group']));
				if (!$group->success()) {
					echo json_encode(array('error'=>$group->error(),'id'=>$_GET['id']));
					return;			
				}
				$member = $POD->getPerson(array('id'=>$_GET['id']));
				if (!$member->success()) {
					echo json_encode(array('error'=>$member->error(),'id'=>$_GET['id']));
					return;			
				}
							
				$group->removeMember($member);
				if ($group->success()) { 
					echo json_encode($member->asArray());
				} else {
					echo json_encode(array('error'=>$group->error(),'id'=>$_GET['id']));
				}
			} else {
					echo json_encode(array('error'=>'PERMISSION DENIED','id'=>$_GET['id']));
			}
		
		}	
	
	
		if ($method == "changeMemberType") {
			if ($POD->isAuthenticated()) {
				$group = $POD->getGroup(array('id'=>$_GET['group']));
				if (!$group->success()) {
					echo json_encode(array('error'=>'Group Error: ' . $group->error(),'id'=>$_GET['id']));
					return;			
				}
				$member = $POD->getPerson(array('id'=>$_GET['id']));
				if (!$member->success()) {
					echo json_encode(array('error'=>'Person Error: ' . $member->error(),'id'=>$_GET['id']));
					return;			
				}
							
				$type=$group->changeMemberType($member,$_GET['type']);
				$member->set('membership',$type,false);
				if ($group->success()) { 
					echo json_encode($member->asArray());
				} else {
					echo json_encode(array('error'=>'Group Error: ' . $group->error(),'id'=>$_GET['id']));
				}
			} else {
					echo json_encode(array('error'=>'PERMISSION DENIED','id'=>$_GET['id']));
			}
		
		}	
	} // if pod is enabled
	
?>