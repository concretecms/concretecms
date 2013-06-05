<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<h1 class="error"><?=t('Page Not Found')?></h1>

<?=t('No page could be found at this address.')?>

<? if (is_object($c)) { ?>
	<br/><br/>
	<? $a = new Area("Main"); $a->display($c); ?>
<? } ?>

<br/><br/>

<a href="<?=DIR_REL?>/"><?=t('Back to Home')?></a>.