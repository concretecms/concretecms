<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
foreach($posts as $item) { ?>
	
	<div class="post">
	<h4><a href="<?php  echo $item->get_permalink(); ?>"><?php  echo $item->get_title(); ?></a></h4>
	<h5><?php  echo $item->get_date('F jS'); ?></h5>
	<?php  echo $item->get_description(); ?>
	</div>
<?php  } ?>

<h2><?php echo t('Read More')?></h2>

<p><?php echo t('Read more C5 news <a href="%s">at the C5 Developer Center</a>', $feed_read_more)?>.</p>