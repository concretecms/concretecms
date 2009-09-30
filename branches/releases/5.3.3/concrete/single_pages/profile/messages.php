<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div id="ccm-profile-wrapper">
    <?php  Loader::element('profile/sidebar', array('profile'=> $ui)); ?>    
    <div id="ccm-profile-body">
    	<?php echo $error->output(); ?>
    	<?php  switch($this->controller->getTask()) { 
    		case 'view_message': ?>

    		<div><a href="<?php echo $this->url('/profile/messages', 'view_mailbox', $box)?>">&lt;&lt; <?php echo t('Back to Mailbox')?></a></div><br/>
    		
    		<h1><?php echo t('Message Details')?></h1>
    		<form method="post" action="<?php echo $this->action('reply', $box, $msg->getMessageID())?>">
    		<div class="ccm-profile-detail">
				<div class="ccm-profile-section">
					<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top" class="ccm-profile-message-from"><a href="<?php echo $this->url('/profile', 'view', $msg->getMessageRelevantUserID())?>"><?php echo $av->outputUserAvatar($msg->getMessageRelevantUserObject())?></a>
						<a href="<?php echo $this->url('/profile', 'view', $msg->getMessageRelevantUserID())?>"><?php echo $msg->getMessageRelevantUserName()?></a>

						</td>
						<td valign="top">
							<h2><?php echo $subject?></h2>
							<div><?php echo $dateAdded?></div>
						</td>
					</tr>
					</table>
					
    			</div>
    			
   				<?php echo $msg->getFormattedMessageBody()?>
    		</div>
			<div class="ccm-profile-buttons">
				<?php  if ($msg->getMessageAuthorID() != $ui->getUserID()) { ?>
					<?php  
					$mui = $msg->getMessageRelevantUserObject();
					if (is_object($mui)) { 
						if ($mui->getUserProfilePrivateMessagesEnabled()) { ?>
							<?php echo $form->submit('button_submit', t('Reply'))?>
						<?php  } 
						
					}?>
				<?php  } ?>
				<?php echo $form->submit('button_delete', t('Delete'), array('onclick' => 'if(confirm(\'' . t('Delete this message?') . '\')) { window.location.href=\'' . $deleteURL . '\'}; return false'))?>
				<?php echo $form->submit('button_cancel', t('Back'), array('onclick' => 'window.location.href=\'' . $backURL . '\'; return false'))?>
			</div>
			</form>
			    		
    		
    		<?php  
    			break;
    		case 'view_mailbox': ?>
    		
    		<div><a href="<?php echo $this->url('/profile/messages')?>">&lt;&lt; <?php echo t('Back to Mailbox List')?></a></div><br/>
    		
			<table class="ccm-profile-messages-list" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<th><?php  if ($mailbox == 'sent') { ?><?php echo t('To')?><?php  } else { ?><?php echo t('From')?><?php  } ?></th>
				<th><?php echo t('Subject')?></th>
				<th><?php echo t('Sent At')?></th>
				<th><?php echo t('Status')?></th>
			</tr>
    		
    		
    		
    		<?php 
    			if (is_array($messages)) { 
					foreach($messages as $msg) { ?>
					
					<tr>
						<td class="ccm-profile-message-from">
						<a href="<?php echo $this->url('/profile', 'view', $msg->getMessageRelevantUserID())?>"><?php echo $av->outputUserAvatar($msg->getMessageRelevantUserObject())?></a>
						<a href="<?php echo $this->url('/profile', 'view', $msg->getMessageRelevantUserID())?>"><?php echo $msg->getMessageRelevantUserName()?></a>
						</td>
						<td class="ccm-profile-messages-item-name"><a href="<?php echo $this->url('/profile/messages', 'view_message', $mailbox, $msg->getMessageID())?>"><?php echo $msg->getMessageSubject()?></a></td>
						<td style="white-space: nowrap"><?php echo $msg->getMessageDateAdded('user', t('F d, Y \a\t g:i A'))?></td>
						<td><?php echo $msg->getMessageStatus()?></td>
					</tr>
					
					
			
				<?php  } ?>
			<?php  } else { ?>
				<tr>
					<Td colspan="4"><?php echo t('No messages found.')?></td>
				</tr>
			<?php  } ?>
			</table>
			
			
			<?php 

				$messageList->displayPaging();
    			break;
    		case 'reply_complete': ?>
    		
    		<h2><?php echo t('Reply Sent.')?></h2>
    		<a href="<?php echo $this->url('/profile/messages', 'view_message', $box, $msgID)?>"><?php echo t('Return to Message.')?></a>
    		
    		<?php 
    			break;
    		case 'send_complete': ?>
    		
    		<h2><?php echo t('Message Sent.')?></h2>
    		<a href="<?php echo $this->url('/profile', 'view', $recipient->getUserID())?>"><?php echo t('Return to Profile.')?></a>
    		
    		<?php 
    			break;
    		case 'send':
    		case 'reply':
    		case 'write': ?>

			<div id="ccm-profile-message-compose">
				<form method="post" action="<?php echo $this->action('send')?>">
				
				<?php echo $form->hidden("uID", $recipient->getUserID())?>
				<?php  if ($this->controller->getTask() == 'reply') { ?>
					<?php echo $form->hidden("msgID", $msgID)?>
					<?php echo $form->hidden("box", $box)?>
				<?php  
					$subject = t('Re: %s', $msgSubject);
				} else {
					$subject = $msgSubject;
				}
				?>
				
				<h1><?php echo t('Send a Private Message')?></h1>
				
				<div class="ccm-profile-section">
					<label><?php echo t('To')?></label>
					<div><?php echo $recipient->getUserName()?></div>
				</div>
				
				<div class="ccm-profile-detail">
					<div class="ccm-profile-section">
						<?php echo $form->label('subject', t('Subject'))?>
						<div><?php echo $form->text('msgSubject', $subject)?></div>
					</div>
					
					<div class="ccm-profile-section-bare">
						<?php echo $form->label('body', t('Message'))?> <span class="ccm-required">*</span>
						<div><?php echo $form->textarea('msgBody', $msgBody)?></div>
					</div>
				</div>
				
				<div class="ccm-profile-buttons">
					<?php echo $form->submit('button_submit', t('Send Message'))?>
					<?php echo $form->submit('button_cancel', t('Cancel'), array('onclick' => 'window.location.href=\'' . $backURL . '\'; return false'))?>
				</div>
				
				<?php echo $vt->output('validate_send_message');?>
				
				</form>
				
			</div>    	    		
    		
    		
    		<?php  break; 
    		
    		default: 
    			// the inbox and sent box and other controls ?>
    		
    			<table class="ccm-profile-messages-list" border="0" cellspacing="0" cellpadding="0">
    			<tr>
    				<th class="ccm-profile-messages-item-name"><?php echo t('Mailbox')?></th>
    				<th><?php echo t('Messages')?></th>
    				<th><?php echo t('Latest Message')?></th>
    			</tr>
    			<tr>
    				<td class="ccm-profile-messages-item-name"><a href="<?php echo $this->action('view_mailbox', 'inbox')?>"><?php echo t('Inbox')?></a></td>
    				<td><?php echo $inbox->getTotalMessages()?></td>
    				<td class="ccm-profile-mailbox-last-message"><?php 
    				$msg = $inbox->getLastMessageObject();
    				if (is_object($msg)) {
    					print t('<strong>%s</strong>, sent by %s on %s', $msg->getMessageSubject(), $msg->getMessageAuthorName(), $msg->getMessageDateAdded('user', t('F d, Y \a\t g:i A')));
    				}
    				?></td>
    			</tr>
    			<tr>
    				<td class="ccm-profile-messages-item-name"><a href="<?php echo $this->action('view_mailbox', 'sent')?>"><?php echo t('Sent Messages')?></a></td>
    				<td><?php echo $sent->getTotalMessages()?></td>
    				<td class="ccm-profile-mailbox-last-message"><?php 
     				$msg = $sent->getLastMessageObject();
    				if (is_object($msg)) {
    					print t('<strong>%s</strong>, sent by %s on %s', $msg->getMessageSubject(), $msg->getMessageAuthorName(), $msg->getMessageDateAdded('user', t('F d, Y \a\t g:i A')));
    				}
    				?>
   				</td>
    			</tr>
    			</table>
    		
    		<?php 
    			break;
    	} ?>
        
        
    </div>
	
	<div class="ccm-spacer"></div>
</div>