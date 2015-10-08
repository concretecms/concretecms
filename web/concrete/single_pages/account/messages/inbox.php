<?php defined('C5_EXECUTE') or die("Access Denied.");

$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
?>

<div class="row">
<div class="col-sm-10 col-sm-offset-1">

<div class="page-header">
<h1><?=t("Private Messages")?></h1>
</div>

    	<?php switch($this->controller->getTask()) {
    		case 'view_message': ?>

			<?=Loader::helper('concrete/ui')->tabs(array(
				array($view->action('view_mailbox', 'inbox'), t('Inbox'), $box == 'inbox'),
				array($view->action('view_mailbox', 'sent'), t('Sent'), $box == 'sent')
			), false)?>

    		<div id="ccm-private-message-detail">
			<? if (\Config::get('concrete.user.profiles_enabled')) { ?>
				<a href="<?=$view->url('/members/profile', 'view', $msg->getMessageRelevantUserID())?>"><?=$av->outputUserAvatar($msg->getMessageRelevantUserObject())?></a>
				<a href="<?=$view->url('/members/profile', 'view', $msg->getMessageRelevantUserID())?>"><?=$msg->getMessageRelevantUserName()?></a>
			<? } else { ?>
				<?=$av->outputUserAvatar($msg->getMessageRelevantUserObject())?>
				<?=$msg->getMessageRelevantUserName()?>
			<? } ?>

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
				<?php $u = new User(); ?>
				<?php if ($msg->getMessageAuthorID() != $u->getUserID()) { ?>
					<?
					$mui = $msg->getMessageRelevantUserObject();
					if (is_object($mui)) {
						if ($mui->getUserProfilePrivateMessagesEnabled()) { ?>
							<li><a href="<?=$view->action('reply', $box, $msg->getMessageID())?>"><?=t('Reply')?></a>
							<li class="divider"></li>
						<?php }
					}?>
				<?php } ?>
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

                <a href="<?=URL::to('/account')?>" class="btn btn-default pull-right" /><?=t('Back to Account')?></a>

                <?=Loader::helper('concrete/ui')->tabs(array(
				array($view->action('view_mailbox', 'inbox'), t('Inbox'), $mailbox == 'inbox'),
				array($view->action('view_mailbox', 'sent'), t('Sent'), $mailbox == 'sent')
			), false)?>


			<table class="ccm-profile-messages-list table-striped table" border="0" cellspacing="0" cellpadding="0">
			<thead>
			<tr>
				<th><?php if ($mailbox == 'sent') { ?><?=t('To')?><?php } else { ?><?=t('From')?><?php } ?></th>
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
						<a href="<?=$view->url('/members/profile', 'view', $msg->getMessageRelevantUserID())?>"><?=$av->outputUserAvatar($msg->getMessageRelevantUserObject())?></a>
						<a href="<?=$view->url('/members/profile', 'view', $msg->getMessageRelevantUserID())?>"><?=$msg->getMessageRelevantUserName()?></a>
						</td>
						<td class="ccm-profile-messages-item-name"><a href="<?=$view->url('/account/messages/inbox', 'view_message', $mailbox, $msg->getMessageID())?>"><?=$msg->getFormattedMessageSubject()?></a></td>
						<td style="white-space: nowrap"><?=$dh->formatDateTime($msg->getMessageDateAdded(), true)?></td>
						<td><?=$msg->getMessageStatus()?></td>
					</tr>



				<?php } ?>
			<?php } else { ?>
				<tr>
					<Td colspan="4"><?=t('No messages found.')?></td>
				</tr>
			<?php } ?>
			</tbody>
			</table>


			<?

				$messageList->displayPaging();
    			break;
    		case 'reply_complete': ?>

    		<div class="alert alert-success"><?=t('Reply Sent.')?></div>
    		<a href="<?=$view->url('/account/messages/inbox', 'view_message', $box, $msgID)?>" class="btn btn-default"><?=t('Back to Message')?></a>

    		<?
    			break;
    		case 'send_complete': ?>

    		<div class="alert alert-success"><?=t('Message Sent.')?></div>
    		<a href="<?=$view->url('/members/profile', 'view', $recipient->getUserID())?>" class="btn btn-default"><?=t('Back to Profile')?></a>

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
				<form method="post" action="<?=$view->action('send')?>">

				<?=$form->hidden("uID", $recipient->getUserID())?>
				<?php if ($this->controller->getTask() == 'reply') { ?>
					<?=$form->hidden("msgID", $msgID)?>
					<?=$form->hidden("box", $box)?>
				<?
					$subject = t('Re: %s', $text->entities($msgSubject));
				} else {
					$subject = $text->entities($msgSubject);
				}
				?>

				<h4><?=t('Send a Private Message')?></h4>

				<div class="form-group">
					<label class="control-label"><?=t("To")?></label>
					<input disabled="disabled" class="form-control" type="text" value="<?=$recipient->getUserName()?>" class="span5" />
				</div>

				<div class="form-group">
					<?=$form->label('subject', t('Subject'))?>
					<?=$form->text('msgSubject', $subject, array('class' => 'span5'))?>
				</div>

				<div class="form-group">
					<?=$form->label('body', t('Message'))?>
					<?=$form->textarea('msgBody', $msgBody, array('rows'=>8, 'class' => 'span5'))?>
				</div>

                <?=$form->submit('button_submit', t('Send Message'), array('class' => 'pull-right btn btn-primary'))?>
                <?=$form->submit('button_cancel', t('Cancel'), array('class' => 'btn-default', 'onclick' => 'window.location.href=\'' . $backURL . '\'; return false'))?>

				<?=$valt->output('validate_send_message');?>

				</form>

			</div>


    		<?php break;

    		default:
    			// the inbox and sent box and other controls ?>

    			<table class="table table-striped" border="0" cellspacing="0" cellpadding="0">
    			<tr>
    				<th class="ccm-profile-messages-item-name"><?=t('Mailbox')?></th>
    				<th><?=t('Messages')?></th>
    				<th><?=t('Latest Message')?></th>
    			</tr>
    			<tr>
    				<td class="ccm-profile-messages-item-name"><a href="<?=$view->action('view_mailbox', 'inbox')?>"><?=t('Inbox')?></a></td>
    				<td><?=$inbox->getTotalMessages()?></td>
    				<td class="ccm-profile-mailbox-last-message"><?
    				$msg = $inbox->getLastMessageObject();
    				if (is_object($msg)) {
    					print t('<strong>%s</strong>, sent by %s on %s', $msg->getFormattedMessageSubject(), $msg->getMessageAuthorName(), $dh->formatDateTime($msg->getMessageDateAdded(), true));
    				}
    				?></td>
    			</tr>
    			<tr>
    				<td class="ccm-profile-messages-item-name"><a href="<?=$view->action('view_mailbox', 'sent')?>"><?=t('Sent Messages')?></a></td>
    				<td><?=$sent->getTotalMessages()?></td>
    				<td class="ccm-profile-mailbox-last-message"><?
     				$msg = $sent->getLastMessageObject();
    				if (is_object($msg)) {
    					print t('<strong>%s</strong>, sent by %s on %s', $msg->getFormattedMessageSubject(), $msg->getMessageAuthorName(), $dh->formatDateTime($msg->getMessageDateAdded(), true));
    				}
    				?>
   				</td>
    			</tr>
    			</table>

                <div class="form-actions">
                    <a href="<?=URL::to('/account')?>" class="btn btn-default" /><?=t('Back to Account')?></a>
                </div>

            <?
    			break;
    	} ?>


</div>
</div>
