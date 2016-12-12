<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<form id="tracking-code-form" action="<?=$view->action('')?>" method="post">
    <?=Core::make('helper/validation/token')->output('update_tracking_code')?>
	
	<div class="row">
	    <div class="col-md-12">
        	<div class="form-group">
        		<?=$form->label('tracking_code', t('Tracking Codes'))?>
        		<div class="input">
        			<?=$form->textarea('tracking_code', $tracking_code, array('class' => 'xxlarge', 'rows' => 12, 'cols' => 50))?>
        			<span class="help-block"><?=t('Any HTML you paste here will be inserted at either the bottom or top of every page in your website automatically.')?></span>
        		</div>
        	</div>
        	
        	<div class="form-group">
        		<div class="radio"><label><?=$form->radio('tracking_code_position', 'top', $tracking_code_position)?> <span><?=t('Header of the page')?></span></label></div>
        	    <div class="radio"><label><?=$form->radio('tracking_code_position', 'bottom', $tracking_code_position)?> <span><?=t('Footer of the page')?></span></label></div>
        	</div>
        	
            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <button type="submit" class="btn btn-primary pull-right" name="tracking-code-form"><?=t('Save')?></button>
                </div>
            </div>
	    </div>
	</div>
</form>