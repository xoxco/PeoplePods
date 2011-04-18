			<form method="post" action="<? $user->POD->podRoot(); ?>/admin/people/" enctype="multipart/form-data" id="edit_member" class="valid">

<div id="options">
	<div class="option_set">

				<input type="submit" class="button" name="save" value="Save" />
	<? if ($user->saved()) { ?>

				<ul>
					<? if ($user->get('verificationKey') != '') { ?>
						<li><a href="<? $user->POD->podRoot(); ?>/admin/people/?id=<? $user->write('id'); ?>&action=verify" class="tool">Verify</a></li>
					<? } ?>
					<li><a href="<? $user->POD->podRoot(); ?>/admin/people/?id=<? $user->write('id'); ?>&action=welcome" class="tool">Send Welcome Email</a></li>
					<li><a href="<? $user->POD->podRoot(); ?>/admin/people/?id=<? $user->write('id'); ?>&action=delete" onclick="return confirm('Are you sure you want to delete this person and all of their posts, comments and friends FOREVER?');" class="tool">Delete Person</a></li>
				</ul>
		<? } ?>
	</div>
	<? if ($user->saved()) { ?>
	
	<div class="option_set">

			<p class="input"><label for="stub">Stub:</label><input name="stub" class="text" value="<? $user->htmlspecialwrite('stub'); ?>" /></p>
			
			<p><b>View <? $user->permalink(); ?></b></p>
			<p>Member since <em><? echo date_format(date_create($user->get('memberSince')),'M jS Y'); ?></em> and last logged in <em><? echo intval((time() - strtotime($user->get('lastVisit'))) / 86400); ?> days ago</em></p>
			<? if ($user->get('invitedBy')) { 
				$inviter = $this->POD->getPerson(array('id'=>$user->get('invitedBy'))); 
				?>
				<br />Invited by <a href="<? $user->POD->podRoot(); ?>/admin/people/?id=<? $inviter->write('id'); ?>"><? $inviter->write('nick'); ?></a>
			<? } ?>

			<P>Status: <? if ($user->get('verificationKey')=='') { ?>Verified<? } else { ?><b>UNVERIFIED</b><? } ?></P>
		
			Friends: <? echo $user->friends()->totalCount(); ?><Br />
			Followers: <? echo $user->followers()->totalCount(); ?>


	
	</div>
	<? } ?>

</div>
<div class="panel panel_with_options">


		<h1>
			<? if ($user->saved()) { ?>
			<ul class="attachments">			
				<li><a href="<? $user->POD->podRoot(); ?>/admin/content/search.php?userId=<? $user->write('id'); ?>" class="tool">Content</a></li>
				<li><a href="#comments" class="tool">Comments</a></li>
				<li><a href="<? $POD->podRoot(); ?>/admin/files/index.php?userId=<? $user->write('id'); ?>" class="tool"><? echo $user->files()->totalCount(); ?> <? echo $user->POD->pluralize($user->files()->totalCount(),'File','Files'); ?></a></li>
				<li><a href="<? $POD->podRoot(); ?>/admin/flags/index.php?userId=<? $user->write('id'); ?>" class="tool">Flags</a></li>
			</ul>
			<? } ?>
			<? if (!$user->saved()) {?>New Person<? } else { ?>Edit Person<? } ?>
		</h1>
				<input type="hidden" name="action" value="update" />
				<? if ($user->get('id')) { ?>
					<input type="hidden" name="id" value="<? $user->write('id'); ?>" />
				<? } ?>
				
				<p class="input"><label for="nick">Username:</label><input type="text" id="nick" name="nick" class="required text" value="<? $user->write('nick'); ?>" /></p>
				<p class="input"><label for="fullname">Full Name:</label><input type="text" id="fullname" name="fullname" class="text" value="<? $user->write('fullname'); ?>" /></p>

				<p class="input"><label for="email">Email:</label><input type="text" id="email" name="email" class="required email text" value="<? $user->write('email'); ?>" /></p>
				<? if (!$user->get('id')) { ?>
				<p class="input"><label for="password">Password:</label><input type="text" id="password" name="password" class="required text" value="" /></p>
				<? } ?>


				
				<p class="input" id="person_img" <? if (!$user->saved() || !($file = $user->files()->contains('file_name','img'))) {?>style="display: none;"<? } ?>>
					<label for="link">Primary Image:</label> <? if ($user->saved()) { ?><a href="<? $POD->podRoot(); ?>/admin/files?userId=<? $user->write('id'); ?>">More Files</a><? } ?>
					<? if ($file) { ?>
						<br />			
						<a href="<? $POD->podRoot(); ?>/admin/files?id=<?= $file->id; ?>"><img src="<?= $file->src('100',true); ?>" border="0"></a>
					<? } else { ?>
						<input name="img" type="file" class="text" />	
					<? } ?>
				</p>		
				
				<p class="input" id="user_tags" <? if (!$user->saved() || $user->tags()->count()==0) {?>style="display: none;"<? } ?>>
					<label for="tags">Tags:</label>
					<input id="tags" name="tags" class="text" value="<? echo $user->tagsAsString(); ?>" />
				</p>


				<? if (!$user->saved() || !$user->files()->contains('file_name','img')) {?>
					<a href="#" onclick="return showOptional(this,'#person_img');" class="optional_field">+ Add Image</a>
				<? } ?>
				
				<? if (!$user->saved() || $user->tags()->count()==0) {?>
					<a href="#" onclick="return showOptional(this,'#user_tags');" class="optional_field">+ Add Tags</a>
				<? } ?>

				<h2>Additional Information</h3>
						
				<? $meta = $user->getMeta();
				if ($meta) { 
					foreach ($meta as $field=>$value) { ?>
						<p class="input">
							<label for="meta_<? echo $field; ?>"><? echo $field; ?>:</label>
							<? if (strlen($value) > 50) { ?>
								<textarea class="text" name="meta_<? echo $field; ?>"><? echo htmlspecialchars($value); ?></textarea>
							<? } else { ?>
								<input class="text" type="text" name="meta_<? echo $field; ?>" id="meta_<? echo $field; ?>" value="<? echo htmlspecialchars($value); ?>"/>
							<? } ?>
						</p>				
					<? }
				} ?>

				<div id="new_meta_fields">
				</div>
				<div id="new_meta" style="display:none;">
						<label for="new_meta_name">Name the new field:</label>
						<input id="new_meta_name" type="text" class="meta_lookup text" /><input type="submit" onclick="return addMetaField();" value="Add to Form">
				</div>
				
				<a href="#" onclick="return showOptional(this,'#new_meta');" class="optional_field" id="add_field_link">+ Add Field</a>

			
				<h2>Permissions</h2>
				<p><input type="checkbox" name="adminUser" value="1" <? if ($user->get('adminUser')) { ?>checked<? } ?> />&nbsp;&nbsp;Admin User</p>
</div>
</form>

<? if ($user->saved()) { ?>
<div class="panel panel_with_options">
		<h1><a name="comments"></a>Comments</h1>
			<? 
				$offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
				$comments = $POD->getComments(array('userId'=>$this->get('id')),'date desc',5.,$offset);
				$comments->output('comment','header','pager',null,'No comments','&id='.$user->id . '#comments');
			?>

</div>

<div class="panel panel_with_options">
		<form method="post" action="<? $user->POD->podRoot(); ?>/admin/people/" id="edit_password">
			<input type="hidden" name="action" value="password" />
			<h3>Change Password</h3>
			<? if ($user->get('id')) { ?>
				<input type="hidden" name="id" value="<? $user->write('id'); ?>" />
			<? } ?>
			<p class="input"><label for="password">New Password:</label><input type="text" id="password" name="password" class="required text" value="" /></p>
			<p>PeoplePods does not store passwords in the database.  An encrypted hash is stored instead.  The user will not be informed of this password change automatically.</P>
			<p>This tool should only be used when a member has requested their password be reset to a specific value. In most situations, members should be referred to the password reset tool.</p>
			<p><input type="submit" class="button" name="save" value="Change Password" />
		</form>

</div>
<? } ?>