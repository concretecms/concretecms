<?php defined('C5_EXECUTE') or die('Access Denied');
$form = Loader::helper('form'); ?>
	<form method="post" id="url-form" action="<?php echo $view->action('')?>">
		<?=$this->controller->token->output('update_statistics')?>
        <div class="form-group">
            <div class="checkbox">
                <label>
                <?=$form->checkbox('STATISTICS_TRACK_PAGE_VIEWS', 1, $STATISTICS_TRACK_PAGE_VIEWS); ?>
                <span><?=t('Track page view statistics.');?></span>
                </label>
            </div>
        </div>
		<div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
			    <?php echo $interface->submit(t('Save'), null, 'right', 'btn-primary');?>
            </div>
		</div>
	</form>

