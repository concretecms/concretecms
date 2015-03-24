<? defined('C5_EXECUTE') or die("Access Denied.");?>
<form method="post" class="ccm-dashboard-content-form" action="<?=$view->action('submit')?>">
	<?=$this->controller->token->output('submit')?>
	<fieldset>
		<p class="lead"><?=t('Concrete5 Extensions')?></p>
		<div class="checkbox">
			<label>
				<?=$form->checkbox('filemanager', 1)?> <?=t('Enable file selection from file manager.')?>
			</label>
		</div>
		<div class="checkbox">
			<label>
				<?=$form->checkbox('sitemap', 1)?> <?=t('Enable page selection from sitemap.')?>
			</label>
		</div>
		<div class="checkbox">
			<label>
				<?=$form->checkbox('lightbox', 1)?> <?=t('Add lightbox option to link editor.')?>
			</label>
		</div>
	</fieldset>
	<fieldset>
		<p class="lead"><?=t('Redactor Plugins')?></p>
		<? foreach($plugins as $key => $name) { ?>
		<div class="checkbox">
			<label>
				<?=$form->checkbox('<?=$key?>', 1, $manager->isSelected($key))?> <?=$name?>
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