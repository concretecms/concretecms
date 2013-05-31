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
				<?=$form->select('cmpMessageFilter', array('any'=>t('** Any')) + $cmpFilterTypes, array("class" => "span2"))?>
				<?=$form->select('cmpMessageSort', $cmpSortTypes, array("class" => "span2"))?>
				<button type="submit" class="btn"><?=t('Search')?></button>
			</div>
		</form>
	</div>
</div>
<div class="ccm-pane-body">
	<form action="<?=$this->action('bulk_update')?>" method="post" id="ccm-conversation-messages-multiple-update">
	<? Loader::helper('validation/token')->output(); ?>
	<div style="margin-bottom: 10px">
		<select id="ccm-conversation-messages-multiple-operations" class="span3" disabled="">
				<option value""><?=t('** With Selected')?></option>
			<?php foreach($cmpFilterTypes as $status) { ?>
				<option value="<?=$status?>"><?=t('Flag as %s', $status)?></option>
			<? } ?>
		</select>
	</div>
	<table class="ccm-conversation-messages table table-condensed ccm-results-list">
		<tr>
			<th><input type="checkbox" id="ccm-conversation-message-list-all"/></th>
			<th><?=t('Message')?></th>
			<th><?=t('Posted')?></th>
		</tr>
<? if (count($messages > 0)) { ?>
	<? foreach($messages as $msg) { 
		$ui = $msg->getConversationMessageUserObject();
	?>
	<tr>
		<td><?=$form->checkbox('cnvMessageID[]', $msg->getConversationMessageID())?></td>
		<td><?
		$cnv = $msg->getConversationObject();
		if(is_object($cnv)) {
			$page = $cnv->getConversationPageObject();
			if (is_object($page)) { ?>
				<div><a href="<?=Loader::helper('navigation')->getLinkToCollection($page)?>"><?=$page->getCollectionName()?></a></div>
			<? }
		} ?>
		<div class="ccm-conversation-message-summary">
			<?=$msg->getConversationMessageBodyOutput()?>
		</div>
		</td>
		<td>
			
			<?=$msg->getConversationMessageDateTimeOutput();?> <?=t('By')?> <? if (!is_object($ui)) { ?><?=t('Anonymous')?><? } else { ?><?=$ui->getUserDisplayName()?><? } ?>
		</td>
	</tr>
	<? } ?>
	</table>
	</form>
	<?=$list->displaySummary()?>
<? } else { ?>
	<p><?=t('There are no conversations with messages.')?></p>
<? } ?>

</div>

<div class="ccm-pane-footer">
	<?=$list->displayPagingV2()?>
</div>
<script type="text/javascript">
$(function() {
	$('.ccm-conversation-messages .ccm-input-checkbox').change(function() {
		if($(this).is(':checked')) {
			$('#ccm-conversation-messages-multiple-operations').attr('disabled',false);
		} else {
			var disableSelect = true;
			$('.ccm-conversation-messages .ccm-input-checkbox').each(function() {
				if($(this).is(':checked')) {
					disableSelect = false;
					return false;
				}
			});
			if(disableSelect) {
				$('#ccm-conversation-messages-multiple-operations').attr('disabled',true);
			}
		}
	});
	
	$('#ccm-conversation-message-list-all').change(function() {
		if($(this).is(':checked')) {
			$('.ccm-conversation-messages .ccm-input-checkbox').each(function() { 
				$(this).attr('checked',true);
				$('#ccm-conversation-messages-multiple-operations').attr('disabled',false); 
			});
		} else {
			$('.ccm-conversation-messages .ccm-input-checkbox').each(function() { 
				$(this).attr('checked',false);
				$('#ccm-conversation-messages-multiple-operations').attr('disabled',true);
			});
		}
	});
	
	$('#ccm-conversation-messages-multiple-operations').change(function() {
		if($(this).val() != 'any') {
			if(confirm('<?=t('Are you sure?')?>')) {
				$('#ccm-conversation-messages-multiple-update').submit();
			}
		}
	});
	
	
});
</script>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(); ?>
