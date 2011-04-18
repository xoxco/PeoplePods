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
<? if ($this->count() == 0) { ?>
		<div class="empty_list">
			<? if ($empty_message) {
				echo $empty_message; 
			} else { ?>
			Nothing to show!
			<? } ?>
		</div>
	<? } ?>
	<div class="stack_footer">
		<? if ($this->hasPreviousPage()) { echo '<a href="?offset=' . $this->previousPage() . $additional_parameters . '" class="stack_previous_link">Previous</a>'; } ?>
		<? if ($this->hasNextPage()) { echo '<a href="?offset=' . $this->nextPage() . $additional_parameters . '" class="stack_next_link">Next</a>'; }	?>
	</div>
</div>