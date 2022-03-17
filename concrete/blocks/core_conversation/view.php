<?php defined('C5_EXECUTE') or die('Access Denied.');
/** @var \Concrete\Core\Block\View\BlockView $this */
$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
/** @var bool|string $paginate */
$paginate = ($paginate ?? null) ? 'true' : 'false';
/** @var int $itemsPerPage */
$itemsPerPage = ($paginate === 'true') ? $itemsPerPage ?? 50 : -1;
/** @var \Concrete\Core\Block\Block $b */
$blockAreaHandle = $b->getAreaHandle();
/** @var int $maxFileSizeRegistered */
/** @var int $maxFileSizeGuest */
/** @var int $maxFilesRegistered */
/** @var int $maxFilesGuest */
/** @var int $bID */
/** @var int $cID */

$newCollectionID = null;
if ($b->getBlockCollectionID()) {
    // Fix for when using stacks (for sitewide conversations)
    $newCollectionID = $b->getBlockCollectionID();
}

/** @var \Concrete\Core\User\User $u */
$u = app(Concrete\Core\User\User::class);
if ($u->isRegistered()) {
    $maxFileSize = $maxFileSizeRegistered;
    $maxFiles = $maxFilesRegistered;
} else {
    $maxFileSize = $maxFileSizeGuest;
    $maxFiles = $maxFilesGuest;
}

/** @var string $addMessageToken */
/** @var string $editMessageToken */
/** @var string $deleteMessageToken */
/** @var string $flagMessageToken */
/** @var string $displayMode */
/** @var string $addMessageLabel */
/** @var string $orderBy */
/** @var string $enableOrderin */
/** @var string $displayPostingForm */
/** @var int $enableOrdering */
/** @var int $enableCommentRating */
/** @var string $dateFormat */
/** @var string $customDateFormat */
/** @var string $blockAreaHandle */
/** @var string $fileExtensions */

/** @var int $attachmentsEnabled */
/** @var int $attachmentOverridesEnabled */
/** @var int $enableTopCommentReviews */
/** @var int $displaySocialLinks */
/** @var string $users */

/** @var \Concrete\Core\Conversation\Conversation|null $conversation */
if (isset($conversation) && is_object($conversation)) { ?>
    <div class="ccm-conversation-wrapper" data-conversation-id="<?=$conversation->getConversationID()?>">
        <?=t('Loading Conversation')?> <i class="fas fa-spin fa-circle-o-notch"></i>
    </div>

    <script>
    $(function() {
        $('div[data-conversation-id=<?=$conversation->getConversationID()?>]').concreteConversation({
            cnvID: <?=$conversation->getConversationID()?>,
            blockID: <?=$bID?>,
            cID: <?=$newCollectionID ?? $cID?>,
            addMessageToken: '<?=$addMessageToken?>',
            editMessageToken: '<?=$editMessageToken?>',
            deleteMessageToken: '<?=$deleteMessageToken?>',
            flagMessageToken: '<?=$flagMessageToken?>',
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
