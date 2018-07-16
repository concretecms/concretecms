<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Block\Block $b */
/* @var Concrete\Core\Application\Service\Urls $ci */
/* @var Concrete\Core\Application\Service\UserInterface\Help\BlockTypeManager $bth */

$app = Core::getFacadeApplication();

$ci = $app->make('helper/concrete/urls');
$bth = $app->make('help/block_type');

if (!isset($btHandle)) {
    $btHandle = $b->getBlockTypeHandle();
}

$bt = $b->getBlockTypeObject();

if ($b->getBlockTypeHandle() === BLOCK_HANDLE_SCRAPBOOK_PROXY) {
    $bx = Block::getByID($b->getController()->getOriginalBlockID());
    $cont = $bx->getController();
    $supportsInlineEdit = $bx->getBlockTypeObject()->supportsInlineEdit();
} else {
    $cont = $bt->getController();
    $supportsInlineEdit = $bt->supportsInlineEdit();
}

?>
<a name="_edit<?= $b->getBlockID() ?>"></a>

<script type="text/javascript">
<?php

$url = (string) $ci->getBlockTypeJavaScriptURL($bt);
if ($url !== '') {
    ?>ConcreteAssetLoader.loadJavaScript(<?= json_encode($url) ?>);<?php
}
if (isset($headerItems) && is_array($headerItems)) {
    $identifier = 'BLOCK_CONTROLLER_' . strtoupper($btHandle);
    if (isset($headerItems[$identifier]) && is_array($headerItems[$identifier])) {
        foreach ($headerItems[$identifier] as $item) {
            if ($item instanceof CSSOutputObject) { ?>
                ConcreteAssetLoader.loadCSS(<?= json_encode((string) $item->file) ?>);
            <?php
            } else { ?>
                ConcreteAssetLoader.loadJavaScript(<?= json_encode((string) $item->file) ?>);
            <?php }
        }
    }
}

?>
$(function() {
	$('#ccm-block-form').concreteAjaxBlockForm({
		task: 'edit',
		bID: <?= is_object($b->getProxyBlock()) ? $b->getProxyBlock()->getBlockID() : $b->getBlockID() ?>,
        btSupportsInlineEdit: <?= $supportsInlineEdit ? 'true' : 'false' ?>
	});
});
</script>
<?php

$message = $bth->getMessage($bt->getBlockTypeHandle());

if (!$message && $cont->getBlockTypeHelp()) {
    $message = new Concrete\Core\Application\Service\UserInterface\Help\Message();
    $message->setIdentifier($bt->getBlockTypeHandle());
    $message->setMessageContent($cont->getBlockTypeHelp());
}

if (is_object($message) && !$bt->supportsInlineEdit()) { ?>
	<div class="dialog-help" id="ccm-menu-help-content"><?php echo $message->getContent() ?></div>
<?php
} ?>

<div <?php if (!$bt->supportsInlineEdit()) {
    ?>class="ccm-ui"<?php
} else {
    ?>data-container="inline-toolbar"<?php
}
?>>
    <?php
    $method = 'submit';
    ?>
    <form method="post" id="ccm-block-form" class="validate" action="<?= $dialogController->action($method) ?>" enctype="multipart/form-data">
        <?php
        foreach ($this->controller->getJavaScriptStrings() as $key => $val) {
            ?><input type="hidden" name="ccm-string-<?= $key ?>" value="<?= h($val) ?>" /><?php
        }
        if ($bt->supportsInlineEdit()) {
            $css = $b->getCustomStyle();
            ?>
            <div<?php if (is_object($css) && $b->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY) {
                ?> class="<?= $css->getContainerClass() ?>"<?php
            }
            ?>><?php
        } else {
            ?><div id="ccm-block-fields"><?php
        }
        
