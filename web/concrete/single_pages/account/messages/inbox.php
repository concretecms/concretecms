<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="page-header">
<h1><?=t("Private Messages")?></h1>
</div>

    	<?=$error->output(); ?>
    	<? switch($this->controller->getTask()) { 
    		case 'view_message': ?>

    		<div><a href="<?=$this->url('/profile/messages', 'view_mailbox', $box)?>">&lt;&lt; <?=t('Back to Mailbox')?></a></div><br/>
    		
    		<h1><?=t('Message Details')?></h1>
    		<form method="post" action="<?=$this->action('reply', $box, $msg->getMessageID())?>">
    		<div class="ccm-profile-detail">
				<div class="ccm-profile-section">
					<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top" class="ccm-profile-message-from"><a href="<?=$this->url('/profile', 'view', $msg->getMessageRelevantUserID())?>"><?=$av->outputUserAvatar($msg->getMessageRelevantUserObject())?></a>
						<a href="<?=$this->url('/profile', 'view', $msg->getMessageRelevantUserID())?>"><?=$msg->getMessageRelevantUserName()?></a>

						</td>
						<td valign="top">
							<h2><?=$subject?></h2>
							<div><?=$dateAdded?></div>
						</td>
					</tr>
					</table>
					
    			</div>
    			
   				<?=$msg->getFormattedMessageBody()?>
    		</div>
			<div class="ccm-profile-buttons">
				<? if ($msg->getMessageAuthorID() != $ui->getUserID()) { ?>
					<? 
					$mui = $msg->getMessageRelevantUserObject();
					if (is_object($mui)) { 
						if ($mui->getUserProfilePrivateMessagesEnabled()) { ?>
							<?=$form->submit('button_submit', t('Reply'))?>
						<? } 
						
					}?>
				<? } ?>
				<?=$form->submit('button_delete', t('Delete'), array('onclick' => 'if(confirm(\'' . t('Delete this message?') . '\')) { window.location.href=\'' . $deleteURL . '\'}; return false'))?>
				<?=$form->submit('button_cancel', t('Back'), array('onclick' => 'window.location.href=\'' . $backURL . '\'; return false'))?>
			</div>
			</form>
			    		
    		
    		<? 
    			break;
    		case 'view_mailbox': ?>

			<?=Loader::helper('concrete/interface')->tabs(array(
				array($this->action('view_mailbox', 'inbox'), t('Inbox'), $mailbox == 'inbox'),
				array($this->action('view_mailbox', 'sent'), t('Sent'), $mailbox == 'sent')
			), false)?>
			
    		
			<table class="ccm-profile-messages-list table-bordered table-striped table" border="0" cellspacing="0" cellpadding="0">
			<thead>
			<tr>
				<th><? if ($mailbox == 'sent') { ?><?=t('To')?><? } else { ?><?=t('From')?><? } ?></th>
				<th><?=t('Subject')?></th>
				<th><?=t('Sent At')?></th>
				<th><?=t('Status')?></th>
			</tr>
			</thead>
			<tbody>    		
    		
    		
    		<?
    			if (is_array($messages)) { 
					foreach($messages as $msg) { ?>
					
					<tr>
						<td class="ccm-profile-message-from">
						<a href="<?=$this->url('/profile', 'view', $msg->getMessageRelevantUserID())?>"><?=$av->outputUserAvatar($msg->getMessageRelevantUserObject())?></a>
						<a href="<?=$this->url('/profile', 'view', $msg->getMessageRelevantUserID())?>"><?=$msg->getMessageRelevantUserName()?></a>
						</td>
						<td class="ccm-profile-messages-item-name"><a href="<?=$this->url('/profile/messages', 'view_message', $mailbox, $msg->getMessageID())?>"><?=$msg->getFormattedMessageSubject()?></a></td>
						<td style="white-space: nowrap"><?=$msg->getMessageDateAdded('user', t('F d, Y \a\t g:i A'))?></td>
						<td><?=$msg->getMessageStatus()?></td>
					</tr>
					
					
			
				<? } ?>
			<? } else { ?>
				<tr>
					<Td colspan="4"><?=t('No messages found.')?></td>
				</tr>
			<? } ?>
			</tbody>
			</table>
			
			
			<?

				$messageList->displayPaging();
    			break;
    		case 'reply_complete': ?>
    		
    		<h3><?=t('Reply Sent.')?></h3>
    		<a href="<?=$this->url('/profile/messages', 'view_message', $box, $msgID)?>" class="btn"><?=t('Back to Message.')?></a>
    		
    		<?
    			break;
    		case 'send_complete': ?>
    		
    		<h3><?=t('Message Sent.')?></h3>
    		<a href="<?=$this->url('/account/profile/public', 'view', $recipient->getUserID())?>" class="btn"><?=t('Back to Profile.')?></a>
    		
    		<?
    			break;
			case 'over_limit': ?>
				<h2><?php echo t('Woops!')?></h2>
				<p><?php echo t("You've sent more messages than we can handle just now, that last one didn't go out. 
				We've notified an administrator to check into this. 
				Please wait a few minutes before sending a new message."); ?></p>
				<?php break; 
    		case 'send':
    		case 'reply':
    		case 'write': ?>

			<div id="ccm-profile-message-compose" class="row">
				<div class="span8 offset2">
				<form method="post" action="<?=$this->action('send')?>" class="form-horizontal">
				
				<?=$form->hidden("uID", $recipient->getUserID())?>
				<? if ($this->controller->getTask() == 'reply') { ?>
					<?=$form->hidden("msgID", $msgID)?>
					<?=$form->hidden("box", $box)?>
				<? 
					$subject = t('Re: %s', $text->entities($msgSubject));
				} else {
					$subject = $text->entities($msgSubject);
				}
				?>
				
				<h3><?=t('Send a Private Message')?></h3>
				
				<div class="control-group">
					<label class="control-label"><?=t("To")?></label>
					<div class="controls">
						<input disabled="disabled" type="text" value="<?=$recipient->getUserName()?>" class="span5" />
					</div>
				</div>
				
				<div class="control-group">
					<?=$form->label('subject', t('Subject'))?>
					<div class="controls">
						<?=$form->text('msgSubject', $subject, array('class' => 'span5'))?>
					</div>
				</div>
				
				<div class="control-group">
					<?=$form->label('body', t('Message'))?>
					<div class="controls">
						<?=$form->textarea('msgBody', $msgBody, array('rows'=>8, 'class' => 'span5'))?>
					</div>
				</div>
				
				<div class="well">
					<?=$form->submit('button_submit', t('Send Message'), array('class' => 'pull-right btn-primary'))?>
					<?=$form->submit('button_cancel', t('Cancel'), array('onclick' => 'window.location.href=\'' . $backURL . '\'; return false'))?>
				</div>
				
				<?=$vt->output('validate_send_message');?>
				
				</form>
				</div>
				
			</div>    	    		
    		
    		
    		<? break; 
    		
    		default: 
    			// the inbox and sent box and other controls ?>
    		
    			<table class="table table-striped" border="0" cellspacing="0" cellpadding="0">
    			<tr>
    				<th class="ccm-profile-messages-item-name"><?=t('Mailbox')?></th>
    				<th><?=t('Messages')?></th>
    				<th><?=t('Latest Message')?></th>
    			</tr>
    			<tr>
    				<td class="ccm-profile-messages-item-name"><a href="<?=$this->action('view_mailbox', 'inbox')?>"><?=t('Inbox')?></a></td>
    				<td><?=$inbox->getTotalMessages()?></td>
    				<td class="ccm-profile-mailbox-last-message"><?
    				$msg = $inbox->getLastMessageObject();
    				if (is_object($msg)) {
    					print t('<strong>%s</strong>, sent by %s on %s', $msg->getFormattedMessageSubject(), $msg->getMessageAuthorName(), $msg->getMessageDateAdded('user', t('F d, Y \a\t g:i A')));
    				}
    				?></td>
    			</tr>
    			<tr>
    				<td class="ccm-profile-messages-item-name"><a href="<?=$this->action('view_mailbox', 'sent')?>"><?=t('Sent Messages')?></a></td>
    				<td><?=$sent->getTotalMessages()?></td>
    				<td class="ccm-profile-mailbox-last-message"><?
     				$msg = $sent->getLastMessageObject();
    				if (is_object($msg)) {
    					print t('<strong>%s</strong>, sent by %s on %s', $msg->getFormattedMessageSubject(), $msg->getMessageAuthorName(), $msg->getMessageDateAdded('user', t('F d, Y \a\t g:i A')));
    				}
    				?>
   				</td>
    			</tr>
    			</table>
    		
    		<?
    			break;
    	} ?>
        
        
