<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Conversation\Message\MessageList as ConversationMessageList;
use Concrete\Core\Conversation\Message\ThreadedList as ConversationMessageThreadedList;

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

            if (in_array(Request::post('displayMode'), array('flat'))) {
                $displayMode = Request::post('displayMode');
            } else {
                $displayMode = 'threaded';
            }

            $addMessageLabel = t('Add Message');
            if (Request::post('addMessageLabel')) {
                $addMessageLabel = Core::make('helper/security')->sanitizeString(Request::post('addMessageLabel'));
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

            if ($paginate && Core::make('helper/validation/numbers')->integer(Request::post('itemsPerPage'))) {
                $ml->setItemsPerPage(Request::post('itemsPerPage'));
            } else {
                $ml->setItemsPerPage(-1);
            }

            $summary = $ml->getSummary();
            $totalPages = $summary->pages;
            $args = array(
                'cID' => $cID,
                'bID' => intval(Request::post('blockID')),
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
                'enableTopCommentReviews' => !!Request::post('enableTopCommentReviews'),
                'displayPostingForm' => Request::post('displayPostingForm'),
                'enableCommentRating' => Request::post('enableCommentRating'),
                'dateFormat' => Request::post('dateFormat'),
                'customDateFormat' => Request::post('customDateFormat'),
                'blockAreaHandle' => Request::post('blockAreaHandle'),
                'attachmentsEnabled' => Request::post('attachmentsEnabled'),
                'attachmentOverridesEnabled' => !!Request::post('attachmentOverridesEnabled'),
            );
            View::element('conversation/display', $args);
        }
    }
}
