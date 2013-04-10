<?php defined('C5_EXECUTE') or die("Access Denied."); 
$c = Page::getCurrentPage();
if (is_object($discussion)) { ?>

	<div class="ccm-discussion" data-discussion-block-id="<?=$b->getBlockID()?>">

	<?
	if ($enableNewTopics && $composer) { ?>

		<div style="display: none">
			<div data-form="discussion">
				<form data-form="composer">
				<?=Loader::helper('composer/form')->display($composer)?>
				<div class="dialog-buttons">
				<button type="button" data-composer-btn="exit" class="btn pull-left"><?=t('Cancel')?></button>
				<button type="button" data-composer-btn="publish" class="btn btn-primary pull-right"><?=t('Post')?></button>
				</div>
				</form>
			</div>
		</div>

		<button class="pull-right btn" data-action="add-conversation" type="button"><?=t('New Topic')?></button>

		<h3><?=$c->getCollectionName()?></h3>
	
		<? if (count($topics)) { ?>

			<ul class="ccm-discussion-topics">

			<? foreach($topics as $t) { ?>
			<li>
				<div class="ccm-discussion-topic-replies"><em>0</em> Replies</div>
				<div class="ccm-discussion-topic-details"><h3><a href="<?=Loader::helper('navigation')->getLinkToCollection($t)?>"><?=$t->getCollectionName()?></a></h3></div>
			</li>
			<? } ?>

			</div>

		<? } else { ?>
			<div class="well"><?=t('No topics have been posted.')?></div>
		<? } ?>
	<? } ?>

	</div>

<? } ?>

<script type="text/javascript">
$(function() {

	var $db = $('div[data-discussion-block-id=<?=$b->getBlockID()?>]'),
		$dialog = $db.find('div[data-form=discussion]'),
		$addTopic = $db.find('button[data-action=add-conversation]');

	$('form[data-form=composer]').ccmcomposer({
		publishURL: '<?=html_entity_decode($this->action("post"))?>',
		onExit: function() {
			$dialog.dialog('close');
		},
		autoSaveEnabled: false,
		publishReturnMethod: 'ajax'
	});

	$addTopic.on('click', function() {
		$dialog.dialog({
			modal: true,
			width: 400,
			height: 540,
			title: '<?=t("Add Topic")?>',
			open: function() {
				var $buttons = $dialog.find('.dialog-buttons').hide().clone(true,true);
				$(this).dialog('option', 'buttons', [{}]);
				$(this).closest('.ui-dialog').find('.ui-dialog-buttonset').html('').append($buttons.show());
			}
		});
	});
});
</script>