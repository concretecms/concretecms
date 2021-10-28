<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\View\View $view
 * @var Concrete\Controller\Frontend\Conversations\ViewAjax $controller
 * @var Concrete\Core\Conversation\Conversation $conversation
 * @var int $cID
 * @var int $bID
 * @var Concrete\Core\Conversation\Message\Message[] $messages
 * @var string $displayMode
 * @var bool $displayForm
 * @var int $enablePosting
 * @var string $addMessageLabel
 * @var int $currentPage
 * @var int $totalPages
 * @var string $orderBy
 * @var bool $enableOrdering
 * @var bool $enableTopCommentReviews
 * @var bool $displaySocialLinks
 * @var string $displayPostingForm
 * @var bool $enableCommentRating
 * @var string $dateFormat
 * @var string $customDateFormat
 * @var string $blockAreaHandle
 * @var bool $attachmentsEnabled
 * @var bool $attachmentOverridesEnabled
 */

View::element(
    'conversation/display',
    compact(
        'cID',
        'bID',
        'conversation',
        'messages',
        'displayMode',
        'displayForm',
        'enablePosting',
        'addMessageLabel',
        'currentPage',
        'totalPages',
        'orderBy',
        'enableOrdering',
        'enableTopCommentReviews',
        'displaySocialLinks',
        'displayPostingForm',
        'enableCommentRating',
        'dateFormat',
        'customDateFormat',
        'blockAreaHandle',
        'attachmentsEnabled',
        'attachmentOverridesEnabled'
    )
);
