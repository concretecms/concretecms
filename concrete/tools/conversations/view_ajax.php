<?php defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Conversation\Message\MessageList as ConversationMessageList;
use Concrete\Core\Conversation\Message\ThreadedList as ConversationMessageThreadedList;

$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();

$cnv = Conversation::getByID(Request::post('cnvID'));
$cID = intval(Request::post('cID'));
if ($cID) {
    $page = Page::getByID($cID);
    $pp = new Permissions($page);
    if ($pp->canViewPage()) {
        $req = Request::getInstance();
        $req->setCurrentPage($page);
        if (is_object($cnv)) {
            $displayForm = true;
            $enableOrdering = (Request::post('enableOrdering') == 1) ? true : false;
            $enablePosting = (Request::post('enablePosting') == 1) ? Conversation::POSTING_ENABLED : Conversation::POSTING_DISABLED_MANUALLY;
            $paginate = (Request::post('paginate') == 1) ? true : false;
            $enableCommentRating = (Request::post('enableCommentRating'));

            $cp = new Permissions($cnv);
            if (!$cp->canAddConversationMessage()) {
                $enablePosting = Conversation::POSTING_DISABLED_PERMISSIONS;
            }

            if (in_array(Request::post('displayMode'), ['flat'])) {
                $displayMode = Request::post('displayMode');
            } else {
                $displayMode = 'threaded';
            }

            $addMessageLabel = t('Add Message');
            if (Request::post('addMessageLabel')) {
                $addMessageLabel = $app->make('helper/security')->sanitizeString(Request::post('addMessageLabel'));
            }

            switch (Request::post('task')) {
                case 'get_messages':
                    $displayForm = false;
                    break;
            }

            switch ($displayMode) {
                case 'flat':
                    $ml = new ConversationMessageList();
                    $ml->filterByConversation($cnv);
                    break;
                default: // threaded
                    $ml = new ConversationMessageThreadedList($cnv);
                    break;
            }

            switch (Request::post('orderBy')) {
                case 'date_desc':
                    $ml->sortByDateDescending();
                    break;
                case 'date_asc':
                    $ml->sortByDateAscending();
                    break;
                case 'rating':
                    $ml->sortByRating();
                    break;
            }

            if ($paginate && $app->make('helper/validation/numbers')->integer(Request::post('itemsPerPage'))) {
                $ml->setItemsPerPage(Request::post('itemsPerPage'));
            } else {
                $ml->setItemsPerPage(-1);
            }

            $bID = (int) Request::post('blockID');
            $block = \Concrete\Core\Block\Block::getByID($bID);
            if (!$block || $block->getBlockActionCollectionID() != $cID) {
                // Our block doesn't seem to be on that page...
                return;
            }

            $checker = new Permissions($block);
            if (!$checker->canViewBlock()) {
                // This user isn't allowed to view the block
                return;
            }

            $checker = new Permissions($block->getBlockCollectionObject());
            if (!$checker->canViewPage()) {
                // This user isn't allowed to view the block's collection
                return;
            }

            if ($block->getBlockTypeHandle() !== 'core_conversation') {
                // We have the wrong block type, how'd that happen?
                return;
            }

            /** @var \Concrete\Block\CoreConversation\Controller $controller */
            $controller = $block->getController();
            $blockConversation = $controller->getConversationObject();

            if (!$blockConversation || $blockConversation->getConversationID() != $cnv->getConversationID()) {
                // Our block doesn't to seem to have the same conversation as was requested
                return;
            }

            $summary = $ml->getSummary();
            $totalPages = $summary->pages;
            $args = [
                'cID' => $cID,
                'bID' => $block->getBlockID(),
                'conversation' => $cnv,
                'messages' => $ml->getPage(),
                'displayMode' => $displayMode,
                'displayForm' => $displayForm,
                'enablePosting' => $enablePosting,
                'addMessageLabel' => $addMessageLabel,
                'currentPage' => 1,
                'totalPages' => $totalPages,
                'orderBy' => Request::post('orderBy'),
                'enableOrdering' => $enableOrdering,
                'enableTopCommentReviews' => (bool) Request::post('enableTopCommentReviews'),
                'displaySocialLinks' => Request::post('displaySocialLinks'),
                'displayPostingForm' => Request::post('displayPostingForm'),
                'enableCommentRating' => Request::post('enableCommentRating'),
                'dateFormat' => Request::post('dateFormat'),
                'customDateFormat' => Request::post('customDateFormat'),
                'blockAreaHandle' => Request::post('blockAreaHandle'),
                'attachmentsEnabled' => Request::post('attachmentsEnabled'),
                'attachmentOverridesEnabled' => (bool) Request::post('attachmentOverridesEnabled'),
            ];
            View::element('conversation/display', $args);
        }
    }
}
