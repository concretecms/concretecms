<?php defined('C5_EXECUTE') or die('Access Denied.');

$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();

$paginate = ($paginate) ? 'true' : 'false';
$itemsPerPage = ($paginate) ? $itemsPerPage : -1;
$blockAreaHandle = $this->block->getAreaHandle();

$u = new User();
if ($u->isRegistered()) {
    $maxFileSize = $maxFileSizeRegistered;
    $maxFiles = $maxFilesRegistered;
} else {
    $maxFileSize = $maxFileSizeGuest;
    $maxFiles = $maxFilesGuest;
}

if (is_object($conversation)) { ?>
    <div class="ccm-conversation-wrapper" data-conversation-id="<?=$conversation->getConversationID()?>">
        <?=t('Loading Conversation')?> <i class="fa fa-spin fa-circle-o-notch"></i>
    </div>

    <script>
    $(function() {
        $('div[data-conversation-id=<?=$conversation->getConversationID()?>]').concreteConversation({
            cnvID: <?=$conversation->getConversationID()?>,
            blockID: <?=$bID?>,
            cID: <?=$cID?>,
            addMessageToken: '<?=$addMessageToken?>',
            editMessageToken: '<?=$editMessageToken?>',
            deleteMessageToken: '<?=$deleteMessageToken?>',
            displayMode: '<?=$displayMode?>',
            addMessageLabel: '<?=$addMessageLabel?>',
            paginate: <?=$paginate?>,
            itemsPerPage: <?=$itemsPerPage?>,
            orderBy: '<?=$orderBy?>',
            enableOrdering: <?=$enableOrdering?>,
            displayPostingForm: '<?=$displayPostingForm?>',
            activeUsers: <?=$app->make('helper/json')->encode($users)?>,
            enableCommentRating: <?=$enableCommentRating?>,
            dateFormat: '<?=$dateFormat?>',
            customDateFormat: '<?=$customDateFormat?>',
            blockAreaHandle: '<?=$blockAreaHandle ?>',
            fileExtensions: '<?=$fileExtensions?>',
            maxFileSize: '<?=$maxFileSize?>',
            maxFiles: '<?=$maxFiles?>',
            attachmentsEnabled: '<?=$attachmentsEnabled?>',
            attachmentOverridesEnabled: '<?=$attachmentOverridesEnabled?>',
            enableTopCommentReviews: <?= json_encode($enableTopCommentReviews) ?>,
            displaySocialLinks: <?=$displaySocialLinks?>
        });
    });
    </script>
<?php
}
