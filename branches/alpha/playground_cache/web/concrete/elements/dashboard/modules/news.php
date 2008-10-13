<?
defined('C5_EXECUTE') or die(_("Access Denied."));
foreach($posts as $item) { ?>
	
	<div class="post">
	<h4><a href="<?php echo $item->get_permalink(); ?>"><?php echo $item->get_title(); ?></a></h4>
	<h5><?php echo $item->get_date('F jS'); ?></h5>
	<?php echo $item->get_description(); ?>
	</div>
<? } ?>

<h2>Read More</h2>

<p>Read more C5 news <a href="<?=$feed_read_more?>">at the official C5 developer blog</a>.</p>