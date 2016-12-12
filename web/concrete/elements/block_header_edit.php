<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<a name="_edit<?=$b->getBlockID()?>"></a>

<? $bt = $b->getBlockTypeObject(); ?>

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
	$('#ccm-block-form').concreteAjaxBlockForm({
		'task': 'edit',
		'bID': <? if (is_object($b->getProxyBlock())) { ?><?=$b->getProxyBlock()->getBlockID()?><? } else { ?><?=$b->getBlockID()?><? } ?>,
		<? if ($bt->supportsInlineEdit()) { ?>
			btSupportsInlineEdit: true,
		<? } else { ?>
			btSupportsInlineEdit: false
		<? } ?>
	});
});
</script>

<?
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

if (isset($message) && is_object($message) && !$bt->supportsInlineEdit()) { ?>
	<div class="dialog-help" id="ccm-menu-help-content"><? print $message->getContent() ?></div>
<? } ?>

<div <? if (!$bt->supportsInlineEdit()) { ?>class="ccm-ui"<? } else { ?>data-container="inline-toolbar"<? } ?>>

<form method="post" id="ccm-block-form" class="validate" action="<?=$dialogController->action('submit')?>" enctype="multipart/form-data">

<? foreach($this->controller->getJavaScriptStrings() as $key => $val) { ?>
	<input type="hidden" name="ccm-string-<?=$key?>" value="<?=h($val)?>" />
<? } ?>

<? if (!$bt->supportsInlineEdit()) { ?>
<div id="ccm-block-fields">
<? } else {
	$css = $b->getCustomStyle();
?>

	<div <? if (is_object($css) && $b->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY) { ?>class="<?=$css->getContainerClass() ?>" <? } ?>>

<? } ?>
