<?
defined('C5_EXECUTE') or die("Access Denied.");

// HELPERS
$ci = Loader::helper('concrete/interface');

?>

		<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Inspect Theme'), false, 'span10 offset1', false);?>
    
    <form method="post" id="ccm-inspect-form" action="<?=$this->url('/dashboard/pages/themes/inspect/', 'activate_files', $ptID)?>">
    
	<div class="ccm-pane-body" style="padding-top:10px;">
    
    	<h3><?=t("Theme name: ").$pageTheme->getThemeName()?></h3>
        
        <div class="row">
        
            <div class="span3">
            	<h5><?=t('Thumbnail')?></h5>
                <div class="well" style="padding:14px;">
                	<div class="ccm-themes-thumbnail" style="padding:4px;background-color:#FFF;border-radius:3px;border:1px solid #DDD;">
                    	<?=$pageTheme->getThemeThumbnail()?>
                    </div>
                </div>
            </div>
        
            <div class="span6">
                <h5><?=t('Files in Theme')?></h5>
                <table border="0" cellspacing="0" cellpadding="0" class="table table-striped table-bordered">            
                    <thead>
                        <tr>
                            <th><?=t('File')?></th>
                            <th><?=t('Type')?></th>
                            <th><?=t('Action to take')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    <?
                    $txt = Loader::helper('text');
                    $pf = 0;
                    
                        if (count($files) == 0) { ?>
                        <tr>
                            <td colspan="3">
                                <?=t('There are no templates in this file.')?>
                            </td>
                        </tr>
                        <? }
                    
                        foreach ($files as $f) { ?>
                        <tr class="inputs-list">
                            <td><?=$f->getFilename()?></td>
                            <td><?
                                switch($f->getType()) {
                                    case PageThemeFile::TFTYPE_VIEW:
                                        print t("Wrapper for static pages.");
                                        break;
                                    case PageThemeFile::TFTYPE_DEFAULT:
                                        print t("Default template.");
                                        break;
                                    case PageThemeFile::TFTYPE_SINGLE_PAGE:
                                        print t("Template for internal concrete5 page.");
                                        break;
                                    case PageThemeFile::TFTYPE_PAGE_TYPE_NEW:
                                        print t("New template.");
                                        break;
                                    case PageThemeFile::TFTYPE_PAGE_TYPE_EXISTING:
                                        print t("Template for existing page type.");
                                        break;
                                }
                                // END Switch
                            ?>
                            </td>
                            <td><?
                                switch($f->getType()) {
                                    case PageThemeFile::TFTYPE_VIEW:
                                        print t('None. This file will automatically be used.');
                                        break;
                                    case PageThemeFile::TFTYPE_DEFAULT:
                                        print t('None. This file will automatically be used for pages without a template.');
                                        break;
                                    case PageThemeFile::TFTYPE_SINGLE_PAGE:
                                        print t('None. This file will automatically be used by the <strong>%s</strong> page.',$txt->unhandle($f->getHandle()) );
                                        break;
                                    case PageThemeFile::TFTYPE_PAGE_TYPE_NEW:
                                        $pf++;
                                        print '<label><input type="checkbox" value="' . $f->getHandle() . '" name="pageTypes[]" checked /> <span>'.t('Create page type.').'</span></label>';
                                        break;
                                    case PageThemeFile::TFTYPE_PAGE_TYPE_EXISTING:
                                        print t('None. This file will automatically be used by the <strong>%s</strong> page type.',$txt->unhandle($f->getHandle()) );
                                        break;
                                }
                                // END Switch
                            ?></td>
                        </tr>
                        
                        <? } // END FOREACH ?>
                    
                    </tbody>
                </table>        
            </div>
        
        </div>
	
	</div>
    
    <div class="ccm-pane-footer">
        <?
        print $ci->button(t('Return to Themes'), $this->url('/dashboard/pages/themes'), 'left');
        if ($pf > 0) { 
            print $ci->submit(t('Ok'), 'ccm-inspect-form', 'right', 'primary'); ?>
        <? }?>
    </div>
    
    </form>
    
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>