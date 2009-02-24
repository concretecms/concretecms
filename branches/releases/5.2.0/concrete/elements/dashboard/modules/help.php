<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
foreach($posts as $item) { ?>
	
	<div class="post">
	<h4><a href="<?php  echo $item->get_permalink(); ?>"><?php  echo $item->get_title(); ?></a></h4>
	<h5><?php  echo $item->get_date('F jS'); ?></h5>
	<?php  echo $item->get_description(); ?>
	</div>
<?php  } ?>

<h2><?php echo t('Search Documentation')?></h2>
<form method="post" action="http://www.concrete5.org/search/">
<input type="text" name="query" style="width: 130px" />
<input name="search_paths[]" type="hidden" value="/help" />
<input type="hidden" name="do" value="search" />
<input type="submit" value="<?php echo t('Search')?>" />
</form>
<br/>

<h2><?php echo t('Full Documentation')?></h2>
<div><?php echo t('Full documentation is available <a href="%s">at Concrete5.org</a>', 'http://www.concrete5.org/docs/')?>.</div><br/>
