<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<ul class="ccm-inline-toolbar ccm-ui">
	<li class="ccm-inline-toolbar-icon-cell"><a href="#"><i class="icon-filter"></i></a></li>
	<li class="ccm-inline-toolbar-icon-cell"><a href="#"><i class="icon-resize-full"></i></a></li>
	<li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-cancel"><button type="button"><?=t("Cancel")?></button></li>
	<li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-save"><button type="button"><?=t("Save")?></button></li>
</ul>

<?
Loader::element('aggregator/display', array(
	'aggregator' => $aggregator,
	'list' => $itemList,
	'showTileControls' => true
));
