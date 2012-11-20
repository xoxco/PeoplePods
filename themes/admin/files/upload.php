<form method="post" action="<?php $POD->podRoot(); ?>/admin/files/?<?php if ($file->get('contentId')) { ?>contentId=<?php $file->write('contentId'); } else if ($file->get('groupId')) { ?>groupId=<?php $file->write('groupId'); } else { ?>userId=<?php $file->write('userId'); }?>" enctype="multipart/form-data" class="valid" id="upload_file">
	<input type="hidden" name="id" value="<?php $file->write('id'); ?>">
	<input type="hidden" name="contentId" value="<?php $file->write('contentId'); ?>">
	<input type="hidden" name="userId" value="<?php $file->write('userId'); ?>">
	<input type="hidden" name="groupId" value="<?php $file->write('groupId'); ?>">

		<?php if ($file->get('id')) { ?>
			<h3>Upload a new version of this file</h3>
		<?php } else { ?>
			<?php if ($file->get('contentId')) { ?>
				<h3>Attach a new file to "<?php $file->parent()->write('headline'); ?>"</h3>
			<?php } else if ($file->get('groupId')) { ?>
				<h3>Attach a new file to "<?php $file->group()->write('groupname'); ?>"</h3>
			<?php } else { ?>
				<h3>Upload a new file for <?php $file->author()->write('nick'); ?></h3>
			<?php } ?>
		<?php } ?>
		
		<p class="input">
			<label for="name">Name:</label>
			<input name="name" type="text" class="text required" id="name" value="<?php echo htmlspecialchars($file->get('file_name')); ?>" /><br />
			<span class="field_explain">
				This should be short and descriptive, like 'img' or 'mp3' or 'file1.'
			</span>
		</p>	
			
		<p class="input">
			<label for="file">File:</label>
			<input name="file" type="file" id="file" />
		</p>	
		
		<p class="input">
			<label for="description">Description:</label>
			<textarea name="description" id="description"><?php $file->htmlspecialwrite('description'); ?></textarea>			
		</p>
		
		<p class="input">
			<label>&nbsp;</label>
			<input type="submit" value="Save" class="button"/>
		</p>
</form>