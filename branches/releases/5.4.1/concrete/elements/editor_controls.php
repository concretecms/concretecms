<?php  defined('C5_EXECUTE') or die("Access Denied."); ?> 
<div class="ccm-editor-controls">
<div class="ccm-editor-controls-right-cap">
<ul>
<li ccm-file-manager-field="rich-text-editor-image"><a class="ccm-file-manager-launch" onclick="ccm_editorCurrentAuxTool='image'; setBookMark();return false;" href="#"><?php echo t('Add Image')?></a></li>
<li><a class="ccm-file-manager-launch" onclick="ccm_editorCurrentAuxTool='file'; setBookMark();return false;" href="#"><?php echo t('Add File')?></a></li>
<?php  if (isset($mode) && $mode == 'full') {?>
<li><a href="#" onclick="setBookMark();ccmEditorSitemapOverlay();"><?php echo t('Insert Link to Page')?></a></li>
<?php  } else {?>
<li><a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/sitemap_overlay.php?sitemap_mode=select_page" onclick="setBookMark();" class="dialog-launch" dialog-modal="false" ><?php echo t('Insert Link to Page')?></a></li>
<?php  } ?>
<li><a style="float: right" href="<?php echo View::url('/dashboard/settings')?>"><?php echo t('Customize Toolbar')?></a></li>
</ul>
</div>
</div>
<div id="rich-text-editor-image-fm-display">
<input type="hidden" name="fType" class="ccm-file-manager-filter" value="<?php echo FileType::T_IMAGE?>" />
</div>

<div class="ccm-spacer">&nbsp;</div>
<script type="text/javascript">
function ccmEditorSitemapOverlay() {
    $.fn.dialog.open({
        title: 'Choose A Page',
        href: CCM_TOOLS_PATH + '/sitemap_overlay.php?sitemap_mode=select_page&callback=ccm_selectSitemapNode<?php echo $GLOBALS['CCM_EDITOR_SITEMAP_NODE_NUM']?>',
        width: '550',
        modal: false,
        height: '400'
    });
};

ccm_activateFileSelectors();
</script>