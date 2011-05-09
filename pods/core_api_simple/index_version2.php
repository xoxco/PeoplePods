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
	$POD = new PeoplePod(array('authSecret'=>@$_COOKIE['pp_auth'],'debug'=>0));


	// $method defines what action we are going to take
	$method = @$_GET['method'];

	// $data will hold all of information for our response
	$data = array();
	
	// in order to avoid duplication in the methods below
	// let's load any objects specified up here, and properly handle them
	// at the end, we may have $content, $user, $comment, $file, and/or $group set.
	if (@$_GET['content']) {
	
		if (is_numeric($_GET['content'])) { 
			$content = $POD->getContent(array('id'=>$_GET['content']));
		} else {
			$content = $POD->getContent(array('stub'=>$_GET['content']));
		}
		
		if (!$content->success()) { 
			echo results(array('error'=>$content->error()));
			exit;
		}
		
		$data['content'] = $content->asArray();
	}

	if (@$_GET['person']) {
	
		if (is_numeric($_GET['person'])) { 
			$person = $POD->getPerson(array('id'=>$_GET['person']));
		} else {
			$person = $POD->getPerson(array('nick'=>$_GET['person']));
		}
		
		if (!$person->success()) { 
			echo results(array('error'=>$person->error()));
			exit;
		}
		
		$data['person'] = $person->asArray();
	}
	if (@$_GET['group']) {
	
		if (is_numeric($_GET['group'])) { 
			$group = $POD->getGroup(array('id'=>$_GET['group']));
		} else {
			$group = $POD->getGroup(array('stub'=>$_GET['group']));
		}
		
		if (!$group->success()) { 
			echo results(array('error'=>$group->error()));
			exit;
		}
		
		$data['group'] = $group->asArray();
	}
	if (@$_GET['comment']) {
	
		if (is_numeric($_GET['comment'])) { 
			$comment = $POD->getComment(array('id'=>$_GET['comment']));
		
			if (!$comment->success()) { 
				echo results(array('error'=>$comment->error()));
				exit;
			}
			
			$data['comment'] = $comment->asArray();
		} else {
			// if not a comment id, a comment parameter meant for an addComment call.
		}
	}
	if (@$_GET['file']) {
	
		if (is_numeric($_GET['file'])) { 
			$file = $POD->getFile(array('id'=>$_GET['file']));
		}
		
		if (!$file->success()) { 
			echo results(array('error'=>$file->error()));
			exit;
		}
		
		$data['file'] = $file->asArray();
	}
	if (@$_GET['alert']) {
	
		if (is_numeric($_GET['alert'])) { 
			$alert = $POD->getAlert(array('id'=>$_GET['alert']));
		}
		
		if (!$alert->success()) { 
			echo results(array('error'=>$alert->error()));
			exit;
		}
		
		$data['alert'] = $alert->asArray();
	}	
	
	
	// process the actual methods!
	switch ($method) {

/* Content methods ****************************************************************************/

		case 'content.toggleFlag':
			requireAuthentication($POD);
			$flag = @$_GET['flag'];
			if (!$flag) { 
				$data['error'] = 'No valid flag was specified!';
			} else {
			
				if ($flag == 'watching') {
					$res = $POD->currentUser()->toggleWatch($content);
				} else if ($flag == 'favorite') {
					$res = $POD->currentUser()->toggleFavorite($content);
				} else {
					$res = $content->toggleFlag($flag,$POD->currentUser());
				}
				if ($content->success()) { 
					if ($res==0) {
						$data['state'] = 'off';
					} else {
						$data['state'] = 'on';
					}
				} else {
					$data['error'] = $content->error();
				}
			}
			echo results($data);
			break;

		case 'content.addFlag':
			requireAuthentication($POD);
			$flag = @$_GET['flag'];
			if (!$flag) { 
				$data['error'] = 'No valid flag was specified!';
			} else {
			
				$res = $content->addFlag($flag,$POD->currentUser());
				if ($content->success()) { 
					$data['state'] = 'on';
				} else {
					$data['error'] = $content->error();
				}
			}
			echo results($data);
			break;


		case 'content.removeFlag':
			requireAuthentication($POD);
			$flag = @$_GET['flag'];
			if (!$flag) { 
				$data['error'] = 'No valid flag was specified!';
			} else {
			
				$res = $content->removeFlag($flag,$POD->currentUser());
				if ($content->success()) { 
					$data['state'] = 'off';
				} else {
					$data['error'] = $content->error();
				}
			}
			echo results($data);
			break;

		case 'content.addComment':
			requireAuthentication($POD);
			$comment = @$_GET['comment'];
			$template = @$_GET['template'] ? $_GET['template'] : 'comment';
			if (!$comment) {
				$data['error'] = 'Your comment was blank!';
			} else { 
				$c = $content->addComment($comment);
				$data['comment'] = $c->asArray();
				$data['comments'] = $content->comments()->asArray();
				$POD->startBuffer();
				$content->comments()->output($template);		
				$data['html'] = $POD->endBuffer();
			}
			echo results($data);
			break;
		case 'content.delete':
			requireAuthentication($POD);

			$parent = $content->parent();
			$content->delete();
			if (!$content->success()) { 
				$data['error'] = $content->error();
			}
			echo results($data);
			break;
		case 'content.markAsRead':
			requireAuthentication($POD);
			$content->markCommentsAsRead();
			if (!$content->success()) { 
				$data['error'] = $content->error();
			}
			echo results($data);
			break;


/* People methods ****************************************************************************/

		case 'person.toggleFlag':
			requireAuthentication($POD);
			$flag = @$_GET['flag'];
			if (!$flag) { 
				$data['error'] = 'No valid flag was specified!';
			} else {
			
				if ($flag == 'friends') {
					if ($POD->currentUser()->isFriendsWith($person)) { 
						$POD->currentUser()->removeFriend($person);
						$res = 0;
					} else {
						$POD->currentUser()->addFriend($person);
						$res = 1;					
					}
					$p = $POD->currentUser();
				} else {
					$res = $person->toggleFlag($flag,$POD->currentUser());
					$p = $person;
				}
				if ($POD->currentUser()->success()) { 
					if ($res==0) {
						$data['state'] = 'off';
					} else {
						$data['state'] = 'on';
					}
				} else {
					$data['error'] = $POD->currentUser()->error();
				}
			}
			echo results($data);
			break;

		case 'person.addFlag':
			requireAuthentication($POD);
			$flag = @$_GET['flag'];
			if (!$flag) { 
				$data['error'] = 'No valid flag was specified!';
			} else {
				if ($flag == 'friends') {		
					$POD->currentUser()->addFriend($person);
					$p = $POD->currentUser();
				} else {
					$person->addFlag($flag,$POD->currentUser());
					$p=$person;
				}
				if ($p->success()) { 
					$data['state'] = 'on';
				} else {
					$data['error'] = $p->error();
				}
			}
			echo results($data);
			break;


		case 'person.removeFlag':
			requireAuthentication($POD);
			$flag = @$_GET['flag'];
			if (!$flag) { 
				$data['error'] = 'No valid flag was specified!';
			} else {
			
				$res = $person->removeFlag($flag,$POD->currentUser());
				if ($POD->currentUser()->success()) { 
					$data['state'] = 'off';
				} else {
					$data['error'] = $person->error();
				}
			}
			echo results($data);
			break;
			
		case 'person.addComment':
			requireAuthentication($POD);
			$comment = @$_GET['comment'];
			$template = @$_GET['template'] ? $_GET['template'] : 'comment';
			if (!$comment) {
				$data['error'] = 'Your comment was blank!';
			} else { 
				$c = $person->addComment($comment);
				$data['comment'] = $c->asArray();
				$data['comments'] = $person->comments()->asArray();
				$POD->startBuffer();
				$person->comments()->output($template);		
				$data['html'] = $POD->endBuffer();
			}
			echo results($data);
			break;


/* Comment methods ****************************************************************************/

		case 'comment.toggleFlag':
			requireAuthentication($POD);
			$flag = @$_GET['flag'];
			if (!$flag) { 
				$data['error'] = 'No valid flag was specified!';
			} else {
			
				$res = $comment->toggleFlag($flag,$POD->currentUser());
				if ($comment->success()) { 
					if ($res==0) {
						$data['state'] = 'off';
					} else {
						$data['state'] = 'on';
					}
				} else {
					$data['error'] = $comment->error();
				}
			}
			echo results($data);
			break;


		case 'comment.addFlag':
			requireAuthentication($POD);
			$flag = @$_GET['flag'];
			if (!$flag) { 
				$data['error'] = 'No valid flag was specified!';
			} else {
			
				$res = $comment->addFlag($flag,$POD->currentUser());
				if ($comment->success()) { 
					$data['state'] = 'on';
				} else {
					$data['error'] = $comment->error();
				}
			}
			echo results($data);
			break;


		case 'comment.removeFlag':
			requireAuthentication($POD);
			$flag = @$_GET['flag'];
			if (!$flag) { 
				$data['error'] = 'No valid flag was specified!';
			} else {
			
				$res = $comment->removeFlag($flag,$POD->currentUser());
				if ($comment->success()) { 
					$data['state'] = 'off';
				} else {
					$data['error'] = $comment->error();
				}
			}
			echo results($data);
			break;
			
		case 'comment.delete':
			requireAuthentication($POD);

			$template = @$_GET['template'] ? $_GET['template'] : 'comment';
			$parent = $comment->parent();
			$comment->delete();
			if (!$comment->success()) { 
				$data['error'] = $comment->error();
			}
			echo results($data);
			break;
			
/* Group methods ****************************************************************************/

		case 'group.toggleFlag':
			requireAuthentication($POD);
			$flag = @$_GET['flag'];
			if (!$flag) { 
				$data['error'] = 'No valid flag was specified!';
			} else {
			
				$res = $group->toggleFlag($flag,$POD->currentUser());
				if ($group->success()) { 
					if ($res==0) {
						$data['state'] = 'off';
					} else {
						$data['state'] = 'on';
					}
				} else {
					$data['error'] = $group->error();
				}
			}
			echo results($data);
			break;


		case 'group.addFlag':
			requireAuthentication($POD);
			$flag = @$_GET['flag'];
			if (!$flag) { 
				$data['error'] = 'No valid flag was specified!';
			} else {
			
				$res = $group->addFlag($flag,$POD->currentUser());
				if ($group->success()) { 
					$data['state'] = 'on';
				} else {
					$data['error'] = $group->error();
				}
			}
			echo results($data);
			break;


		case 'group.removeFlag':
			requireAuthentication($POD);
			$flag = @$_GET['flag'];
			if (!$flag) { 
				$data['error'] = 'No valid flag was specified!';
			} else {
			
				$res = $group->removeFlag($flag,$POD->currentUser());
				if ($group->success()) { 
					$data['state'] = 'off';
				} else {
					$data['error'] = $group->error();
				}
			}
			echo results($data);
			break;
			
		case 'group.changeMemberType':
			requireAuthentication($POD);
			if ($_GET['membership'] == '') { 
				$group->removeMember($person);
				$type='';
			} else {
				$type=$group->changeMemberType($person,$_GET['membership']);			
			}
			$data['membership'] = $type;

			if (!$group->success()) { 
				$data['error'] = $group->error();
			} 
			echo results($data);
			break;

			
		case 'group.addMember':
			requireAuthentication($POD);
			if (!$person) {
				$person = $POD->currentUser();
			}
			$group->addMember($person);
			if (!$group->success()) {
				$data['error'] = $group->error();
			} else {
				$data['membership'] = $group->isMember($person);
			}
			echo results($data);
			break;


		case 'group.removeMember':
			requireAuthentication($POD);
			$group->removeMember($person);
			if (!$group->success()) { 
				$data['error'] = $group->error();
			} 
			echo results($data);
			break;
		
/* File methods ****************************************************************************/

		case 'file.toggleFlag':
			requireAuthentication($POD);
			$flag = @$_GET['flag'];
			if (!$flag) { 
				$data['error'] = 'No valid flag was specified!';
			} else {
			
				$res = $file->toggleFlag($flag,$POD->currentUser());
				if ($file->success()) { 
					if ($res==0) {
						$data['state'] = 'off';
					} else {
						$data['state'] = 'on';
					}
				} else {
					$data['error'] = $file->error();
				}
			}
			echo results($data);
			break;

		case 'file.addFlag':
			requireAuthentication($POD);
			$flag = @$_GET['flag'];
			if (!$flag) { 
				$data['error'] = 'No valid flag was specified!';
			} else {
			
				$res = $file->addFlag($flag,$POD->currentUser());
				if ($file->success()) { 
					$data['state'] = 'on';
				} else {
					$data['error'] = $file->error();
				}
			}
			echo results($data);
			break;


		case 'file.removeFlag':
			requireAuthentication($POD);
			$flag = @$_GET['flag'];
			if (!$flag) { 
				$data['error'] = 'No valid flag was specified!';
			} else {
			
				$res = $file->removeFlag($flag,$POD->currentUser());
				if ($file->success()) { 
					$data['state'] = 'off';
				} else {
					$data['error'] = $file->error();
				}
			}
			echo results($data);
			break;
		case 'file.delete':
			requireAuthentication($POD);
			$file->delete();
			if (!$file->success()) { 
				$data['error'] = $file->error();
			}
			echo results($data);
			break;
			
/* File methods ****************************************************************************/
		case 'alert.markAsRead':
			requireAuthentication($POD);
			$alert->markAsRead();
			if (!$alert->success()) { 
				$data['error'] = $alert->error();
			}
			echo results($data);
			break;
			
/*******************************************************************************************/

		default:
			echo results(array('error'=>"No valid API method specified! (method = {$method})"));
			break;
	}
	
	
	function requireAuthentication($POD) {
		if (!$POD->isAuthenticated()) {
			echo json_encode(array('error'=>'Access denied!  Please login first!'));
			exit;
		}
	}
	
	
	function results($data,$format='json') {
		// if a callback function has been passed in as a parameter, this is a jsonP request
		if ($func = $_GET['callback']) { 
		
			return "{$func}(" . json_encode($data) . ")";
		
		} else {
		
			return json_encode($data);
		}
	}