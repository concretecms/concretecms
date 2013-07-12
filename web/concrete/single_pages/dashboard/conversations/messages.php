<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ninja-div"></div>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Messages'), false, false, false);
$ip = Loader::helper('validation/ip'); ?>
<style type="text/css">

	div.message-actions {
		display: none;
	}
	
	div.popover-content ul {
		list-style-type: none;
		margin-left: 0;
	}
	
	td.hidden-actions {
		display: none;
	}

	div.notifications {
		position: absolute;
		display: none;
		top: 20px;
		right: 20px;
		height: 300px;
		width: 300px;
		background: purple;
		z-index: 101;
	}

	span.inactive {
		color: #aaa;
	}

	div.ccm-conversation-message-summary {
		width: 100%;
	}
	table.ccm-conversation-messages td {
		vertical-align: top;
		padding: 5px;
		padding-top: 20px;
		padding-bottom: 20px;
	}
	
	table.ccm-conversation-messages tr {
		cursor: pointer;
	}
	
	table.ccm-conversation-messages tr.deleted {
		background-color: #efdfde;
	}
	
	table.ccm-conversation-messages tr.pending {
		background-color: #ddecf8;
	}
	
	table.ccm-conversation-messages tr.flagged {
		background-color: #faf9e1;
	}
	
	table.ccm-conversation-messages tr p.message-status {
		font-style: italic;
	}
	
	table.ccm-conversation-messages tr:hover {
		background: #f5f5f5;
	}
	
	table.ccm-conversation-messages tr td.message-cell {
		width: 45%; 
		padding-right: 5%;
	}
	
	table.ccm-conversation-messages tr td.message-cell a.read-all{
		display: none;
		margin-bottom: 5px;
	}
	
	table.ccm-conversation-messages tr .message-actions ul  {
		list-style-type: none;
		margin-left: 0;
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
		$msgID = $msg->getConversationMessageID();
		if(!$msg->isConversationMessageApproved() && !$msg->isConversationMessageDeleted()) {
			$pendingClass = "pending";
		} else {
			 $pendingClass = '';
		}
		if($msg->isConversationMessageDeleted()) {
			$deletedClass = "deleted";
		} else {
			$deletedClass = '';
		}
		
		if($msg->isConversationMessageFlagged()) {
			$flagClass = 'flagged';	
		} else {
			$flagClass = '';
		}
		$ui = $msg->getConversationMessageUserObject();
	?>
	<tr class="<?php echo $pendingClass ?> <?php echo $flagClass ?> <?php echo $deletedClass ?> message-entry" data-id = "<?php echo $msgID ?>">
		<td><?=$form->checkbox('cnvMessageID[]', $msg->getConversationMessageID())?></td>
		<td class="message-cell">
		<div class="ccm-conversation-message-summary">
			<div class="message-output">
				<?=$msg->getConversationMessageBodyOutput(true)?>
			</div>
			<a href="#" data-open-text="<?php echo t('View full message.') ?>" data-close-text="<?php echo t('Minimize message') ?>" class="read-all truncated btn"><?php echo t('View full message') ?></a>
			<?php if($flagClass) { ?>
				<p class="message-status"><?php echo t('Message is flagged as spam.') ?></p>
			<?php } ?>
			<?php if($deletedClass) { ?>
				<p class="message-status"><?php echo t('Message is currently deleted.') ?></p>
			<?php } ?>
			<?php if($pendingClass) { ?>
				<p class="message-status"><?php echo t('Message is currently pending approval.') ?></p>
			<?php } ?>
		</div>
		</td>
		<td>
			<?=$msg->getConversationMessageDateTimeOutput(array(DATE_APP_GENERIC_MDY_FULL . ' H:i:s'));?> 
			<p><?=t('By')?> <? if (!is_object($ui)) { ?><?=t('Anonymous')?><? } else { ?><?=$ui->getUserDisplayName()?><? } ?></p>

			<?
			$cnv = $msg->getConversationObject();
			if(is_object($cnv)) {
				$page = $cnv->getConversationPageObject();
				if (is_object($page)) { ?>
					<div><a href="<?=Loader::helper('navigation')->getLinkToCollection($page)?>"><?=$page->getCollectionPath()?></a></div>
				<? }
			} ?>
		</td>
		<td class="hidden-actions">
			<div class="message-actions message-actions<?php echo $msgID ?>" data-id="<?php echo $msgID ?>">
				<ul>
					<li>
						<?php if($msg->isConversationMessageApproved()) { ?>
						<a class = "unapprove-message" data-rel-message-id="<?php echo $msgID ?>" href="#"><?php echo t('Unapprove') ?></a>
						<?php } else {  ?>
						<a class = "approve-message" data-rel-message-id="<?php echo $msgID ?>" href="#"><?php echo t('Approve') ?></a>
						<?php } ?>
					</li>
					<li>
					<?php if($msg->isConversationMessageDeleted()){ ?>
						<a class = "restore-message" data-rel-message-id="<?php echo $msgID ?>" href="#"><?php echo t('Restore') ?></a>
					<?php } else { ?>
						<a class = "delete-message" data-rel-message-id="<?php echo $msgID ?>" href="#"><?php echo t('Delete') ?></a>
					<?php } ?>
					</li>
					<li><?php if($msg->isConversationMessageFlagged()) { ?>
						<a class = "unmark-spam" data-rel-message-id="<?php echo $msgID ?>" href="#"><?php echo t('Unmark as spam') ?></a>
					<?php } else { ?>
						<a class = "mark-spam" data-rel-message-id="<?php echo $msgID ?>" href="#"><?php echo t('Mark as spam') ?></a>
					<?php } ?>
					</li>
					<li>
						<a class = "mark-user" data-rel-message-id="<?php echo $msgID ?>" href="#"><?php echo t('Mark all user posts as spam') ?></a>
					</li>
					<li>
						<?php if(is_object($ui) && $ui->isActive()) { ?>
						<a class = "deactivate-user" data-rel-message-id="<?php echo $msgID ?>" href="#"><?php echo t('Deactivate User') ?></a>
						<?php } else { ?>
						<span class="inactive"><?php echo t('User deactivated'); ?></span>
						<?php }?>
					</li>
					<li>
						<?php if($ip->check(long2ip($msg->cnvMessageSubmitIP))) { ?>
						<a class = "block-ip" data-rel-message-id="<?php echo $msgID ?>" href="#"><?php echo t('Block user IP Address') ?></a>
						<?php } else { ?>
						<span class="inactive"><?php echo t('IP Banned') ?></span>
						<?php } ?>
					</li>						
				</ul>
			</div>
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
	
	var attachButtonBindings = function(parentObj, targetSelector, selectedAction) {
		var controllerActions = {};
		controllerActions.approve = '<?php echo $this->action('approve'); ?>';
		controllerActions.unapprove = '<?php echo $this->action('unapprove'); ?>';
		controllerActions.markSpam = '<?php echo $this->action('markSpam'); ?>';
		controllerActions.unmarkSpam = '<?php echo $this->action('unmarkSpam'); ?>';
		controllerActions.deleteMessage = '<?php echo $this->action('deleteMessage'); ?>';
		controllerActions.restoreMessage = '<?php echo $this->action('restoreMessage'); ?>';
		controllerActions.markUser = '<?php echo $this->action('markUser'); ?>';
		controllerActions.deactivateUser = '<?php echo $this->action('deactivateUser'); ?>';
		controllerActions.blockUserIP = '<?php echo $this->action('blockUserIP'); ?>';
		var selectedButtons = parentObj.find(targetSelector);
		if(selectedButtons){
			selectedButtons.each(function(){
				$(this).unbind(); // prevent duplicate binding
				$(this).on('click', function(event){
					event.preventDefault();
					var url = controllerActions[selectedAction];
					$.ajax({
					  type: "POST",
					  url: url,
					  data: {messageID : $(this).attr('data-rel-message-id')},
					  success: function(){
						$('.container').load($(location).attr('href')+' .container', function(){ 
							attachBindings($('.ccm-conversation-messages'));  // reattach bindings after ajax reload
						});
					  }
					});
				})
			});
		}
	}
	var tmp = $.fn.popover.Constructor.prototype.show;
	$.fn.popover.Constructor.prototype.show = function () {
	  tmp.call(this);
	  if (this.options.callback) {
	    this.options.callback();
	  }
	}
	
	var attachBindings = function(parentObj) {
	
		$('.ccm-conversation-message-summary .message-output').each( function(){  // truncating messages and showing full on trigger click.
			var summary = '';
			var ellipsis = '';
			var summary = $(this).text();
			$(this).find('a.read-all').attr('data-rel-summary', summary);
			var splitSummary = summary.split(' ', 50);
			if(splitSummary.length == 50) {
				var ellipsis = ' ...';
				$(this).next('.read-all').show().css('display', 'block');
			}
			var truncatedSummary = splitSummary.join(' ') + ellipsis;
			$(this).next('a.read-all').attr('data-rel-truncated-summary', truncatedSummary); // tethering summary + trunc summary to toggle
			$(this).next('a.read-all').attr('data-rel-summary', summary);
			$(this).html('<p>' + truncatedSummary + '</p>');
			$(this).next('a.read-all').on('click', function(event){
				event.preventDefault();
				event.stopPropagation();
				$('.message-entry').each(function(){
					$('.popover').each(function(){
						if($(this).is(':visible')) {
							$(this).prev('.message-entry').popover('hide');
						}
					})
				});
				if($(this).hasClass('truncated')) {
					$(this).prev('.message-output').html('<p>'+$(this).attr('data-rel-summary')+'</p>');
					$(this).removeClass('truncated');
					$(this).addClass('expanded');
					$(this).text($(this).attr('data-close-text'));
				} else {
					$(this).prev('.message-output').html('<p>'+$(this).attr('data-rel-truncated-summary')+'</p>');
					$(this).removeClass('expanded');
					$(this).addClass('truncated');
					$(this).text($(this).attr('data-open-text'));
				}
			})
		});
		$('.message-entry').each(function(){  // message entry click 
			$(this).unbind('click');
			$(this).on('click', function(event) {
				$('.message-entry').each(function(){
					$('.popover').each(function(){
						if($(this).is(':visible')) {
							$(this).prev('.message-entry').popover('hide');
						}
					})
				});
				$(this).popover('show');
				event.stopPropagation();
			});
			$(this).popover({
				html: true,
				trigger: 'manual',
				content: $('.message-actions'+$(this).attr('data-id')).html(),
				placement: 'bottom', 
				callback: function(){
					attachButtonBindings($('.popover-content'), 'a.approve-message', 'approve');  // binding buttons
					attachButtonBindings($('.popover-content'), 'a.unapprove-message', 'unapprove');
					attachButtonBindings($('.popover-content'), 'a.mark-spam', 'markSpam');
					attachButtonBindings($('.popover-content'), 'a.unmark-spam', 'unmarkSpam');
					attachButtonBindings($('.popover-content'), 'a.delete-message', 'deleteMessage');
					attachButtonBindings($('.popover-content'), 'a.restore-message', 'restoreMessage');
					attachButtonBindings($('.popover-content'), 'a.deactivate-user', 'deactivateUser');
					attachButtonBindings($('.popover-content'), 'a.mark-user', 'markUser');
					attachButtonBindings($('.popover-content'), 'a.block-ip', 'blockUserIP');
				}
			})
		});
		$(document).on('click', function(){   // close popovers on click off
			$('.message-entry').each(function(){
				$('.popover').each(function(){
					if($(this).is(':visible')) {
						$(this).prev('.message-entry').popover('hide');
					}
				})
			})
		})
	
		$('.ccm-conversation-messages .ccm-input-checkbox').on('click', function(event){
			event.stopPropagation();  // keep message summary expand from triggering popover.
		});
		
		$('#ccm-conversation-message-list-all').change(function() {  // bulk checkbox checker / unchecker
			if($(this).is(':checked')) {
				$('.ccm-conversation-messages .ccm-input-checkbox').each(function() { 
					$(this).prop('checked', true);
					$('#ccm-conversation-messages-multiple-operations').attr('disabled',false); 
				});
			} else {
				$('.ccm-conversation-messages .ccm-input-checkbox').each(function() { 
					$(this).prop('checked', false);
					$('#ccm-conversation-messages-multiple-operations').attr('disabled',true);
				});
			}
		});
		
		$('#ccm-conversation-messages-multiple-operations').change(function() {
			if($(this).val() != 'any') {
				if(confirm('<?=t('Are you sure?')?>')) {  // hidden "bulkTask" input is appended to form with given task name prior to submit
					$('#ccm-conversation-messages-multiple-update').append('<input type="hidden" name="bulkTask" value="'+$('#ccm-conversation-messages-multiple-operations').val()+'"/>');
					$('#ccm-conversation-messages-multiple-update').submit();
				}
			}
		});
		
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
	}

	attachBindings($('.ccm-conversation-messages'));
	
});
</script>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(); ?>
