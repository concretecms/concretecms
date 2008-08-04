
<? foreach($posts as $item) { ?>
	
	<div class="post">
	<h4><a href="<?php echo $item->get_permalink(); ?>"><?php echo $item->get_title(); ?></a></h4>
	<h5><?php echo $item->get_date('F jS'); ?></h5>
	<?php echo $item->get_description(); ?>
	</div>
<? } ?>

<h2>Search Documentation</h2>
<form method="post" action="http://www.concrete5.org/search/">
<input type="text" name="query" style="width: 130px" />
<input name="search_paths[]" type="hidden" value="/documentation" />
<input type="hidden" name="do" value="search" />
<input type="submit" value="Search" />
</form>
<br/>

<h2>Full Documentation</h2>
<div>Full documentation is available <a href="http://www.concrete5.org/documentation/">at Concrete5.org</a>.</div><br/>
