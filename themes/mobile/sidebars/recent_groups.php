<? 
	$recent = $POD->getGroups(array('type'=>'public'),'g.date DESC',5,0);
?>
<div class="sidebar padded" id="recent_groups_sidebar">
	<h3>Newish Groups:</h3>
	<? $recent->output('recent_list',null,null); ?>
</div>