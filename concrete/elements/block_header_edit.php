<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<a name="_edit<?=$b->getBlockID()?>"></a>

<?php $bt = $b->getBlockTypeObject(); ?>

<script type="text/javascript">

<?php $ci = Loader::helper("concrete/urls"); ?>
<?php $url = $ci->getBlockTypeJavaScriptURL($bt);
if ($url != '') {
    ?>
	ccm_addHeaderItem("<?=$url?>", 'JAVASCRIPT');
<?php 
}

$identifier = strtoupper('BLOCK_CONTROLLER_' . $btHandle);
if (is_array($headerItems[$identifier])) {
    foreach ($headerItems[$identifier] as $item) {
        if ($item instanceof CSSOutputObject) {
            $type = 'CSS';
        } else {
            $type = 'JAVASCRIPT';
        }
        ?>
		ccm_addHeaderItem("<?=$item->file?>", '<?=$type?>');
	<?php

    }
}
?>
$(function() {
	$('#ccm-block-form').concreteAjaxBlockForm({
		'task': 'edit',
		'bID': <?php if (is_object($b->getProxyBlock())) {
    ?><?=$b->getProxyBlock()->getBlockID()?><?php 
} else {
    ?><?=$b->getBlockID()?><?php 
} ?>,
		<?php if ($bt->supportsInlineEdit()) {
    ?>
			btSupportsInlineEdit: true,
		<?php 
} else {
    ?>
			btSupportsInlineEdit: false
		<?php 
} ?>
	});
});
</script>

<?php
$cont = $bt->getController();
if ($b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
    $bx = Block::getByID($b->getController()->getOriginalBlockID());
    $cont = $bx->getController();
}

$hih = Core::make("help/block_type");
$message = $hih->getMessage($bt->getBlockTypeHandle());

if (!$message && $cont->getBlockTypeHelp()) {
    $message = new \Concrete\Core\Application\Service\UserInterface\Help\Message();
    $message->setIdentifier($bt->getBlockTypeHandle());
    $message->setMessageContent($cont->getBlockTypeHelp());
}

if (isset($message) && is_object($message) && !$bt->supportsInlineEdit()) {
    ?>
	<div class="dialog-help" id="ccm-menu-help-content"><?php echo $message->getContent() ?></div>
<?php 
} ?>

<div <?php if (!$bt->supportsInlineEdit()) {
    ?>class="ccm-ui"<?php 
} else {
    ?>data-container="inline-toolbar"<?php 
}

$method = 'submit';
?>>

<form method="post" id="ccm-block-form" class="validate" action="<?=$dialogController->action($method)?>" enctype="multipart/form-data">

<?php foreach ($this->controller->getJavaScriptStrings() as $key => $val) {
    ?>
	<input type="hidden" name="ccm-string-<?=$key?>" value="<?=h($val)?>" />
<?php 
} ?>

<?php if (!$bt->supportsInlineEdit()) {
    ?>
<div id="ccm-block-fields">
<?php 
} else {
    $css = $b->getCustomStyle();
    ?>

	<div <?php if (is_object($css) && $b->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY) {
    ?>class="<?=$css->getContainerClass() ?>" <?php 
}
    ?>>

<?php 
} ?>
