<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/content/editform.php
* Default content add/edit form used by the core_usercontent module
* Customizing the fields in this form will alter the information stored!
* Use this file as the basis for new content type forms
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/new-content-type
/**********************************************/
?>
<section id="editform">
    <div data-role="collapsible" data-collapsed="true">
        <h3>Create Something</h3>
	<form class="valid" action="<?php $doc->write('editpath'); ?>" method="post" id="post_something"  enctype="multipart/form-data">
		<?php if ($doc->get('id')) { ?>
			<input type="hidden" name="id" value="<?php $doc->write('id'); ?>" />
			<input type="hidden" name="redirect" value="<?php $doc->write('permalink'); ?>" />
		<?php } else if ($doc->get('groupId')) { ?>
			<input type="hidden" name="redirect" value="<?php $this->group()->write('permalink'); ?>" />
		<?php } ?>
		<?php if ($doc->get('groupId')) { ?>
			<input type="hidden" name="groupId" value="<?php $doc->write('groupId'); ?>" />		
		<?php } ?>
		<?php if ($doc->get('type')) { ?>
			<input type="hidden" name="type" value="<?php $doc->write('type'); ?>" />		
		<?php } ?>
			<ul id="post_options">
				<li class="post_option" >
					<a href="#" id="add_body"onclick="return togglePostOption('body');">+ More</a>
				</li>
                                <!--
				<li class="post_option" >
					<a href="#" id="add_photo" onclick="return togglePostOption('photo');">+ Image</a>
				</li>-->
				<li class="post_option" >
					<a href="#" id="add_video" onclick="return togglePostOption('video');">+ Video</a>
				</li>
				<li class="post_option" >
					<a href="#" id="add_link" onclick="return togglePostOption('link');">+ Link</a>
				</li>
				<li class="post_option">
					<a href="#"  id="add_tags" onclick="return togglePostOption('tags');">+ Tags</a>
				</li>			
			</ul>
			<textarea name="headline" id="headline" class="text expanding required" required><?php $doc->htmlspecialwrite('headline'); ?></textarea>

		<div class="clearer"></div>
		<p class="post_extra" id="post_body">
			<label for="body">Write More:</label>
			<textarea name="body" id="body" class="text"><?php $doc->htmlspecialwrite('body'); ?></textarea>
		</p>
		<p class="post_extra" id="post_photo">
			<label for="photo">Image:</label>
			<input type="file" name="img" id="img" />
			<?php if ($img = $doc->files()->contains('file_name','img')) { ?>
			<div id="file<?php echo $img->id; ?>" class="file">
				<a href="<?php echo $img->original_file; ?>"><img src="<?php $img->write('thumbnail'); ?>" border="0" /></a>
				<a href="#deleteFile" data-file="<?php echo $img->id;?>">Delete</a>
			</div>
			<?php } ?>
		</p>				
		<p class="post_extra" id="post_video">
			<label for="link">Video:</label>
			<input name="meta_video" id="video" value="<?php $doc->htmlspecialwrite('video'); ?>" class="text" />
			(Paste a YouTube, Google Video, Veoh or Vimeo link.)
		</p>
		<p class="post_extra" id="post_link"><label for="link">Link:</label>
		<input name="link" id="link" value="<?php echo $doc->htmlspecialwrite('link'); ?>" class="text" />
		</p>
		<p class="post_extra" id="post_tags"><label for="tags">Tags:</label>
		<input name="tags" id="tags" value="<?php echo $doc->tagsAsString(); ?>" class="text" />
		(Separate tags with a space: monkeys robots ninjas)
		</p>

		<p>
			<input type="submit" id="editform_save" value="Save" />

			<?php
				// if this is a new post, we need to give the option to set it friend only or group only
				if (!$doc->get('id')) { 
					if ($doc->group()) {
						if ($doc->group()->get('type')=="private") { ?>
							<input type="hidden" name="group_only" value="group_only" />
							Posts in this group will only be available to other members.
						<?php } else { ?>
							<input type="checkbox" name="group_only" value="group_only" />
							<label for="group_only">Group Only</label>&nbsp;&nbsp;&nbsp;
						<?php } 
					} else { ?>
						
                                                <div data-role="fieldcontain">
                                                    <fieldset data-role="controlgroup">
                                                        <input type="checkbox" name="friends_only" value="friends_only" id="friends_only" class="custom" />
                                                        <label for="friends_only">Friends Only</label>
                                                     </fieldset>
                                                </div>
					<?php } 
				} else { 
					if ($doc->get('privacy')=="friends_only") { ?>
						This post is visible to friends only.
					<?php } else if ($doc->get('privacy')=="group_only") { ?>
						This post is only visible to other members of this group.
					<?php } 
				} ?>
		</p>
		<div class="clearer"></div>
	</form>
    </div>
	<div class="clearer"></div>
</section> <!-- end editform -->

<script type="text/javascript">
// display the appropriate fields in the edit form.
	<?php if ($doc->get('video')) { ?>
		togglePostOption('video');
	<?php } ?>
	<?php if ($doc->get('body')) { ?>
		togglePostOption('body');
	<?php } ?>
	<?php if ($doc->get('link')) { ?>
		togglePostOption('link');
	<?php } ?>
	<?php if ($doc->get('id') && $doc->files()->contains('file_name','img')) { ?>
		togglePostOption('photo');
	<?php } ?>
	<?php if ($doc->get('id') && $doc->tags()->count() > 0) { ?>
		togglePostOption('tags');
	<?php } ?>
	<?php if ($doc->get('option1')) { ?>
		togglePostOption('poll');
	<?php } ?>

</script>