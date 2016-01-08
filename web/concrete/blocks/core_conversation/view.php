<?php defined('C5_EXECUTE') or die("Access Denied.");
$paginate = ($paginate) ? 'true' : 'false';
$itemsPerPage = ($paginate) ? $itemsPerPage : -1;
$blockAreaHandle = $this->block->getAreaHandle();
/** @var \Concrete\Core\Permission\IPService $iph */
$iph = Core::make('helper/validation/ip');
$commentRatingIP = $iph->getRequestIP()->getIp();
$u = new User();
if ($u->isLoggedIn()) {
    $uID = $u->getUserID();
    $maxFileSize = $maxFileSizeRegistered;
    $maxFiles = $maxFilesRegistered;
}else{
    $maxFileSize = $maxFileSizeGuest;
    $maxFiles = $maxFilesGuest;
    $uID = 0;
}

if (is_object($conversation)) {
    ?>
    <div class="ccm-conversation-wrapper" data-conversation-id="<?=$conversation->getConversationID()?>">
    <?=t('Loading Conversation')?> <i class="fa fa-spin fa-circle-o-notch"></i>
    </div>

    <script type="text/javascript">
    $(function() {
        $('div[data-conversation-id=<?=$conversation->getConversationID()?>]').concreteConversation({
            cnvID: <?=$conversation->getConversationID()?>,
            blockID: <?=$bID?>,
            cID: <?=$cID?>,
            posttoken: '<?=$posttoken?>',
            displayMode: '<?=$displayMode?>',
            addMessageLabel: '<?=$addMessageLabel?>',
            paginate: <?=$paginate?>,
            itemsPerPage: <?=$itemsPerPage?>,
            orderBy: '<?=$orderBy?>',
            enableOrdering: <?=$enableOrdering?>,
            displayPostingForm: '<?=$displayPostingForm?>',
            activeUsers: <?=Loader::helper('json')->encode($users)?>,
            enableCommentRating: <?=$enableCommentRating?>,
            commentRatingUserID: <?=$uID?>,
            commentRatingIP: '<?=$commentRatingIP?>',
            dateFormat: '<?=$dateFormat?>',
            customDateFormat: '<?=$customDateFormat?>',
            blockAreaHandle: '<?=$blockAreaHandle ?>',
            fileExtensions: '<?=$fileExtensions?>',
            maxFileSize: '<?=$maxFileSize?>',
            maxFiles: '<?=$maxFiles?>',
            attachmentsEnabled: '<?=$attachmentsEnabled?>',
            attachmentOverridesEnabled: '<?=$attachmentOverridesEnabled?>'
        });
    });
    </script>
<?php } ?>