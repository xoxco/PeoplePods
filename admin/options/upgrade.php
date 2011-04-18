<?

	include_once("../../PeoplePods.php");	
	$POD = new PeoplePod(array('lockdown'=>'adminUser','authSecret'=>$_COOKIE['pp_auth']));

	$this_database_update = 0.9;

	$last_version = $POD->libOptions('last_database_update');
	if (!$last_version) { $last_version = 0; }


	if (isset($_GET['confirm'])) {
	
		echo "<ul>";
		
		
		if ($last_version < 0.7) { 
			echo "<li>Checking permissions...</li>";
			$POD->saveLibOptions();
			if (!$POD->success()) { 
			echo "<li><strong>" . $POD->error() . "</strong></li>";
			} else {
	
				echo "<li>Creating table meta_tmp...</li>";	
				$sql = "CREATE TABLE meta_tmp(type enum('group','content','user'),itemId bigint(12), name varchar(100),value text,id bigint(12) NOT NULL UNIQUE auto_increment, unique index u (type,itemId,name));";
				$res = mysql_query($sql,$POD->DATABASE);
				if (!$res) { 
					echo "<li><strong>SQL Error: " . mysql_error() . "</strong></li>";
				} else { 	
					echo "<li>Copying meta values into meta_tmp...</li>";
					$sql = "REPLACE INTO meta_tmp (type,itemId,name,value) SELECT type,itemId,name,value FROM meta;";
					$res = mysql_query($sql,$POD->DATABASE);
					if (!$res) { 
						echo "<li><strong>SQL Error: " . mysql_error() . "</strong></li>";
					} else { 	
						echo "<li>Deleting values from meta... (If this errors, you may have to restore your db from backup)</li>";
						$sql = "DELETE FROM meta";
						$res = mysql_query($sql,$POD->DATABASE);
						if (!$res) { 
							echo "<li><strong>SQL Error: " . mysql_error() . "</strong></li>";
						} else { 	
							echo "<li>Adding unique index to the meta table...</li>";
							$sql = "ALTER TABLE meta ADD UNIQUE INDEX u (type,itemId,name);";
							$res = mysql_query($sql,$POD->DATABASE);
							if (!$res) { 
								echo "<li><strong>SQL Error: " . mysql_error() . "</strong></li>";
							} else {
								echo "<li>Copying values from meta_tmp back into meta... (If this errors, you may have to restore your db from backup)</li>";
								$sql = "INSERT INTO meta (type,itemId,name,value) SELECT type,itemId,name,value FROM meta_tmp;";
								$res = mysql_query($sql,$POD->DATABASE);
	
								if (!$res) { 
									echo "<li><strong>SQL Error: " . mysql_error() . "</strong></li>";
								} else {
									echo "<li>Cleaning up by removing meta_tmp table...</li>";		
									$sql = "DROP TABLE meta_tmp;";
									$res = mysql_query($sql,$POD->DATABASE);
									if (!$res) { 
										echo "<li><strong>SQL Error: " . mysql_error() . "</strong></li>";
									} else {
										$POD->setLibOptions('last_database_update','0.7');
										$POD->saveLibOptions();
										if (!$POD->success()) { 
											echo "<li><strong>" . $POD->error() . "</strong></li>";
										} else {
											echo "<li>Upgrade to 0.7 complete.</li>";
										}
									}
								}
							}
						}
					}
				}
			}
		}	
		if ($last_version < 0.71) {
		
			echo "<li>Checking permissions...</li>";
			$POD->saveLibOptions();
			if (!$POD->success()) { 
				echo "<li><strong>" . $POD->error() . "</strong></li>";
			} else {
				echo "<li>Altering Meta Table..</li>";	
				$sql = "ALTER TABLE meta CHANGE type type enum('content','user','group','comment','file');";
				$res = mysql_query($sql,$POD->DATABASE);
				if (!$res) { 
						echo "<li><strong>SQL Error: " . mysql_error() . "</strong></li>";
				} else {
					echo "<li>Altering Meta Table..</li>";	
					$sql = "ALTER TABLE flags CHANGE type type enum('content','user','group','comment','file');";
					$res = mysql_query($sql,$POD->DATABASE);
					if (!$res) { 
							echo "<li><strong>SQL Error: " . mysql_error() . "</strong></li>";
					} else {

						echo "<li>Creating table activity...</li>";	
						$sql = "CREATE TABLE activity(userId bigint(12),target varchar(100),targetType varchar(10),count int default 0,message varchar(255),gid varchar(25),date datetime,id bigint(12) NOT NULL UNIQUE auto_increment, index uid (userId),index tid (target,targetType),unique index gidx (gid));";
						$res = mysql_query($sql,$POD->DATABASE);
						if (!$res) { 
							echo "<li><strong>SQL Error: " . mysql_error() . "</strong></li>";
						} else {
							$POD->setLibOptions('last_database_update','0.71');
							$POD->saveLibOptions();
							if (!$POD->success()) { 
								echo "<li><strong>" . $POD->error() . "</strong></li>";
							} else {
								echo "<li>Upgrade to 0.71 complete.</li>";
							}
						}
					}
				}
			}
		}
		
		if ($last_version < 0.8) { 

			echo "<li>Checking permissions...</li>";
			$POD->saveLibOptions();
			if (!$POD->success()) { 
				echo "<li><strong>" . $POD->error() . "</strong></li>";
			} else {
				echo "<li>Altering invites table... adding email field</li>";
				$sql = "ALTER TABLE invites ADD email varchar(255) after groupId;";
				$res = mysql_query($sql,$POD->DATABASE);
				if (!$res) { 
						echo "<li><strong>SQL Error: " . mysql_error() . "</strong></li>";
				} else {					
					echo "<li>Altering files table...increasing file_name to 60 chars...</li>";
					$sql = "ALTER TABLE files change file_name file_name varchar(60);";
					$res = mysql_query($sql,$POD->DATABASE);
					if (!$res) { 
							echo "<li><strong>SQL Error: " . mysql_error() . "</strong></li>";
					} else {					
						echo "<li>Altering files table...increasing original_name to 60 chars...</li>";
						$sql = "ALTER TABLE files change original_name original_name varchar(60);";
						$res = mysql_query($sql,$POD->DATABASE);
						if (!$res) { 
								echo "<li><strong>SQL Error: " . mysql_error() . "</strong></li>";
						} else {					
							echo "<li>Altering files table...increasing original_name to 60 chars...</li>";
							$sql = "DROP TABLE activity;";
							$res = mysql_query($sql,$POD->DATABASE);

							$sql = "CREATE TABLE `activity` (`userId` bigint(12) default NULL, `targetUserId` bigint(12) default NULL, `targetContentId` bigint(12) default NULL, `targetContentType` varchar(25) default NULL, `resultContentId` bigint(12) default NULL, `resultContentType` varchar(25) default NULL, `message` varchar(255) default NULL, `targetMessage` varchar(255) default NULL, `userMessage` varchar(255) default NULL, `gid` varchar(25) default NULL, `date` datetime default NULL, `id` bigint(12) NOT NULL auto_increment, UNIQUE KEY `id` (`id`), UNIQUE KEY `gidx` (`gid`), KEY `uid` (`userId`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
							$res = mysql_query($sql,$POD->DATABASE);
							if (!$res) { 
								echo "<li><strong>SQL Error: " . mysql_error() . "</strong></li>";
							} else {
								$POD->setLibOptions('last_database_update','0.8');
								$POD->saveLibOptions();
								if (!$POD->success()) { 
									echo "<li><strong>" . $POD->error() . "</strong></li>";
								} else {
									echo "<li>Upgrade to 0.8 complete!</li>";
								}
							}
						}
					}
				}
			}
		}
		if ($last_version < 0.81) { 

			echo "<li>Checking permissions...</li>";
			$POD->saveLibOptions();
			if (!$POD->success()) { 
				echo "<li><strong>" . $POD->error() . "</strong></li>";
			} else {
			
				$updates = array();
				$updates[] = array('desc'=>'Add groupId field to files','sql'=>"alter table files add groupId bigint(12) after contentId,add index gid(groupId);");
				$updates[] = array('desc'=>'Adding profileId to comments','sql'=>'alter table comments add profileId bigint(12) after contentId,add index pid(profileId);');
				$updates[] = array('desc'=>'Adding hidden field to content table','sql'=>'alter table content add hidden int(1) default 0;');
				$updates[] = array('desc'=>'Add fullname to users table','sql'=>'alter table users add fullname varchar(255) after nick;');
				$updates[] = array('desc'=>'Add stub to users table','sql'=>'alter table users add stub varchar(255) after fullname;');
				$updates[] = array('desc'=>'Populate stub field','sql'=>'update users set stub=nick where stub is null;');
				$updates[] = array('desc'=>'Populate fullname field','sql'=>'update users set fullname=nick where fullname is null;');
				$updates[] = array('desc'=>'Change friendId to targetId, drop toId in messages','sql'=>'alter table messages change friendId targetUserId bigint(12), drop toId;');
				$updates[] = array('desc'=>'Add alerts table','sql'=>"CREATE TABLE `alerts` (
				  `id` bigint(12) NOT NULL AUTO_INCREMENT,
				  `userId` bigint(12) DEFAULT NULL,
				  `targetUserId` bigint(12) DEFAULT NULL,
				  `targetContentId` bigint(12) DEFAULT NULL,
				  `targetContentType` varchar(25) DEFAULT NULL,
				  `message` varchar(255) DEFAULT NULL,
				  `date` datetime DEFAULT NULL,
				  `status` enum('new','read') DEFAULT 'new',
				  PRIMARY KEY (`id`),
				  KEY `uid` (`targetUserId`),
				  KEY `targetidx` (`targetContentId`,`targetContentType`)
				) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=latin1;"
				);

				$success = true;
				foreach ($updates as $update) { 
					echo "<li>" . $update['desc'] . "</li>\n";
					$res = mysql_query($update['sql'],$POD->DATABASE);
					if (!$res) { 
						echo "<li><strong>SQL Error: " . mysql_error() . "</strong></li>";
						$success = false;
					}

				}

				if ($success) { 
					$POD->setLibOptions('last_database_update','0.81');
					$POD->saveLibOptions();
					if (!$POD->success()) { 
						echo "<li><strong>" . $POD->error() . "</strong></li>";
					} else {
						echo "<li>Upgrade to 0.81 complete!</li>";
					}
				}
			}
		}
		if ($last_version < 0.9) { 

			echo "<li>Checking permissions...</li>";
			$POD->saveLibOptions();
			if (!$POD->success()) { 
				echo "<li><strong>" . $POD->error() . "</strong></li>";
			} else {
			
				$updates = array();
				$updates[] = array('desc'=>'Add weight field to tags','sql'=>'alter table tags add weight bigint(12) default 0 after value;');
				$updates[] = array('desc'=>'Allow tags to be added to any object','sql'=>'alter table tagRef change contentId itemId bigint(12), change type type enum("content","user","group","comment","file") DEFAULT NULL;');
				$updates[] = array('desc'=>'Add lookup index to tagRef table','sql'=>'alter table tagRef add index lookup (type,itemId);');
				$updates[] = array('desc'=>'Update existing tags','sql'=>'update tagRef set type="content";');
				$success = true;
				foreach ($updates as $update) { 
					echo "<li>" . $update['desc'] . "</li>\n";
					$res = mysql_query($update['sql'],$POD->DATABASE);
					if (!$res) { 
						echo "<li><strong>SQL Error: " . mysql_error() . "</strong></li>";
						$success = false;
					}

				}

				if ($success) { 
					$POD->setLibOptions('last_database_update','0.9');
					$POD->saveLibOptions();
					if (!$POD->success()) { 
						echo "<li><strong>" . $POD->error() . "</strong></li>";
					} else {
						echo "<li>Upgrade to 0.9 complete!</li>";
					}
				}
			}
		}


		echo "</ul>";
	
	} else { 
		
		
	$POD->changeTheme('admin');
	$POD->header(); 
	
	?>
	<div class="column_padding">
		<h1>Upgrade</h1>
	<?
		if ($last_version < $this_database_update) { ?>
		
			<p>PeoplePods needs to make updates to your database.  We suggest you make a backup of your database first!</p>
			<p><a href="upgrade.php?confirm=destruct+alpha+alpha+destruct">UPGRADE</a></p>
		
		<? } else { ?>
		
			<p>Your PeoplePods schema is up to date.</p>
			
		<? } 	
	
	$POD->footer(); 
	
	}


?>