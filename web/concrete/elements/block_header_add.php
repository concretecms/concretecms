<?
defined('C5_EXECUTE') or die("Access Denied.");
if ($action == null) { 
	// we can pass an action from the block, but in most instances we won't, we'll use the default
	$action = $bt->getBlockAddAction($a);
	global $c;
} ?>

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

$(function() {
	$('#ccm-block-form').each(function() {
		ccm_setupBlockForm($(this), false, 'add');
	});
});

</script>

<input type="hidden" name="ccm-block-pane-action" value="<?= Loader::helper('security')->sanitizeURL($_SERVER['REQUEST_URI']); ?>" />

<?
$hih = Loader::helper("concrete/interface/help");
$blockTypes = $hih->getBlockTypes();
$cont = $bt->getController();
	
if (isset($blockTypes[$bt->getBlockTypeHandle()])) {
	$help = $blockTypes[$bt->getBlockTypeHandle()];
} else {
	if ($cont->getBlockTypeHelp()) {
		$help = $cont->getBlockTypeHelp();
	}
}
if (isset($help)) { ?>
	<div class="dialog-help" id="ccm-menu-help-content"><? 
		if (is_array($help)) { 
			print $help[0] . '<br><br><a href="' . $help[1] . '" class="btn small" target="_blank">' . t('Learn More') . '</a>';
		} else {
			print $help;
		}
	?></div>
<? } ?>

<? if ($cont->getBlockTypeWrapperClass() != '') { ?>
	<div class="<?=$cont->getBlockTypeWrapperClass();?>">
<? } ?>

<form method="post" action="<?=$action?>" id="ccm-block-form" enctype="multipart/form-data" class="validate form-horizontal">

<input type="hidden" name="ccm-block-form-method" value="REGULAR" />

<? foreach($this->controller->getJavaScriptStrings() as $key => $val) { ?>
	<input type="hidden" name="ccm-string-<?=$key?>" value="<?=$val?>" />
<? } ?>

<div id="ccm-block-fields">