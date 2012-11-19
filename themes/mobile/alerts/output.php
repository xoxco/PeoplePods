<div class="alert" id="alert<?= $this->id; ?>">
	<?= $this->formatMessage(); ?>
	<a href="#markAsRead" data-alert="<?= $this->id; ?>" class="markAsRead">x</a>
</div>