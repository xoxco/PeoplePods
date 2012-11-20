<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/stacks/pager.php
* Footer template which includes next/previous navigation
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/stack-output
/**********************************************/
?>	
<?php if ($this->count() == 0) { ?>
		<li class="empty_list">
			<?php if ($empty_message) {
				echo $empty_message; 
			} else { ?>
			Nothing to show!
			<?php } ?>
		</li>
	<?php } ?>
	<li class="stack_footer pager">
		<?php if ($this->hasPreviousPage()) { echo '<a href="?offset=' . $this->previousPage() . $additional_parameters . '" class="previous" data-role="button" data-icon="arrow-l" rel="external">Previous</a>'; } ?>
		<?php if ($this->hasNextPage()) { echo '<a href="?offset=' . $this->nextPage() . $additional_parameters . '" data-role="button" data-icon="arrow-r" class="next" rel="external">Next</a>'; }	?>
	</li>
</ul>
</section>