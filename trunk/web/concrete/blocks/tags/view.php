<?php defined('C5_EXECUTE') or die("Access Denied.");  ?>
<div class="ccm-tags-display">
<h4><?php echo $title ?></h4>
<?php
$av = $c->getAttributeValueObject($ak);
$selectedOptions = $c->getAttribute($ak->getAttributeKeyHandle());
if($selectedOptions instanceof SelectAttributeTypeOptionList && $selectedOptions->count() > 0) {
	?><ul>
		<?php foreach($selectedOptions as $opt) {
			?><li><?php echo $opt ?></li><?php 
		}?>	
	</ul>
<?php } ?>
</div>