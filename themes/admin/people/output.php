			<form method="post" action="<?php $user->POD->podRoot(); ?>/admin/people/" enctype="multipart/form-data" id="edit_member" class="valid">

<div id="options">
	<div class="option_set">

				<input type="submit" class="button" name="save" value="Save" />
	<?php if ($user->saved()) { ?>

				<ul>
					<?php if ($user->get('verificationKey') != '') { ?>
						<li><a href="<?php $user->POD->podRoot(); ?>/admin/people/?id=<?php $user->write('id'); ?>&action=verify" class="tool">Verify</a></li>
					<?php } ?>
					<li><a href="<?php $user->POD->podRoot(); ?>/admin/people/?id=<?php $user->write('id'); ?>&action=welcome" class="tool">Send Welcome Email</a></li>
					<li><a href="<?php $user->POD->podRoot(); ?>/admin/people/?id=<?php $user->write('id'); ?>&action=delete" onclick="return confirm('Are you sure you want to delete this person and all of their posts, comments and friends FOREVER?');" class="tool">Delete Person</a></li>
				</ul>
		<?php } ?>
	</div>
	<?php if ($user->saved()) { ?>
	
	<div class="option_set">

			<p class="input"><label for="stub">Stub:</label><input name="stub" class="text" value="<?php $user->htmlspecialwrite('stub'); ?>" /></p>
			
			<p><b>View <?php $user->permalink(); ?></b></p>
			<p>Member since <em><?php echo date_format(date_create($user->get('memberSince')),'M jS Y'); ?></em> and last logged in <em><?php echo intval((time() - strtotime($user->get('lastVisit'))) / 86400); ?> days ago</em></p>
			<?php if ($user->get('invitedBy')) { 
				$inviter = $this->POD->getPerson(array('id'=>$user->get('invitedBy'))); 
				?>
				<br />Invited by <a href="<?php $user->POD->podRoot(); ?>/admin/people/?id=<?php $inviter->write('id'); ?>"><?php $inviter->write('nick'); ?></a>
			<?php } ?>

			<P>Status: <?php if ($user->get('verificationKey')=='') { ?>Verified<?php } else { ?><b>UNVERIFIED</b><?php } ?></P>
		
			Friends: <?php echo $user->friends()->totalCount(); ?><Br />
			Followers: <?php echo $user->followers()->totalCount(); ?>


	
	</div>
	<?php } ?>

</div>
<div class="panel panel_with_options">


		<h1>
			<?php if ($user->saved()) { ?>
			<ul class="attachments">			
				<li><a href="<?php $user->POD->podRoot(); ?>/admin/content/search.php?userId=<?php $user->write('id'); ?>" class="tool">Content</a></li>
				<li><a href="#comments" class="tool">Comments</a></li>
				<li><a href="<?php $POD->podRoot(); ?>/admin/files/index.php?userId=<?php $user->write('id'); ?>" class="tool"><?php echo $user->files()->totalCount(); ?> <?php echo $user->POD->pluralize($user->files()->totalCount(),'File','Files'); ?></a></li>
				<li><a href="<?php $POD->podRoot(); ?>/admin/flags/index.php?userId=<?php $user->write('id'); ?>" class="tool">Flags</a></li>
			</ul>
			<?php } ?>
			<?php if (!$user->saved()) {?>New Person<?php } else { ?>Edit Person<?php } ?>
		</h1>
				<input type="hidden" name="action" value="update" />
				<?php if ($user->get('id')) { ?>
					<input type="hidden" name="id" value="<?php $user->write('id'); ?>" />
				<?php } ?>
				
				<p class="input"><label for="nick">Username:</label><input type="text" id="nick" name="nick" class="required text" value="<?php $user->write('nick'); ?>" /></p>
				<p class="input"><label for="fullname">Full Name:</label><input type="text" id="fullname" name="fullname" class="text" value="<?php $user->write('fullname'); ?>" /></p>

				<p class="input"><label for="email">Email:</label><input type="text" id="email" name="email" class="required email text" value="<?php $user->write('email'); ?>" /></p>
				<?php if (!$user->get('id')) { ?>
				<p class="input"><label for="password">Password:</label><input type="text" id="password" name="password" class="required text" value="" /></p>
				<?php } ?>


				
				<p class="input" id="person_img" <?php if (!$user->saved() || !($file = $user->files()->contains('file_name','img'))) {?>style="display: none;"<?php } ?>>
					<label for="link">Primary Image:</label> <?php if ($user->saved()) { ?><a href="<?php $POD->podRoot(); ?>/admin/files?userId=<?php $user->write('id'); ?>">More Files</a><?php } ?>
					<?php if ($file) { ?>
						<br />			
						<a href="<?php $POD->podRoot(); ?>/admin/files?id=<?php echo $file->id; ?>"><img src="<?php echo $file->src('100',true); ?>" border="0"></a>
					<?php } else { ?>
						<input name="img" type="file" class="text" />	
					<?php } ?>
				</p>		
				
				<p class="input" id="user_tags" <?php if (!$user->saved() || $user->tags()->count()==0) {?>style="display: none;"<?php } ?>>
					<label for="tags">Tags:</label>
					<input id="tags" name="tags" class="text" value="<?php echo $user->tagsAsString(); ?>" />
				</p>


				<?php if (!$user->saved() || !$user->files()->contains('file_name','img')) {?>
					<a href="#" onclick="return showOptional(this,'#person_img');" class="optional_field">+ Add Image</a>
				<?php } ?>
				
				<?php if (!$user->saved() || $user->tags()->count()==0) {?>
					<a href="#" onclick="return showOptional(this,'#user_tags');" class="optional_field">+ Add Tags</a>
				<?php } ?>

				<h2>Additional Information</h3>
						
				<?php $meta = $user->getMeta();
				if ($meta) { 
					foreach ($meta as $field=>$value) { ?>
						<p class="input">
							<label for="meta_<?php echo $field; ?>"><?php echo $field; ?>:</label>
							<?php if (strlen($value) > 50) { ?>
								<textarea class="text" name="meta_<?php echo $field; ?>"><?php echo htmlspecialchars($value); ?></textarea>
							<?php } else { ?>
								<input class="text" type="text" name="meta_<?php echo $field; ?>" id="meta_<?php echo $field; ?>" value="<?php echo htmlspecialchars($value); ?>"/>
							<?php } ?>
						</p>				
					<?php }
				} ?>

				<div id="new_meta_fields">
				</div>
				<div id="new_meta" style="display:none;">
						<label for="new_meta_name">Name the new field:</label>
						<input id="new_meta_name" type="text" class="meta_lookup text" /><input type="submit" onclick="return addMetaField();" value="Add to Form">
				</div>
				
				<a href="#" onclick="return showOptional(this,'#new_meta');" class="optional_field" id="add_field_link">+ Add Field</a>

			
				<h2>Permissions</h2>
				<p><input type="checkbox" name="adminUser" value="1" <?php if ($user->get('adminUser')) { ?>checked<?php } ?> />&nbsp;&nbsp;Admin User</p>
</div>
</form>

<?php if ($user->saved()) { ?>
<div class="panel panel_with_options">
		<h1><a name="comments"></a>Comments</h1>
			<?php 
				$offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
				$comments = $POD->getComments(array('userId'=>$this->get('id')),'date desc',5.,$offset);
				$comments->output('comment','header','pager',null,'No comments','&id='.$user->id . '#comments');
			?>

</div>

<div class="panel panel_with_options">
		<form method="post" action="<?php $user->POD->podRoot(); ?>/admin/people/" id="edit_password">
			<input type="hidden" name="action" value="password" />
			<h3>Change Password</h3>
			<?php if ($user->get('id')) { ?>
				<input type="hidden" name="id" value="<?php $user->write('id'); ?>" />
			<?php } ?>
			<p class="input"><label for="password">New Password:</label><input type="text" id="password" name="password" class="required text" value="" /></p>
			<p>PeoplePods does not store passwords in the database.  An encrypted hash is stored instead.  The user will not be informed of this password change automatically.</P>
			<p>This tool should only be used when a member has requested their password be reset to a specific value. In most situations, members should be referred to the password reset tool.</p>
			<p><input type="submit" class="button" name="save" value="Change Password" />
		</form>

</div>
<?php } ?>