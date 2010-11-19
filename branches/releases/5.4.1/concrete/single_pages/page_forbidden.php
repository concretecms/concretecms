<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>

<h1 class="error"><?php echo t('Page Forbidden')?></h1>

<?php echo t('You are not authorized to access this page.')?>
<br/>
<br/>

<?php  $a = new Area("Main"); $a->display($c); ?>

<a href="<?php echo DIR_REL?>/"><?php echo t('Back to Home')?></a>.