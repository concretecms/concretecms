<? defined('C5_EXECUTE') or die("Access Denied."); ?>


<?
if ($enablePostingFromAggregator && is_object($composer)) { ?>

	<div data-aggregator-block-id="<?=$b->getBlockID()?>">

	<div style="display: none">
		<div data-form="aggregator-post">
			<form data-form="composer">
			<?=Loader::helper('composer/form')->display($composer)?>
			<div class="dialog-buttons">
			<button type="button" data-composer-btn="exit" class="btn pull-left"><?=t('Cancel')?></button>
			<button type="button" data-composer-btn="publish" class="btn btn-primary pull-right"><?=t('Post')?></button>
			</div>
			</form>
		</div>
	</div>

	<button class="btn" data-action="post-to-aggregator" type="button"><?=t('Post')?></button><br/><br/>

	</div>

	<script type="text/javascript">
	$(function() {

		var $db = $('div[data-aggregator-block-id=<?=$b->getBlockID()?>]'),
			$dialog = $db.find('div[data-form=aggregator-post]'),
			$postToAggregator = $db.find('button[data-action=post-to-aggregator]');

		$db.find('form[data-form=composer]').ccmcomposer({
			publishURL: '<?=html_entity_decode($this->action("post"))?>',
			onExit: function() {
				$dialog.dialog('close');
			},
			autoSaveEnabled: false,
			publishReturnMethod: 'ajax'
		});

		$postToAggregator.on('click', function() {
			$dialog.dialog({
				modal: true,
				width: 400,
				height: 540,
				title: '<?=t("Post to Aggregator")?>',
				open: function() {
					var $buttons = $dialog.find('.dialog-buttons').hide().clone(true,true);
					$(this).dialog('option', 'buttons', [{}]);
					$(this).closest('.ui-dialog').find('.ui-dialog-buttonset').html('').append($buttons.show());
				}
			});
		});
	});
	</script>

<? } ?>

<?
  Loader::element('aggregator/display', array(
  	'aggregator' => $aggregator,
  	'list' => $itemList
  ));
?>

