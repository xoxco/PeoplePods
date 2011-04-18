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
<img src="<?= $file->src('200'); ?>" />

<fieldset>
<p><strong>Original name:</strong> <?= $file->original_name; ?></p>

<p><strong>Choose a file size:</strong></p>
<p><input type="radio" onclick="generateImageMarkup();" id="fileBrowser_original" name="fileBrowser_src" value="<?= $file->original_file; ?>" xwidth="<?= $sizes['original'][0]; ?>" xheight="<?= $sizes['original'][1]; ?>" checked> <strong>Original (<?= $sizes['original'][0]; ?> x <?= $sizes['original'][1];  ?>)</strong>
<p><input type="radio" onclick="generateImageMarkup();" name="fileBrowser_src"  value="<?= $file->resized; ?>"  xwidth="<?= $sizes['resized'][0]; ?>" xheight="<?= $sizes['resized'][1]; ?>" > <strong>Resized (<?= $sizes['resized'][0]; ?> x <?= $sizes['resized'][1];  ?>)</strong>
<p><input type="radio" onclick="generateImageMarkup();" name="fileBrowser_src" value="<?= $file->thumbnail; ?>"  xwidth="<?= $sizes['thumbnail'][0]; ?>" xheight="<?= $sizes['thumbnail'][1]; ?>" > <strong>Thumbnail (<?= $sizes['thumbnail'][0]; ?> x <?= $sizes['thumbnail'][1];  ?>)</strong>
<p><input type="radio" onclick="generateImageMarkup();" id="fileBrowser_custom" name="fileBrowser_src" value="custom"> Custom: <input name="fileBrowser_width" onchange="fileBrowserCustomSize('width');" id="fileBrowser_width" value="<?= $sizes['original'][0]; ?>" size="4" />x<input name="fileBrowser_height" onchange="fileBrowserCustomSize('height');" id="fileBrowser_height" value="<?= $sizes['original'][1]; ?>" size="4" /> <input type="checkbox" value="1" id="fileBrowser_maintainAspectRatio" onclick="fileBrowserCustomSize('width');" checked /> Maintain Ratio

<p><strong>Choose an alignment:</strong></p>
<p><input type="radio" onclick="generateImageMarkup();" name="fileBrowser_align" value="none" checked /> No float</p>
<p><input type="radio" onclick="generateImageMarkup();" name="fileBrowser_align" value="left" /> Float Left</p>
<p><input type="radio" onclick="generateImageMarkup();" name="fileBrowser_align" value="right" /> Float Right</p>

<p><strong>Caption:</strong></p>
<textarea id="fileBrowser_caption" onchange="generateImageMarkup();">
<?= $file->description; ?>
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