<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/stacks/uc_pager.php
* Footer template for core_usercontent stack
* which includes next/previous navigation
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/stack-output
/**********************************************/
?>	
	<? if ($this->count() == 0) { ?>
			<li class="empty_list">
				<? if ($empty_message) {
					echo $empty_message; 
				} else { ?>
					No groups found.
				<? } ?>
			</li>
		<? } ?>
		<li class="stack_footer pager">
			<? if ($this->hasPreviousPage()) { echo '<a href="?offset=' . $this->previousPage() . $additional_parameters . '" class="previous">Previous</a>'; } ?>
			<? if ($this->hasNextPage()) { echo '<a href="?offset=' . $this->nextPage() . $additional_parameters . '" class="next">Next</a>'; }	?>
		</li>
	</ul>
	</section>
</div>

<div class="column_4 structure_only">
	
	<? $POD->output('sidebars/group_search'); ?>
	<? $POD->output('sidebars/create_group'); ?>
	<? $POD->output('sidebars/recent_groups'); ?>
	
</div>	