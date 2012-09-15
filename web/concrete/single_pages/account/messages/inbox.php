<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div id="ccm-profile-wrapper">
    <? Loader::element('profile/sidebar', array('profile'=> $ui)); ?>    
    <div id="ccm-profile-body">
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
    		
    		<div><a href="<?=$this->url('/profile/messages')?>">&lt;&lt; <?=t('Back to Mailbox List')?></a></div><br/>
    		
			<table class="ccm-profile-messages-list" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<th><? if ($mailbox == 'sent') { ?><?=t('To')?><? } else { ?><?=t('From')?><? } ?></th>
				<th><?=t('Subject')?></th>
				<th><?=t('Sent At')?></th>
				<th><?=t('Status')?></th>
			</tr>
    		
    		
    		
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
			</table>
			
			
			<?

				$messageList->displayPaging();
    			break;
    		case 'reply_complete': ?>
    		
    		<h2><?=t('Reply Sent.')?></h2>
    		<a href="<?=$this->url('/profile/messages', 'view_message', $box, $msgID)?>"><?=t('Return to Message.')?></a>
    		
    		<?
    			break;
    		case 'send_complete': ?>
    		
    		<h2><?=t('Message Sent.')?></h2>
    		<a href="<?=$this->url('/profile', 'view', $recipient->getUserID())?>"><?=t('Return to Profile.')?></a>
    		
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

			<div id="ccm-profile-message-compose">
				<form method="post" action="<?=$this->action('send')?>">
				
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
				
				<h1><?=t('Send a Private Message')?></h1>
				
				<div class="ccm-profile-section">
					<label><?=t('To')?></label>
					<div><?=$recipient->getUserName()?></div>
				</div>
				
				<div class="ccm-profile-detail">
					<div class="ccm-profile-section">
						<?=$form->label('subject', t('Subject'))?>
						<div><?=$form->text('msgSubject', $subject)?></div>
					</div>
					
					<div class="ccm-profile-section-bare">
						<?=$form->label('body', t('Message'))?> <span class="ccm-required">*</span>
						<div><?=$form->textarea('msgBody', $msgBody)?></div>
					</div>
				</div>
				
				<div class="ccm-profile-buttons">
					<?=$form->submit('button_submit', t('Send Message'))?>
					<?=$form->submit('button_cancel', t('Cancel'), array('onclick' => 'window.location.href=\'' . $backURL . '\'; return false'))?>
				</div>
				
				<?=$vt->output('validate_send_message');?>
				
				</form>
				
			</div>    	    		
    		
    		
    		<? break; 
    		
    		default: 
    			// the inbox and sent box and other controls ?>
    		
    			<table class="ccm-profile-messages-list" border="0" cellspacing="0" cellpadding="0">
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
        
        
    </div>
	
	<div class="ccm-spacer"></div>
</div>