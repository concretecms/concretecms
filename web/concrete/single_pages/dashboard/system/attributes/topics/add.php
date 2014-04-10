<?
defined('C5_EXECUTE') or die("Access Denied.");
Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add Topic Tree'), false, 'span8 offset2', false);?>
<form method="post" action="<?=$view->action('submit')?>" class="form-horizontal">

<div class="ccm-pane-body">
<? if (PermissionKey::getByHandle('add_topic_tree')->validate()) { ?>
	<?=Loader::helper('validation/token')->output('submit')?>
	<div class="control-group">
		<?=$form->label('topicTreeName', t('Tree Name'))?>
		<div class="controls">
			<?=$form->text('topicTreeName', $topicTreeName, array('class' => 'span4'))?>
		</div>
	</div>
<? } else { ?>
	<p><?=t('You may not add topic trees.')?></p>
<? } ?>
</div>

<div class="ccm-pane-footer">
	<a href="<?=$view->url('/dashboard/system/attributes/topics')?>" class="btn pull-left"><?=t("Cancel")?></a>
	<button type="submit" class="btn btn-primary pull-right"><?=t('Add Topic Tree')?></button>
</div>
</form>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
