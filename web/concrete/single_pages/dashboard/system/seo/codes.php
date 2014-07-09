<?php defined('C5_EXECUTE') or die("Access Denied.");

/* @var $ih ConcreteInterfaceHelper */
$ih = Loader::helper('concrete/ui');
/* @var $form FormHelper */
$form = Loader::helper('form');
?>
<form id="tracking-code-form" action="<?=$view->action('')?>" method="post">
    <?=Core::make('helper/validation/token')->output('update_tracking_code')?>
	<div class="form-group">
		<?=$form->label('tracking_code', t('Tracking Codes'))?>
		<div class="input">
			<?=$form->textarea('tracking_code', $tracking_code, array('class' => 'xxlarge', 'rows' => 4, 'cols' => 50))?>
			<span class="help-block"><?=t('Any HTML you paste here will be inserted at either the bottom or top of every page in your website automatically.')?></span>
		</div>
	</div>
	
	
	<div class="form-group">
		<label class="radio"><?=$form->radio('tracking_code_position', 'top', $tracking_code_position)?> <span><?=t('Header of the page')?></span></label>
	    <label class="radio"><?=$form->radio('tracking_code_position', 'bottom', $tracking_code_position)?> <span><?=t('Footer of the page')?></span></label>
	</div>
	
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?
                $submit = $ih->submit( t('Save'), 'tracking-code-form', 'right', 'primary');
                print $submit;
            ?>
        </div>
    </div>
</form>