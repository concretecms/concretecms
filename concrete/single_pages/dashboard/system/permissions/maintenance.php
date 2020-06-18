<?php defined('C5_EXECUTE') or die("Access Denied.");
$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
$h = $app->make('helper/concrete/dashboard');
$form = $app->make('helper/form');
echo $h->getDashboardPaneHeaderWrapper(t('Maintenance Mode'), false, 'span6 offset3', false);?>

<form id="maintenance-mode-form" action="<?php echo $view->action('')?>" method="post" role="form">
    <?php echo $this->controller->token->output('update_maintenance')?>
    <fieldset>
        <div class="form-group">
            <label class="col-form-label"><?= t('Only Dashboard Works') ?></label>
            <div class="form-check">
              <label class="form-check-label">
                <?=$form->radio('site_maintenance_mode', '1', $site_maintenance_mode)?> <span><?=t('Enabled - for emergencies')?></span>
              </label>
            </div>
            <div class="form-check">
              <label class="form-check-label">
                <?=$form->radio('site_maintenance_mode', '0', $site_maintenance_mode)?> <span><?=t('Disabled')?></span>
              </label>
            </div>
        </div>
    </fieldset>	

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">    
            <button class="float-right btn btn-primary" type="submit" ><?=t('Save')?></button>
        </div>
    </div>
</form>
<?php echo $h->getDashboardPaneFooterWrapper(false);
