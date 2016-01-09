<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Core::make('helper/form');
?>

<form method="post" action="<?=$view->action('submit')?>" class="form-horizontal">
<?php if (PermissionKey::getByHandle('add_topic_tree')->validate()) { ?>
	<?=Loader::helper('validation/token')->output('submit')?>
	<div class="control-group">
		<?=$form->label('topicTreeName', t('Tree Name'))?>
		<div class="controls">
			<?=$form->text('topicTreeName', $topicTreeName, array('class' => 'span4'))?>
		</div>
	</div>
<?php } else { ?>
	<p><?=t('You may not add topic trees.')?></p>
<?php } ?>

<div class="ccm-dashboard-form-actions-wrapper">
    <div class="ccm-dashboard-form-actions ">
        <a href="<?=$view->url('/dashboard/system/attributes/topics')?>" class="btn btn-default pull-left"><?=t("Cancel")?></a>
        <button type="submit" class="btn btn-primary pull-right"><?=t('Add Topic Tree')?></button>
    </div>
</div>
</form>