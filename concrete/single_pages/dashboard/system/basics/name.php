<?php defined('C5_EXECUTE') or die("Access Denied.");?>
<form method="post" class="ccm-dashboard-content-form" action="<?=$view->action('update_sitename')?>">
	<?=$this->controller->token->output('update_sitename')?>

	<fieldset>
	<div class="form-group">
		<label for="SITE" class="launch-tooltip control-label" data-placement="right" title="<?=t('By default, site name is displayed in the browser title bar. It is also the default name for your project on concrete5.org')?>"><?=t('Site Name')?></label>
		<?=$form->text('SITE', $site, array('class' => 'span4'))?>
	</div>
	</fieldset>
	<div class="ccm-dashboard-form-actions-wrapper">
	<div class="ccm-dashboard-form-actions">
		<button class="pull-right btn btn-primary" type="submit" ><?=t('Save')?></button>
	</div>
	</div>
</form>