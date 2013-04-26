<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Messages'), false, false, false);?>
<style type="text/css">
	div.ccm-conversation-message-summary {
		width: 100%;
	}
	table.ccm-conversation-messages td {
		vertical-align: top;
		padding: 5px;
	}
</style>

<div class="ccm-pane-options">
	<div class="ccm-pane-options-permanent-search">
		<form method="get" action="<?=$this->action('view')?>" class="form-inline">
			<div class="control-group">
				<?=$form->text('cmpMessageKeywords', array("placeholder" => t('Search Messages')))?>
				<?=$form->select('cmpMessageFilter', $cmpFilterTypes, array("class" => "span2"))?>
				<?=$form->select('cmpMessageSort', $cmpSortTypes, array("class" => "span2"))?>
				<button type="submit" class="btn"><?=t('Search')?></button>
			</div>
		</form>
	</div>
</div>
<div class="ccm-pane-body">
	<?=$list->displaySummary()?>
	<table class="ccm-conversation-messages">
<? if (count($messages > 0)) { ?>
	<? foreach($messages as $msg) { ?>
	<tr>
		<td><?=$form->checkbox('cnvMessageID[]', $msg->getConversationMessageID())?></td>
		<td><?
		$cnv = $msg->getConversationObject();
		$page = $cnv->getConversationPageObject();
		if (is_object($page)) { ?>
			<div><a href="<?=Loader::helper('navigation')->getLinkToCollection($page)?>"><?=$page->getCollectionName()?></a></div>
		<? } ?>
		<div class="ccm-conversation-message-summary">
			<?=$msg->getConversationMessageBodyOutput()?>
		</div>
		</td>
	</tr>
	<? } ?>
	</table>
<? } else { ?>
	<p><?=t('There are no conversations with messages.')?></p>
<? } ?>

</div>

<div class="ccm-pane-footer">
	<?=$list->displayPagingV2()?>
</div>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(); ?>
