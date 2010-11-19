<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>

<h1 class="error"><?php echo t('Page Not Found')?></h1>

<?php echo t('No page could be found at this address.')?>

<?php  if (is_object($c)) { ?>
	<br/><br/>
	<?php  $a = new Area("Main"); $a->display($c); ?>
<?php  } ?>

<br/><br/>

<a href="<?php echo DIR_REL?>/"><?php echo t('Back to Home')?></a>.