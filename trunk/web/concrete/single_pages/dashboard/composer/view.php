<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<h1><span><?=t('Composer')?></span></h1>
<div class="ccm-dashboard-inner">
<? if (count($ctArray) > 0) { ?>
<h2><?=t('What type of page would you like to write?')?></h2>
<ul>
<? foreach($ctArray as $ct) { ?>
	<li><a href="<?=$this->url('/dashboard/composer/write', $ct->getCollectionTypeID())?>"><?=$ct->getCollectionTypeName()?></a></li>
<? } ?>
</ul>
<? } else { ?>
	<p><?=t('You have not setup any page types for Composer.')?></p>
<? } ?>

</div>