<?php defined('C5_EXECUTE') or die("Access Denied.");  ?>
<div class="ccm-tags-display">
<h4><?php echo $title ?></h4>
<?php
$nh = Loader::helper("navigation");
$c = Page::getCurrentPage();
$av = $c->getAttributeValueObject($ak);
$selectedOptions = $c->getAttribute($ak->getAttributeKeyHandle());
$akc = $ak->getController();
if($selectedOptions instanceof SelectAttributeTypeOptionList && $selectedOptions->count() > 0) {
	?><ul class="ccm-tag-list">
		<?php foreach($selectedOptions as $opt) {
			$qs = $akc->field('atSelectOptionID') . '[]=' . $opt->getSelectAttributeOptionID();
			?><li><? if ($targetCID > 0) {
				$target = Page::getByID($targetCID); ?>
				<a href="<?=$nh->getLinkToCollection($target)?>?<?=$qs?>">
			<? } ?><?php echo $opt ?><? if ($targetCID > 0) { ?></a><? } ?></li><?php 
		}?>	
	</ul>
<?php } ?>
</div>