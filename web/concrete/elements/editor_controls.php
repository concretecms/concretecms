<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
<div class="ccm-editor-controls-left-cap" <? if (isset($editor_width)) { ?>style="width: <?=$editor_width?>px"<? } ?>>
<div class="ccm-editor-controls-right-cap">
<div class="ccm-editor-controls">
<ul>
<li ccm-file-manager-field="rich-text-editor-image"><a class="ccm-file-manager-launch" onclick="ccm_editorSetupImagePicker(); return false" href="#"><?=t('Add Image')?></a></li>
<li><a class="ccm-file-manager-launch" onclick="ccm_editorSetupFilePicker(); return false;" href="#"><?=t('Add File')?></a></li>
<? // I don't know why I need this ?>
<? /*
<?php if (isset($mode) && $mode == 'full') {?>
<li><a href="#" onclick="setBookMark();ccmEditorSitemapOverlay(); return false"><?=t('Insert Link to Page')?></a></li>
<?php } else {?>
<li><a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/sitemap_overlay.php?sitemap_mode=select_page" onclick="setBookMark();" class="dialog-launch" dialog-modal="false" ><?=t('Insert Link to Page')?></a></li>
<?php } ?>
*/ ?>
<li><a href="#" onclick="ccm_editorSitemapOverlay(); return false"><?=t('Insert Link to Page')?></a></li>
<?php
$path = Page::getByPath('/dashboard/settings');
$cp = new Permissions($path);
if($cp->canViewPage()) { ?>
	<li><a href="<?php echo View::url('/dashboard/system/basics/editor')?>" target="_blank"><?php echo t('Customize Toolbar')?></a></li>
<?php } ?>
</ul>
</div>
</div>
</div>
<div id="rich-text-editor-image-fm-display">
<input type="hidden" name="fType" class="ccm-file-manager-filter" value="<?=FileType::T_IMAGE?>" />
</div>

<div class="ccm-spacer">&nbsp;</div>
<script type="text/javascript">
$(function() {
	ccm_activateFileSelectors();
});
</script>
