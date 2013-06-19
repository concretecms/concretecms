<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? $bt = BlockType::getByID($b->getBlockTypeID());
$ci = Loader::helper("concrete/urls");
$btIcon = $ci->getBlockTypeIconURL($bt); 			 
$cont = $bt->getController();

?>

<script type="text/javascript">

<? $ci = Loader::helper("concrete/urls"); ?>
<? $url = $ci->getBlockTypeJavaScriptURL($bt); 
if ($url != '') { ?>
	ccm_addHeaderItem("<?=$url?>", 'JAVASCRIPT');
<? } 

$identifier = strtoupper('BLOCK_CONTROLLER_' . $btHandle);
if (is_array($headerItems[$identifier])) {
	foreach($headerItems[$identifier] as $item) { 
		if ($item instanceof CSSOutputObject) {
			$type = 'CSS';
		} else {
			$type = 'JAVASCRIPT';
		}
		?>
		ccm_addHeaderItem("<?=$item->file?>", '<?=$type?>');
	<?
	}
}
?>

$(function() {
	$('#ccm-block-form').each(function() {
		ccm_setupBlockForm($(this), false, 'add');
	});
});

</script>

<?
if ($b->getBlockName() != '') { 
	$btName = $b->getBlockName();
} else {
	$btName = t($bt->getBlockTypeName());
}
?>

<? if ($displayEditLink) { ?>
	<label class="control-label"><a href="javascript:void(0)" onclick="ccm_composerEditBlock(<?=$b->getBlockCollectionID()?>, <?=$b->getBlockID()?>, '<?=$b->getAreaHandle()?>', <?=$bt->getBlockTypeInterfaceWidth()?> , <?=$bt->getBlockTypeInterfaceHeight()?> )" ><?=$btName?></a></label>
<? } else { ?>
	<label class="control-label"><?=$btName?></label>
<? } ?>

<div class="controls">
<? Loader::element('block_header', array('b' => $b))?>
