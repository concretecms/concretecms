<?
defined('C5_EXECUTE') or die("Access Denied.");
if ($action == null) { 
	// we can pass an action from the block, but in most instances we won't, we'll use the default
	$action = $bt->getBlockAddAction($a);
}

$c = $a->getAreaCollectionObject();

 ?>

<a name="_add<?=$bt->getBlockTypeID()?>"></a>

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

</script>
<?
$hih = Loader::helper("concrete/ui/help");
$blockTypes = $hih->getBlockTypes();
$cont = $bt->getController();
	
if (isset($blockTypes[$bt->getBlockTypeHandle()])) {
	$help = $blockTypes[$bt->getBlockTypeHandle()];
} else {
	if ($cont->getBlockTypeHelp()) {
		$help = $cont->getBlockTypeHelp();
	}
}
if (isset($help) && !$bt->supportsInlineAdd()) { ?>
	<div class="dialog-help" id="ccm-menu-help-content"><? 
		if (is_array($help)) { 
			print $help[0] . '<br><br><a href="' . $help[1] . '" class="btn small" target="_blank">' . t('Learn More') . '</a>';
		} else {
			print $help;
		}
	?></div>
<? } ?>

<div <? if (!$bt->supportsInlineAdd()) { ?>class="ccm-ui"<? } ?>>

		function getBlockAddAction(&$a, $alternateHandler = null) {
			// Note: This is fugly, since we're just grabbing query string variables, but oh well. Not _everything_ can be object oriented
			$arHandle = urlencode($a->getAreaHandle());
			$valt = Loader::helper('validation/token');
			
			
			if ($alternateHandler) {
				$str = $alternateHandler . "?cID={$cID}&arHandle={$arHandle}&btID={$btID}&mode=edit" . $step . '&' . $valt->getParameter();
			} else {
				$str = DIR_REL . "/" . DISPATCHER_FILENAME . "?cID={$cID}&arHandle={$arHandle}&btID={$btID}&mode=edit" . $step . '&' . $valt->getParameter();
			}
			return $str;			
		}
		
		

<form method="post" action="<?=$action?>" id="ccm-block-form" enctype="multipart/form-data" class="validate">

<input type="hidden" name="arHandle" value="<?=$a->getAreaHandle()?>">
<input type="hidden" name="btID" value="<?=$bt->getBlockTypeID()?>">
<input type="hidden" name="arHandle" value="<?=$a->getAreaHandle()?>">
<input type="hidden" name="cID" value="<?=$c->getCollectionID()?>">
<?=Loader::helper('validation/token')->output('add_block')?>

<input type="hidden" name="dragAreaBlockID" value="0" />

<? foreach($this->controller->getJavaScriptStrings() as $key => $val) { ?>
	<input type="hidden" name="ccm-string-<?=$key?>" value="<?=h($val)?>" />
<? } ?>

<? if (!$bt->supportsInlineAdd()) { ?>
<div id="ccm-block-fields">
<? } else { ?>
<div>
<? } ?>
