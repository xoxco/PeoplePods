	<div id="tools">
		<ul>
			<li id="section_name">People</li><li>
				<a href="<? $POD->podRoot(); ?>/admin/people/" class="button"><img src="<? $POD->podRoot(); ?>/admin/img/user_add.png" border="0" align="absmiddle">&nbsp;New Person</a>
			</li><li>
				<a href="search.php?mode=last" class="button">Recent Visitors</a>
			</li><li>
				<a href="search.php?mode=newest" class="button">Newest Members</a>
			</li><li class="last">
				<form method="get" action="search.php">
					<input name="q" default="Search People" class="repairField" />
				</form>
			</li>
		</ul>
		
	</div>