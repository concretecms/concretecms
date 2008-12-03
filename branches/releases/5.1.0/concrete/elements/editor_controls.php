<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<div class="ccm-editor-controls">
<ul>
<li><a class="ccm-launch-al" onclick="ccm_editorCurrentAuxTool='image'; setBookMark();" href="#"><?php echo t('Add Image')?></a></li>
<li><a class="ccm-launch-al" onclick="ccm_editorCurrentAuxTool='file'; setBookMark();" href="#"><?php echo t('Add File')?></a></li>
<li><a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/sitemap_overlay.php?sitemap_mode=select_page" onclick="setBookMark();" class="dialog-launch" dialog-modal="false" ><?php echo t('Insert Link to Page')?></a></li>
</ul>
</div>
<div class="ccm-spacer">&nbsp;</div>