<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<h1><span><?=t('Customize Theme')?></span></h1>
<div class="ccm-dashboard-inner">

<? $h = Loader::helper('concrete/interface'); ?>
<? if (count($styles) > 0) { ?>


<form action="<?=$this->action('save')?>" method="post" id="customize-form">
<?=$form->hidden('previewAction', REL_DIR_FILES_TOOLS_REQUIRED . '/themes/preview_internal?themeID=' . $themeID . '&previewCID=1'); ?>
<?=$form->hidden('saveAction', $this->action('save')); ?>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
	<td valign="top">
<?	
	foreach($styles as $st) { ?>
	
		<div class="ccm-theme-style-attribute">
		<? switch($st->getType()) {
			case PageThemeEditableStyle::TSTYPE_COLOR: ?>
				<?=$st->getName()?>
				<?=$form->hidden('input_color_' . $st->getHandle(), $st->getValue())?>
				<div class="ccm-theme-style-color" id="color_<?=$st->getHandle()?>"><div hex-color="<?=$st->getValue()?>" style="background-color: <?=$st->getValue()?>"></div></div>
			<? 
				break;
		} ?>
		</div>
		
	<? 
	} ?>
	
	<div style="text-align: right">
	<a href="javascript:void(0)" onclick="previewCustomizedTheme()"?><?=t('Preview')?></a>
	</div>
	
	<div style="margin-top: 20px; text-align: center">
	<input type="button" onclick="saveCustomizedTheme()" value="<?=t('Save Theme')?>">
	</div>

	<?=$form->hidden('themeID', $themeID)?>
	<?=$form->hidden('ttask', 'preview_theme_customization')?>
	
	</td>
	<td valign="top" width="100%">
	<div style="padding: 8px; border: 2px solid #eee; margin-left: 10px">
	<iframe name="preview-theme" height="500px" width="100%" src="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/themes/preview_internal?themeID=<?=$themeID?>&previewCID=1" border="0" frameborder="0"></iframe>
	</div>

	
	</td>
	</tr>
	</table>
<? 		
} else {
	print t('This theme contains no styles that can be customized.');
}
?>

</form>
<script type="text/javascript">
previewCustomizedTheme = function() {
	$("#customize-form").attr('target', 'preview-theme');
	$("#customize-form").get(0).action = $('#previewAction').val();
	$("#customize-form").get(0).submit();
}
saveCustomizedTheme = function() {
	$("#customize-form").attr('target', '_self');
	$("#customize-form").get(0).action = $('#saveAction').val();
	$("#customize-form").get(0).submit();
}


$(function() {
	$('div.ccm-theme-style-color').each(function() {
		var thisID = $(this).attr('id');
		var col = $(this).children(0).attr('hex-color');
		$(this).ColorPicker({
			color: col,
			onSubmit: function(hsb, hex, rgb, cal) {
				$('input#input_' + thisID).val('#' + hex);
				$('div#' + thisID + ' div').css('backgroundColor', '#' + hex);
				cal.fadeOut(300);
			}
		});
	});
});
</script>
</div>