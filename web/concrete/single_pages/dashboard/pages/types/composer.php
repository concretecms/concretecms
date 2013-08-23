<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<? 
$form = Loader::helper('form');
$html = Loader::helper('html');
$ih = Loader::helper('concrete/interface');
$cap = Loader::helper('concrete/dashboard');
$ctArray = CollectionType::getList();
?>

<!-- START Composer Settings pane -->

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Composer Settings'), false, false, false);?>

<? 
if ($cap->canAccessComposer()) { ?>

	<form class="form-vertical" method="post" action="<?=$this->action('save')?>">

	<div class="ccm-pane-body">
	<?=$form->hidden('ctID', $ct->getCollectionTypeID()); ?>
    
        <h3><?=t("Page type").': '.$ct->getCollectionTypeName()?></h3>
        <table class="table" cellspacing="0" cellpadding="0" border="0">
            <thead>
                <tr>
                    <th class="header"><?=t('Included in Composer?')?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>                    
                    	<label class="checkbox inline">
                        	<?=$form->checkbox('ctIncludeInComposer', 1, $ct->isCollectionTypeIncludedInComposer() == 1)?>
                            <span><?=t('Yes, include this page type in Composer.')?></span>
                        </label>                        
                    </td>
                </tr>
			</tbody>
		</table>
        
        <table cellspacing="0" cellpadding="0" border="0" class="table">
            <thead>
                <tr>
                    <th class="header"><?=t('Composer Publishing Settings')?></th>
                </tr>
			</thead>
			<tbody>
                <tr class="row-composer inputs-list">
                    <td>
                    
                        <label>
                        	<?=$form->radio('ctComposerPublishPageMethod', 'CHOOSE', $ct->getCollectionTypeComposerPublishMethod() == 'CHOOSE' || $ct->getCollectionTypeComposerPublishMethod == null)?>
                            <span><?=t('Choose from all pages when publishing.')?></span>
                        </label>
                        
                        <label>
                        	<?=$form->radio('ctComposerPublishPageMethod', 'PAGE_TYPE', $ct->getCollectionTypeComposerPublishMethod() == 'PAGE_TYPE')?>
                            <span><?=t('Choose from pages of a certain type when publishing.')?></span>
                        </label>
            
                        <div style="display: none; padding: 10px" id="ccm-composer-choose-parent-page-type">
                            <?
                            $types = array();
                            foreach($ctArray as $cta) {
                                $types[$cta->getCollectionTypeID()] = $cta->getCollectionTypeName();
                            }
                            ?>
                            <?=$form->select('ctComposerPublishPageTypeID', $types, $ct->getCollectionTypeComposerPublishPageTypeID())?>
                        </div>
                        
                        <label>
                        	<?=$form->radio('ctComposerPublishPageMethod', 'PARENT', $ct->getCollectionTypeComposerPublishMethod() == 'PARENT')?>
                            <span><?=t('Always publish below a certain page.')?></span>
                        </label>
                        
                        <div style="display: none; padding: 10px" id="ccm-composer-choose-parent">
							<? 
                            $pf = Loader::helper('form/page_selector');
                            print $pf->selectPage('ctComposerPublishPageParentID', $ct->getCollectionTypeComposerPublishPageParentID());
                            ?>
                        </div>
                        
                    </td>
                </tr>
			</tbody>
		</table>
                
		<table class="table" cellspacing="0" cellpadding="0" border="0">
            <thead>
                <tr class="row-composer">
                    <th colspan="3" class="subheader"><?=t('Attributes to Display in Composer')?></th>
                </tr>
			</thead>
			<tbody>
                <?
                    $selectedAttributes = array();
                    $cpattribs = $ct->getComposerAttributeKeys();
                    foreach($cpattribs as $cpa) {
                        $selectedAttributes[] = $cpa->getAttributeKeyID();
                    }
                    
                    $attribs = CollectionAttributeKey::getList();
                    $i = 0;
                    foreach($attribs as $ak) { 
                    if ($i == 0) { ?>
                        <tr class="row-composer inputs-list">
                    <? } ?>
                    
                    	<td width="33%">
                            <label>
                                <?=$form->checkbox('composerAKID[]', $ak->getAttributeKeyID(), in_array($ak->getAttributeKeyID(), $selectedAttributes))?>
                                <span><?=tc('AttributeKeyName', $ak->getAttributeKeyName())?></span>
                            </label>
                        </td>
                    
                    <? $i++;
                    
                    if ($i == 3) { ?>
                    </tr>
                    <? 
                    $i = 0;
                    }
                    
                }
                
                if ($i < 3 && $i > 0) {
                    for ($j = $i; $j < 3; $j++) { ?>
                        <td>&nbsp;</td>
                    <? }
                ?></tr>
                <? } ?>
			</tbody>
		</table>
                
		<table class="table" cellspacing="0" cellpadding="0" border="0">
            <thead>
				<tr>
                    <th class="header"><?=t('Composer Content Order')?></th>
                </tr>
			</thead>
            <tbody>
                <tr>
                    <td>
                        <div class="ccm-composer-content-item-list">
                        
                        <?
                        $cur = Loader::helper('concrete/urls');
                                    
                        foreach($contentitems as $ci) { 
                            if ($ci instanceof AttributeKey) {
                                $ak = $ci;
                            ?>
                        
                        <div class="ccm-composer-content-item" id="item_akID<?=$ak->getAttributeKeyID()?>">
                            <img class="ccm-composer-content-item-icon" src="<?=$ak->getAttributeKeyIconSRC()?>" width="16" height="16" /><?=tc('AttributeKeyName', $ak->getAttributeKeyName())?>
                        </div>
                
                            <? } else if ($ci instanceof Block) { 
                                $b = $ci; ?>
            
                            
                        <div class="ccm-composer-content-item" id="item_bID<?=$b->getBlockID()?>">
                            <img class="ccm-composer-content-item-icon" src="<?=$cur->getBlockTypeIconURL($b)?>" width="16" height="16" /><?
                                if ($b->getBlockName()) {
                                    print $b->getBlockName();
                                } else {
                                    print t($b->getBlockTypeName());
                                }
                            ?>
                        </div>
                            <? } ?>
            
                        <? } ?>
                        
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        
	</div>
    
    <div class="ccm-pane-footer">
        <? print $ih->submit(t('Save'), 'update', 'right', 'primary'); ?>
        <? print $ih->button(t('Back to Page Types'), $this->url('/dashboard/pages/types'), 'left'); ?>
    </div>
    
	</form>
    
    <script type="text/javascript">
	ccm_setupComposerFields = function() {
		
		if ($("input[name=ctIncludeInComposer]").prop('checked')) {
			$(".row-composer input, .row-composer select").attr('disabled', false);
		} else {
			$(".row-composer input, .row-composer select").attr('disabled', 'true');
		}
		var val = $('input[name=ctComposerPublishPageMethod]:checked').val();
		switch(val) {
			case 'PAGE_TYPE':
				$("#ccm-composer-choose-parent-page-type").show();
				$("#ccm-composer-choose-parent").hide();
				break;
			case 'PARENT':
				$("#ccm-composer-choose-parent-page-type").hide();
				$("#ccm-composer-choose-parent").show();
				break;
			default:
				$("#ccm-composer-choose-parent-page-type").hide();
				$("#ccm-composer-choose-parent").hide();
			break;
		}
		
		$("div.ccm-composer-content-item-list").sortable({
			handle: 'img.ccm-composer-content-item-icon',
			cursor: 'move',
			opacity: 0.5,
			stop: function() {
				var ualist = $(this).sortable('serialize');
				$.post('<?=$this->action("save_content_items", $ct->getCollectionTypeID())?>', ualist, function(r) {
	
				});
			}
		
		});
	}
	
	$(function() {
		$("input[name=ctIncludeInComposer], input[name=ctComposerPublishPageMethod]").click(function() {
			ccm_setupComposerFields();
		});
		ccm_setupComposerFields();
	});
	
	</script>
    
<? } else { ?>

	<div class="ccm-pane-body">
    	<p><?=t('Unable to access composer settings.'); ?></p>
	</div>
    
    <div class="ccm-pane-footer">
        <? print $ih->button(t('Back to Page Types'), $this->url('/dashboard/pages/types'), 'left'); ?>
    </div>
    
<? } ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

	<!-- END Composer Settings pane -->
