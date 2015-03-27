<? defined('C5_EXECUTE') or die("Access Denied.");?>
<form method="post" class="ccm-dashboard-content-form" action="<?=$view->action('submit')?>">
	<?=$this->controller->token->output('submit')?>
	<fieldset>
		<p class="lead"><?=t('Concrete5 Extensions')?></p>
		<div class="checkbox">
			<label>
				<?=$form->checkbox('enable_filemanager', 1, $filemanager)?> <?=t('Enable file selection from file manager.')?>
			</label>
		</div>
		<div class="checkbox">
			<label>
				<?=$form->checkbox('enable_sitemap', 1, $sitemap)?> <?=t('Enable page selection from sitemap.')?>
			</label>
		</div>
	</fieldset>
	<fieldset>
		<p class="lead"><?=t('Redactor Plugins')?></p>
		<? foreach($plugins as $key => $name) { ?>
		<div class="checkbox">
			<label>
				<?=$form->checkbox('plugin[]', $key, $manager->isSelected($key))?> <?=$name?>
			</label>
		</div>
		<? } ?>
	</fieldset>
	<div class="ccm-dashboard-form-actions-wrapper">
		<div class="ccm-dashboard-form-actions">
			<button class="pull-right btn btn-success" type="submit" ><?=t('Save')?></button>
		</div>
	</div>
</form>