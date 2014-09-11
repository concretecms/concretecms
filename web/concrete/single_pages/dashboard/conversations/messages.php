<?
defined('C5_EXECUTE') or die("Access Denied.");
$valt = Loader::helper('validation/token');
$th = Loader::helper('text');
$ip = Loader::helper('validation/ip'); ?>
<style>
    td.hidden-actions {
        display: none;
    }
</style>
<div class="ccm-dashboard-content-full">

    <div data-search-element="wrapper">
        <form role="form" data-search-form="logs" action="<?=$controller->action('view')?>" class="form-inline ccm-search-fields">
            <div class="ccm-search-fields-row">
                <div class="form-group">
                    <?=$form->label('keywords', t('Search'))?>
                    <div class="ccm-search-field-content">
                        <div class="ccm-search-main-lookup-field">
                            <i class="fa fa-search"></i>
                            <?=$form->search('cmpMessageKeywords', array('placeholder' => t('Keywords')))?>
                            <button type="submit" class="ccm-search-field-hidden-submit" tabindex="-1"><?=t('Search')?></button>
                        </div>
                    </div>
                </div>
            </div>
            <?php /* <div class="ccm-search-fields-row">
                <div class="form-group">
                    <?=$form->label('cmpMessageFilter', t('Filter by Flag'))?>
                    <div class="ccm-search-field-content">
                        <?=$form->select('cmpMessageFilter', array('any'=>t('** Any')), $cmpFilterTypes) ?>
                    </div>
                </div>
            </div>  */ ?>

            <div class="ccm-search-fields-row">
                <div class="form-group form-group-full">
                    <?=$form->label('cmpMessageSort', t('Sort By'))?>
                    <div class="ccm-search-field-content">
                        <?=$form->select('cmpMessageSort', $cmpSortTypes)?>
                        <button class="btn btn-primary" type="submit"><?php echo t('Search') ?></button>
                    </div>
                </div>
            </div>

        </form>

    </div>

    <div data-search-element="results">
        <div class="table-responsive">
            <table class="ccm-search-results-table">
                <thead>
                <tr>
                    <th><span><?=t('Message')?></span></th>
                    <th><span><?=t('Posted')?></span></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php if (count($messages) > 0) {
                    foreach($messages as $msg) {
                        $cnv = $msg->getConversationObject();
                        if(is_object($cnv)) {
                            $page = $cnv->getConversationPageObject();
                        }
                        $msgID = $msg->getConversationMessageID();
                        $cnvID = $cnv->getConversationID();
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
                        $ui = $msg->getConversationMessageUserObject(); ?>
                        <tr>
                            <!-- <td><?=$form->checkbox('cnvMessageID[]', $msg->getConversationMessageID())?></td> -->
                            <td class="message-cell">
                                <div class="ccm-conversation-message-summary">
                                    <div class="message-output">
                                        <?=$msg->getConversationMessageBodyOutput(true)?>
                                    </div>
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
                                <?=$msg->getConversationMessageDateTimeOutput('mdy_full_ts');?>
                                <p><?
                                    if(is_object($ui)) {
                                        echo tc(/*i18n: %s is the name of the author */ 'Authored', 'By %s', $ui->getUserDisplayName());
                                    } else {
                                        echo t(/*i18n: when the author of a message is anonymous */ 'By Anonymous');
                                    }
                                    ?></p>

                                <?

                                if (is_object($page)) { ?>
                                    <div><a href="<?=Loader::helper('navigation')->getLinkToCollection($page)?>"><?=$page->getCollectionPath()?></a></div>
                                <? } ?>
                            </td>
                            <td>
                                <a href="<?=Loader::helper('navigation')->getLinkToCollection($page)?>#cnv<?php echo $cnvID ?>Message<?php echo $msgID ?>" data-open-text="<?php echo t('View full message.') ?>" data-close-text="<?php echo t('Minimize message') ?>" class="read-all truncated btn"><i class="fa fa-share"></i></a>
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
                                            <?php if(!$ip->isBanned($msg->getConversationMessageSubmitIP())) { ?>
                                                <a class = "block-ip" data-rel-message-id="<?php echo $msgID ?>" href="#"><?php echo t('Block user IP Address') ?></a>
                                            <?php } else { ?>
                                                <span class="inactive"><?php echo t('IP Banned') ?></span>
                                            <?php } ?>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <? }
                }?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- END Body Pane -->
    <?=$list->displayPagingV2()?>

</div>
