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
<div class="ccm-conversation-block">
    <div id="conversation-block-dropdown" class="ccm-conversation-block-dropdown d-flex justify-content-end d-md-none">
        <button class="btn btn-nav ccm-conversation-block-toggle text-end" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="dropdown-dot"></span>
            <span class="dropdown-dot"></span>
            <span class="dropdown-dot"></span>
        </button>
        <ul class="dropdown-menu ccm-conversation-block-dropdown-menu ">
            <li>
                <button class="btn btn-outline-dim like-page" type="button" data-bs-toggle="" data-bs-target="#conversationSidebar" aria-controls="conversationSidebar">
                    <i class="fas fa-heart"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                        25
                        <span class="visually-hidden">likes</span>
                    </span>
                </button>
            </li>
            <li>
                <button class="btn btn-outline-dim offcanvas-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#conversationSidebar" aria-controls="conversationSidebar">
                    <i class="fas fa-comment-alt"></i><span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                        5
                        <span class="visually-hidden">unread messages</span>
                    </span>
                </button>
            </li>
            <li>
                <button class="btn btn-outline-dim add-page" type="button" data-bs-toggle="" data-bs-target="#conversationSidebar" aria-controls="conversationSidebar">
                    <i class="fas fa-plus"></i>
                </button>
            </li>
        </ul>
        <script>
            $("#conversation-block-dropdown").appendTo("#page-title-byline-author");
        </script>
    </div>
    
    <div class="d-none d-md-block">
        <button class="btn btn-outline-dim like-page" type="button" data-bs-toggle="" data-bs-target="#conversationSidebar" aria-controls="conversationSidebar">
            <i class="fas fa-heart"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                25
                <span class="visually-hidden">likes</span>
            </span>
        </button>
        <button class="btn btn-outline-dim offcanvas-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#conversationSidebar" aria-controls="conversationSidebar">
            <i class="fas fa-comment-alt"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                5
                <span class="visually-hidden">unread messages</span>
            </span>
        </button>
        <button class="btn btn-outline-dim add-page" type="button" data-bs-toggle="" data-bs-target="#conversationSidebar" aria-controls="conversationSidebar">
            <i class="fas fa-plus"></i>
        </button>
    </div>  
<div class="ccm-conversation-wrapper offcanvas offcanvas-end" data-conversation-id="<?=$conversation->getConversationID()?>" data-bs-scroll="true" tabindex="-1" id="conversationSidebar" aria-labelledby="conversationSidebar">
        <?=t('Loading Conversation')?> <i class="fas fa-spin fa-circle-o-notch"></i>
    </div>
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
