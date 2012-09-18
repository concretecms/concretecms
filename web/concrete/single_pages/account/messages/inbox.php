<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="row">
<div class="span10 offset1">

<div class="page-header">
<h1><?=t("Private Messages")?></h1>
</div>

    	<?=$error->output(); ?>
    	<? switch($this->controller->getTask()) { 
    		case 'view_message': ?>

			<?=Loader::helper('concrete/interface')->tabs(array(
				array($this->action('view_mailbox', 'inbox'), t('Inbox'), $box == 'inbox'),
				array($this->action('view_mailbox', 'sent'), t('Sent'), $box == 'sent')
			), false)?>
    		
    		<div id="ccm-private-message-detail">
				<a href="<?=$this->url('/account/profile/public', 'view', $msg->getMessageRelevantUserID())?>"><?=$av->outputUserAvatar($msg->getMessageRelevantUserObject())?></a>
				<a href="<?=$this->url('/account/profile/public', 'view', $msg->getMessageRelevantUserID())?>"><?=$msg->getMessageRelevantUserName()?></a>
				
				<div id="ccm-private-message-actions">
				
				<div class="btn-toolbar">
				
				<div class="btn-group">
				<a href="<?=$backURL?>" class="btn btn-small"><i class="icon-arrow-left"></i> <?=t('Back to Messages')?></a>
				</div>
				
				<div class="btn-group">
				<a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
				<i class="icon-cog"></i> <?=t('Action')?>
				&nbsp;
				<span class="caret"></span>
				</a>
				<ul class="dropdown-menu">
				<? $u = new User(); ?>
				<? if ($msg->getMessageAuthorID() != $u->getUserID()) { ?>
					<? 
					$mui = $msg->getMessageRelevantUserObject();
					if (is_object($mui)) { 
						if ($mui->getUserProfilePrivateMessagesEnabled()) { ?>
							<li><a href="<?=$this->action('reply', $box, $msg->getMessageID())?>"><?=t('Reply')?></a>
							<li class="divider"></li>
						<? } 						
					}?>
				<? } ?>
				<li><a href="javascript:void(0)" onclick="if(confirm('<?=t('Delete this message?')?>')) { window.location.href='<?=$deleteURL?>'}; return false"><?=t('Delete')?></a>
				</ul>
				</div>
				</div>
				
				</div>
				
				<strong><?=$subject?></strong>
				<time><?=$dateAdded?></time>
				<br/><br/>
				
    			<div>
   				<?=$msg->getFormattedMessageBody()?>
   				</div>
			</div>
			    		
    		
    		<? 
    			break;
    		case 'view_mailbox': ?>

			<?=Loader::helper('concrete/interface')->tabs(array(
				array($this->action('view_mailbox', 'inbox'), t('Inbox'), $mailbox == 'inbox'),
				array($this->action('view_mailbox', 'sent'), t('Sent'), $mailbox == 'sent')
			), false)?>
			
    		
			<table class="ccm-profile-messages-list table-striped table" border="0" cellspacing="0" cellpadding="0">
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
						<a href="<?=$this->url('/account/profile/public', 'view', $msg->getMessageRelevantUserID())?>"><?=$av->outputUserAvatar($msg->getMessageRelevantUserObject())?></a>
						<a href="<?=$this->url('/account/profile/public', 'view', $msg->getMessageRelevantUserID())?>"><?=$msg->getMessageRelevantUserName()?></a>
						</td>
						<td class="ccm-profile-messages-item-name"><a href="<?=$this->url('/account/messages/inbox', 'view_message', $mailbox, $msg->getMessageID())?>"><?=$msg->getFormattedMessageSubject()?></a></td>
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
    		
    		<div class="alert alert-success"><?=t('Reply Sent.')?></div>
    		<a href="<?=$this->url('/account/messages/inbox', 'view_message', $box, $msgID)?>" class="btn"><?=t('Back to Message')?></a>
    		
    		<?
    			break;
    		case 'send_complete': ?>
    		
    		<div class="alert alert-success"><?=t('Message Sent.')?></div>
    		<a href="<?=$this->url('/account/profile/public', 'view', $recipient->getUserID())?>" class="btn"><?=t('Back to Profile')?></a>
    		
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
    	

</div>
</div>
        
        
