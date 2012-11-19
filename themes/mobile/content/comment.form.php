	<? if ($this->POD->isAuthenticated()) { ?>
		<div id="comment_form">
			<h3 id="reply">Leave a comment</h3>
			<? $POD->currentUser()->output('avatar'); ?>
			<div class="attributed_content">
				<form method="post" id="add_comment" action="#addComment" data-comments="#comments" data-content="<?= $doc->id; ?>">
					<textarea name="comment" class="expanding" id="comment"></textarea>	
					<a href="" data-role="button" data-ajax="false" class="comment_submit">Submit</a>

				</form>
			</div>
			<div class="clearer"></div>		
		</div>
	<? } else { ?>
		<div id="comment_form">
			<a name="reply"></a>
			<p>
				<a rel="external" href="<? $POD->siteRoot(); ?>/join">Register for an account</a> to leave a comment.
				If you've already got an account, <a href="<? $POD->siteRoot(); ?>/login">login here</a>.
			</p>
		</div>
	<? } ?>