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
		<tr colspan="5">
			<td class="empty_list">
			<?php if ($empty_message) {
				echo $empty_message; 
			} else { ?>
			Nothing to show!
			<?php } ?>
			</td>
		</tr>
	<?php } ?>
	<tr >
		<td colspan="5" class="stack_footer">
		<?php if ($this->hasPreviousPage()) { echo '<a href="?offset=' . $this->previousPage() . $additional_parameters . '" class="stack_previous_link">Previous</a>'; } ?>
		<?php if ($this->hasNextPage()) { echo '<a href="?offset=' . $this->nextPage() . $additional_parameters . '" class="stack_next_link">Next</a>'; }	?>
		</td>
	</tr>
</table>