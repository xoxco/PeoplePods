	<div id="tools">
	
		<ul>
			<li id="section_name">Content</li><li>
				<a href="<? $POD->podRoot(); ?>/admin/content/" class="button"><img src="<? $POD->podRoot(); ?>/admin/img/page_add.png" border="0" align="absmiddle">&nbsp;Add Content</a>
			</li><li>
				<form method="get" action="<? $POD->podRoot(); ?>/admin/content/search.php">
				<input type="text"  default="Search Content"  class="repairField" name="q" id="q" />
				</form>
			</li>		</ul>
	</div>