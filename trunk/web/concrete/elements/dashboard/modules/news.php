
<? foreach($posts as $item) { ?>
	
	<div class="post">
	<h4><a href="<?php echo $item->get_permalink(); ?>"><?php echo $item->get_title(); ?></a></h4>
	<h5><?php echo $item->get_date('F jS'); ?></h5>
	<?php echo $item->get_description(); ?>
	</div>
<? } ?>