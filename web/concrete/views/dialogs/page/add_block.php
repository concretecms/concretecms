<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<script type="text/javascript">
<? $ci = Loader::helper("concrete/urls"); ?>
<? $url = $ci->getBlockTypeJavaScriptURL($blockType); 
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
</script>

<?

$hih = Core::make("help/block_type");
$message = $hih->getMessage($blockType->getBlockTypeHandle());

if (!$message && $blockTypeController->getBlockTypeHelp()) {
	$message = new \Concrete\Core\Application\Service\UserInterface\Help\Message();
	$message->setIdentifier($blockType->getBlockTypeHandle());
	$message->setMessageContent($blockTypeController->getBlockTypeHelp());
}


if (isset($message) && is_object($message) && !$blockType->supportsInlineAdd()) { ?>
	<div class="dialog-help" id="ccm-menu-help-content"><? print $message->getContent() ?></div>
<? }

if ($blockType->supportsInlineAdd()) {
    $pt = $c->getCollectionThemeObject();
    if (
        $pt->supportsGridFramework()
        && $area->isGridContainerEnabled()
        && !$blockType->ignorePageThemeGridFrameworkContainer()
    ) {

        $gf = $pt->getThemeGridFrameworkObject();
        print $gf->getPageThemeGridFrameworkContainerStartHTML();
        print $gf->getPageThemeGridFrameworkRowStartHTML();
        printf('<div class="%s">', $gf->getPageThemeGridFrameworkColumnClassesForSpan(
                $gf->getPageThemeGridFrameworkNumColumns()
            ));
    }
}
?>

<div <? if (!$blockType->supportsInlineAdd()) { ?>class="ccm-ui"<? } else { ?>data-container="inline-toolbar"<? } ?>>


<form method="post" action="<?=$controller->action('submit')?>" id="ccm-block-form" enctype="multipart/form-data" class="validate">

<input type="hidden" name="btID" value="<?=$blockType->getBlockTypeID()?>">
<input type="hidden" name="arHandle" value="<?=$area->getAreaHandle()?>">
<input type="hidden" name="cID" value="<?=$c->getCollectionID()?>">

<input type="hidden" name="dragAreaBlockID" value="0" />

<? foreach($blockTypeController->getJavaScriptStrings() as $key => $val) { ?>
	<input type="hidden" name="ccm-string-<?=$key?>" value="<?=h($val)?>" />
<? } ?>

<? foreach($area->getAreaCustomTemplates() as $btHandle => $template) {?>
	<input type="hidden" name="arCustomTemplates[<?=$btHandle?>]" value="<?=$template?>" />
<? } ?>

<? if (!$blockType->supportsInlineAdd()) { ?>
<div id="ccm-block-fields">
<? } else { ?>
<div>
<? } ?>

<? $blockView->render('add');?>

</div>

<? if (!$blockType->supportsInlineAdd()) { ?>	

	<div class="ccm-buttons dialog-buttons">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop()" class="btn btn-hover-danger btn-default pull-left"><?=t('Cancel')?></a>
	<a href="javascript:void(0)" onclick="$('#ccm-form-submit-button').get(0).click()" class="pull-right btn btn-primary"><?=t('Add')?></a>
	</div>

<? } ?>

	<!-- we do it this way so we still trip javascript validation. stupid javascript. //-->
	<input type="submit" name="ccm-add-block-submit" value="submit" style="display: none" id="ccm-form-submit-button" />
</form>

</div>

<?
if ($blockType->supportsInlineAdd()) {
    $pt = $c->getCollectionThemeObject();
    if (
        $pt->supportsGridFramework()
        && $area->isGridContainerEnabled()
        && !$blockType->ignorePageThemeGridFrameworkContainer()
    ) {
        $gf = $pt->getThemeGridFrameworkObject();
        print '</div>';
        print $gf->getPageThemeGridFrameworkRowEndHTML();
        print $gf->getPageThemeGridFrameworkContainerEndHTML();
    }
}


