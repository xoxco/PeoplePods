<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/stacks/groups_footer.php
* Header used in core_groups to create the /groups page
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
				No groups found.
			<?php } ?>
		</li>
	<?php } ?>
	<li class="stack_footer pager">
		<?php if ($this->hasPreviousPage()) { echo '<a href="?offset=' . $this->previousPage() . $additional_parameters . '" class="previous">Previous</a>'; } ?>
		<?php if ($this->hasNextPage()) { echo '<a href="?offset=' . $this->nextPage() . $additional_parameters . '" class="next">Next</a>'; }	?>
	</li>
</ul>
</section>