<form method="post" action="<? $POD->podRoot(); ?>/admin/files/?<? if ($file->get('contentId')) { ?>contentId=<? $file->write('contentId'); } else if ($file->get('groupId')) { ?>groupId=<? $file->write('groupId'); } else { ?>userId=<? $file->write('userId'); }?>" enctype="multipart/form-data" class="valid" id="upload_file">
	<input type="hidden" name="id" value="<? $file->write('id'); ?>">
	<input type="hidden" name="contentId" value="<? $file->write('contentId'); ?>">
	<input type="hidden" name="userId" value="<? $file->write('userId'); ?>">
	<input type="hidden" name="groupId" value="<? $file->write('groupId'); ?>">

		<? if ($file->get('id')) { ?>
			<h3>Upload a new version of this file</h3>
		<? } else { ?>
			<? if ($file->get('contentId')) { ?>
				<h3>Attach a new file to "<? $file->parent()->write('headline'); ?>"</h3>
			<? } else if ($file->get('groupId')) { ?>
				<h3>Attach a new file to "<? $file->group()->write('groupname'); ?>"</h3>
			<? } else { ?>
				<h3>Upload a new file for <? $file->author()->write('nick'); ?></h3>
			<? } ?>
		<? } ?>
		
		<p class="input">
			<label for="name">Name:</label>
			<input name="name" type="text" class="text required" id="name" value="<? echo htmlspecialchars($file->get('file_name')); ?>" /><br />
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
			<textarea name="description" id="description"><? $file->htmlspecialwrite('description'); ?></textarea>			
		</p>
		
		<p class="input">
			<label>&nbsp;</label>
			<input type="submit" value="Save" class="button"/>
		</p>
</form>