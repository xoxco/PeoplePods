<h1><?= $doc->headline; ?></h1>

<p>This is a sample plugin that does nothing but print a sample message!</p>

<? $doc->sampleContentMethod(); ?>

<p>Sample Settings, set via the <a href="<? $POD->podRoot(); ?>/admin/options/podsettings.php?pod=sample_pod">command center</a>:</p>

<p>Setting 1: <?= $POD->libOptions('sampleSetting1'); ?></p>
<p>Setting 2: <?= $POD->libOptions('sampleSetting2'); ?></p>