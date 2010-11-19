<?php  defined('C5_EXECUTE') or die("Access Denied.");  ?>
<div class="ccm-tags-display">
<h4><?php  echo $title ?></h4>
<?php 
$nh = Loader::helper("navigation");
$c = Page::getCurrentPage();
if($ak instanceof CollectionAttributeKey) {
	$av = $c->getAttributeValueObject($ak);
	$selectedOptions = $c->getAttribute($ak->getAttributeKeyHandle());
	$akc = $ak->getController();
	if($selectedOptions instanceof SelectAttributeTypeOptionList && $selectedOptions->count() > 0) {
		?><ul class="ccm-tag-list">
			<?php  foreach($selectedOptions as $opt) {
				$qs = $akc->field('atSelectOptionID') . '[]=' . $opt->getSelectAttributeOptionID();
				?><li><?php  if ($targetCID > 0) {
					$target = Page::getByID($targetCID); ?>
					<a href="<?php echo $nh->getLinkToCollection($target)?>?<?php echo $qs?>">
				<?php  } ?><?php  echo $opt ?><?php  if ($targetCID > 0) { ?></a><?php  } ?></li><?php  
			}?>	
		</ul>
	<?php  } 
	} ?>
</div>