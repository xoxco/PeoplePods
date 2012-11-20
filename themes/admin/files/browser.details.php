<?

// original name
// pp name
// thumbnails
// original size
// resized sizes

// custom size
// keep aspect ratio
	
	$sizes['original'] = $file->imageSize();
	$sizes['resized'] = $file->imageSize('resized');
	$sizes['thumbnail'] = $file->imageSize('thumbnail');

?>

<p class="crumbs"><a href="#" onclick="return fileBrowserList();">Back to file list</a></p>

<form>
<img src="<?php echo $file->src('200'); ?>" />

<fieldset>
<p><strong>Original name:</strong> <?php echo $file->original_name; ?></p>

<p><strong>Choose a file size:</strong></p>
<p><input type="radio" onclick="generateImageMarkup();" id="fileBrowser_original" name="fileBrowser_src" value="<?php echo $file->original_file; ?>" xwidth="<?php echo $sizes['original'][0]; ?>" xheight="<?php echo $sizes['original'][1]; ?>" checked> <strong>Original (<?php echo $sizes['original'][0]; ?> x <?php echo $sizes['original'][1];  ?>)</strong>
<p><input type="radio" onclick="generateImageMarkup();" name="fileBrowser_src"  value="<?php echo $file->resized; ?>"  xwidth="<?php echo $sizes['resized'][0]; ?>" xheight="<?php echo $sizes['resized'][1]; ?>" > <strong>Resized (<?php echo $sizes['resized'][0]; ?> x <?php echo $sizes['resized'][1];  ?>)</strong>
<p><input type="radio" onclick="generateImageMarkup();" name="fileBrowser_src" value="<?php echo $file->thumbnail; ?>"  xwidth="<?php echo $sizes['thumbnail'][0]; ?>" xheight="<?php echo $sizes['thumbnail'][1]; ?>" > <strong>Thumbnail (<?php echo $sizes['thumbnail'][0]; ?> x <?php echo $sizes['thumbnail'][1];  ?>)</strong>
<p><input type="radio" onclick="generateImageMarkup();" id="fileBrowser_custom" name="fileBrowser_src" value="custom"> Custom: <input name="fileBrowser_width" onchange="fileBrowserCustomSize('width');" id="fileBrowser_width" value="<?php echo $sizes['original'][0]; ?>" size="4" />x<input name="fileBrowser_height" onchange="fileBrowserCustomSize('height');" id="fileBrowser_height" value="<?php echo $sizes['original'][1]; ?>" size="4" /> <input type="checkbox" value="1" id="fileBrowser_maintainAspectRatio" onclick="fileBrowserCustomSize('width');" checked /> Maintain Ratio

<p><strong>Choose an alignment:</strong></p>
<p><input type="radio" onclick="generateImageMarkup();" name="fileBrowser_align" value="none" checked /> No float</p>
<p><input type="radio" onclick="generateImageMarkup();" name="fileBrowser_align" value="left" /> Float Left</p>
<p><input type="radio" onclick="generateImageMarkup();" name="fileBrowser_align" value="right" /> Float Right</p>

<p><strong>Caption:</strong></p>
<textarea id="fileBrowser_caption" onchange="generateImageMarkup();">
<?php echo $file->description; ?>
</textarea>

<p><strong>Customize markup:</strong></p>
<textarea id="fileBrowser_markup">
</textarea>

<a href="#" onclick="return insertMarkup($('#fileBrowser_markup').val());" class="submit">Insert</a>
<div class="clearer"></div>
</fieldset>

</form>
<script type="text/javascript">
	generateImageMarkup();
</script>