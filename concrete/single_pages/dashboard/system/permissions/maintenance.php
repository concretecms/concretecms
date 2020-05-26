<?php defined('C5_EXECUTE') or die("Access Denied.");
$h = Loader::helper('concrete/dashboard');
$form = Loader::helper('form');
echo $h->getDashboardPaneHeaderWrapper(t('Maintenance Mode'), false, 'span6 offset3', false);?>

<form id="maintenance-mode-form" action="<?php echo $view->action('')?>" method="post" role="form">
    <?php echo $this->controller->token->output('update_maintenance')?>
    <fieldset>
        <div class="alert alert-primary" role="alert">
            <?=t('Dashboard will still be accessible in maintenance mode.'); ?> 
        </div>
        <div class="custom-control custom-radio">
          <?=$form->radio('site_maintenance_mode', '1', $site_maintenance_mode)?>
          <?=$form->label('site_maintenance_mode1', t('Enabled - for emergencies'), ['class'=>'custom-control-label'])?>
        </div>
        <div class="custom-control custom-radio">
          <?=$form->radio('site_maintenance_mode', '0', $site_maintenance_mode)?>
          <?=$form->label('site_maintenance_mode2', t('Disabled'), ['class'=>'custom-control-label'])?>
        </div>
    </fieldset>	

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">    
            <button class="pull-right btn btn-primary" type="submit" ><?=t('Save')?></button>
        </div>
    </div>
</form>
<?php echo $h->getDashboardPaneFooterWrapper(false);
