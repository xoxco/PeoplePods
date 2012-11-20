<h1><?php echo $doc->headline; ?></h1>

<p>This is a sample plugin that does nothing but print a sample message!</p>

<?php $doc->sampleContentMethod(); ?>

<p>Sample Settings, set via the <a href="<?php $POD->podRoot(); ?>/admin/options/podsettings.php?pod=sample_pod">command center</a>:</p>

<p>Setting 1: <?php echo $POD->libOptions('sampleSetting1'); ?></p>
<p>Setting 2: <?php echo $POD->libOptions('sampleSetting2'); ?></p>