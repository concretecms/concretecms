<?php defined('C5_EXECUTE') or die("Access Denied.");  ?>
<div class="ccm-tags-display">
<?php if(strlen($title)) {
	?><h4><?php echo $title ?></h4><?php
}

if(is_array($_REQUEST['akID'])) {
	$res = $_REQUEST['akID'][$ak->getAttributeKeyID()]['atSelectOptionID'][0];
	if(is_numeric($res) && $res > 0) {
		$selectedOptionID = $res;
	}
}

if($options instanceof SelectAttributeTypeOptionList && $options->count() > 0) {
	?><ul class="ccm-tag-list">
		<?php foreach($options as $opt) {
			$qs = $akc->field('atSelectOptionID') . '[]=' . $opt->getSelectAttributeOptionID();
			?><li <?php echo ($selectedOptionID == $opt->getSelectAttributeOptionID()?'class="selected"':'')?>><? if ($target instanceof Page) { ?>
				<a href="<?=$navigation->getLinkToCollection($target)?>?<?=$qs?>"><?php echo $opt ?></a><? }  else { echo $opt; }?></li><?php 
		}?>	
	</ul>
<?php } ?>
	<div style="clear: both"></div>
</div>