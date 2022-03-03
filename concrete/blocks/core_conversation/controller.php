<?php

namespace Concrete\Block\CoreConversation;

use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Conversation\Conversation;
use Concrete\Core\Conversation\Message\MessageList;
use Concrete\Core\Entity\Attribute\Key\PageKey;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\User\UserInfo;

/**
 * The controller for the conversation block. This block is used to display conversations in a page.
 */
class Controller extends BlockController implements UsesFeatureInterface
{
    /**
     * @var int|null
     */
    public $enableTopCommentReviews;

    /**
     * @var string|null
     */
    public $reviewAggregateAttributeKey;

    /**
     * @var int|null
     */
    public $maxFilesRegistered;

    /**
     * @var int|null
     */
    public $maxFilesGuest;

    /**
     * @var bool|int
     */
    public $enablePosting;

    /**
     * @var int
     */
    protected $btInterfaceWidth = 450;

    /**
     * @var int
     */
    protected $btInterfaceHeight = 400;

    /**
     * @var bool
     */
    protected $btCacheBlockRecord = true;

    /**
     * @var string
     */
    protected $btTable = 'btCoreConversation';

    /**
     * @var Conversation|null
     */
    protected $conversation;

    /**
     * @var string
     */
    protected $btWrapperClass = 'ccm-ui';

    /**
     * @var bool
     */
    protected $btCopyWhenPropagate = true;

    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Displays conversations on a page.');
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Conversation');
    }

    /**
     * @return string[]
     */
    public function getRequiredFeatures(): array
    {
        return [
            Features::CONVERSATIONS,
        ];
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return string
     */
    public function getSearchableContent()
    {
        $ml = new MessageList();
        $ml->filterByConversation($this->getConversationObject());
        $messages = $ml->get();
        if (!count($messages)) {
            return '';
        }

        $content = '';
        foreach ($messages as $message) {
            $content .= $message->getConversationMessageSubject() . ' ' .
                       strip_tags($message->getConversationMessageBody()) . ' ';
        }

        return rtrim($content);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return Conversation
     */
    public function getConversationObject()
    {
        if (!isset($this->conversation)) {
            // i don't know why this->cnvid isn't sticky in some cases, leading us to query
            // every damn time
            $db = $this->app->make('database');
            $cnvID = $db->fetchColumn('select cnvID from btCoreConversation where bID = ?', [$this->bID]);
            $this->conversation = Conversation::getByID($cnvID);
        }

        return $this->conversation;
    }

    /**
     * @param int $newBID
     * @param Page $newPage
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function duplicate_master($newBID, $newPage)
    {
        $this->duplicate($newBID);
        $db = $this->app->make('database');
        $conv = Conversation::add();
        $conv->setConversationPageObject($newPage);
        $this->conversation = $conv;
        $db->executeQuery('update btCoreConversation set cnvID = ? where bID = ?', [$conv->getConversationID(), $newBID]);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function edit()
    {
        $keys = $this->getReviewAttributeKeys();
        $this->set('reviewAttributeKeys', iterator_to_array($keys));

        $fileSettings = $this->getFileSettings();
        $this->set('maxFilesGuest', $fileSettings['maxFilesGuest']);
        $this->set('maxFilesRegistered', $fileSettings['maxFilesRegistered']);
        $this->set('maxFileSizeGuest', $fileSettings['maxFileSizeGuest']);
        $this->set('maxFileSizeRegistered', $fileSettings['maxFileSizeRegistered']);
        $this->set('fileExtensions', $fileSettings['fileExtensions']);
        $this->set('attachmentsEnabled', $fileSettings['attachmentsEnabled'] > 0 ? $fileSettings['attachmentsEnabled'] : '');
        $this->set('attachmentOverridesEnabled', $fileSettings['attachmentOverridesEnabled'] > 0 ? $fileSettings['attachmentOverridesEnabled'] : '');

        $conversation = $this->getConversationObject();
        $this->set('notificationOverridesEnabled', $conversation->getConversationNotificationOverridesEnabled());
        $this->set('subscriptionEnabled', $conversation->getConversationSubscriptionEnabled());
        $this->set('notificationUsers', $conversation->getConversationSubscribedUsers());
    }

    /**
     * @param string $outputContent
     *
     * @return void
     */
    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset('core/conversation');
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function view()
    {
        if ($this->enableTopCommentReviews) {
            $this->requireAsset('javascript', 'jquery/awesome-rating');
            $this->requireAsset('css', 'jquery/awesome-rating');
        }
        $fileSettings = $this->getFileSettings();
        $conversation = $this->getConversationObject();
        if (is_object($conversation)) {
            $tokenHelper = $this->app->make('token');
            $this->set('conversation', $conversation);
            if ($this->enablePosting) {
                $addMessageToken = $tokenHelper->generate('add_conversation_message');
            } else {
                $addMessageToken = '';
            }
            $this->set('addMessageToken', $addMessageToken);
            $this->set('editMessageToken', $tokenHelper->generate('edit_conversation_message'));
            $this->set('deleteMessageToken', $tokenHelper->generate('delete_conversation_message'));
            $this->set('flagMessageToken', $tokenHelper->generate('flag_conversation_message'));
            $this->set('cID', Page::getCurrentPage()->getCollectionID());
            $this->set('users', $this->getActiveUsers(true));
            $this->set('maxFilesGuest', $fileSettings['maxFilesGuest']);
            $this->set('maxFilesRegistered', $fileSettings['maxFilesRegistered']);
            $this->set('maxFileSizeGuest', $fileSettings['maxFileSizeGuest']);
            $this->set('maxFileSizeRegistered', $fileSettings['maxFileSizeRegistered']);
            $this->set('fileExtensions', $fileSettings['fileExtensions']);
            $this->set('attachmentsEnabled', $fileSettings['attachmentsEnabled']);
            $this->set('attachmentOverridesEnabled', $fileSettings['attachmentOverridesEnabled']);
        }
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return array<string,mixed>
     */
    public function getFileSettings()
    {
        $conversation = $this->getConversationObject();
        $helperFile = $this->app->make('helper/concrete/file');
        $maxFilesGuest = $conversation->getConversationMaxFilesGuest();
        $attachmentOverridesEnabled = $conversation->getConversationAttachmentOverridesEnabled();
        $maxFilesRegistered = $conversation->getConversationMaxFilesRegistered();
        $maxFileSizeGuest = $conversation->getConversationMaxFileSizeGuest();
        $maxFileSizeRegistered = $conversation->getConversationMaxFileSizeRegistered();
        $fileExtensions = $conversation->getConversationFileExtensions();
        $attachmentsEnabled = $conversation->getConversationAttachmentsEnabled();

        $fileExtensions = implode(',', $helperFile->unserializeUploadFileExtensions($fileExtensions)); //unserialize and implode extensions into comma separated string

        $fileSettings = [];
        $fileSettings['maxFileSizeRegistered'] = $maxFileSizeRegistered;
        $fileSettings['maxFileSizeGuest'] = $maxFileSizeGuest;
        $fileSettings['maxFilesGuest'] = $maxFilesGuest;
        $fileSettings['maxFilesRegistered'] = $maxFilesRegistered;
        $fileSettings['fileExtensions'] = $fileExtensions;
        $fileSettings['attachmentsEnabled'] = $attachmentsEnabled;
        $fileSettings['attachmentOverridesEnabled'] = $attachmentOverridesEnabled;

        return $fileSettings;
    }

    /**
     * @param bool $lower
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return string[]
     */
    public function getActiveUsers($lower = false)
    {
        $cnv = $this->getConversationObject();
        $uobs = $cnv->getConversationMessageUsers();
        $users = [];
        foreach ($uobs as $user) {
            if ($lower) {
                $users[] = strtolower($user->getUserName());
            } else {
                $users[] = $user->getUserName();
            }
        }

        return $users;
    }

    /**
     * @param array<string, mixed> $post
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function save($post)
    {
        $helperFile = $this->app->make('helper/concrete/file');
        $db = $this->app->make('database');
        $cnvID = $db->fetchColumn('select cnvID from btCoreConversation where bID = ?', [$this->bID]);
        if (!$cnvID) {
            $conversation = Conversation::add();
            $b = $this->getBlockObject();
            $xc = $b->getBlockCollectionObject();
            $conversation->setConversationPageObject($xc);
        } else {
            $conversation = Conversation::getByID($cnvID);
        }
        $values = $post + [
            'attachmentOverridesEnabled' => null,
            'attachmentsEnabled' => null,
            'itemsPerPage' => null,
            'maxFilesGuest' => null,
            'maxFilesRegistered' => null,
            'maxFileSizeGuest' => null,
            'maxFileSizeRegistered' => null,
            'enableOrdering' => null,
            'enableCommentRating' => null,
            'displaySocialLinks' => null,
            'enableTopCommentReviews' => null,
            'notificationOverridesEnabled' => null,
            'subscriptionEnabled' => null,
            'fileExtensions' => null,
        ];
        if ($values['attachmentOverridesEnabled']) {
            $conversation->setConversationAttachmentOverridesEnabled((int) ($values['attachmentOverridesEnabled']));
            $conversation->setConversationAttachmentsEnabled($values['attachmentsEnabled'] ? 1 : 0);
        } else {
            $conversation->setConversationAttachmentOverridesEnabled(0);
        }
        if (!$values['itemsPerPage']) {
            $values['itemsPerPage'] = 0;
        }
        if ($values['maxFilesGuest']) {
            $conversation->setConversationMaxFilesGuest((int) ($values['maxFilesGuest']));
        }
        if ($values['maxFilesRegistered']) {
            $conversation->setConversationMaxFilesRegistered((int) ($values['maxFilesRegistered']));
        }
        if ($values['maxFileSizeGuest']) {
            $conversation->setConversationMaxFileSizeGuest((int) ($values['maxFileSizeGuest']));
        }
        if ($values['maxFileSizeRegistered']) {
            $conversation->setConversationMaxFilesRegistered((int) ($values['maxFileSizeRegistered']));
        }
        if (!$values['enableOrdering']) {
            $values['enableOrdering'] = 0;
        }
        if (!$values['enableCommentRating']) {
            $values['enableCommentRating'] = 0;
        }
        if (!$values['enableTopCommentReviews']) {
            $values['enableTopCommentReviews'] = 0;
        }
        if (!$values['displaySocialLinks']) {
            $values['displaySocialLinks'] = 0;
        }

        if ($values['notificationOverridesEnabled']) {
            $conversation->setConversationNotificationOverridesEnabled(true);
            $users = [];
            if (is_array($this->post('notificationUsers'))) {
                foreach ($this->post('notificationUsers') as $uID) {
                    $ui = UserInfo::getByID($uID);
                    if (is_object($ui)) {
                        $users[] = $ui;
                    }
                }
            }
            $conversation->setConversationSubscribedUsers($users);
            $conversation->setConversationSubscriptionEnabled((int) ($values['subscriptionEnabled']));
        } else {
            $conversation->setConversationNotificationOverridesEnabled(false);
            $conversation->setConversationSubscriptionEnabled(0);
        }

        if ($values['fileExtensions']) {
            $receivedExtensions = preg_split('{,}', strtolower($values['fileExtensions']), -1, PREG_SPLIT_NO_EMPTY);
            $fileExtensions = $helperFile->serializeUploadFileExtensions($receivedExtensions);
            $conversation->setConversationFileExtensions($fileExtensions);
        }

        $values['cnvID'] = $conversation->getConversationID();
        parent::save($values);
    }

    /**
     * @return \Generator<string|int, string>
     */
    private function getReviewAttributeKeys()
    {
        $category = $this->app->make(PageCategory::class);
        $keys = $category->getAttributeKeyRepository()->findAll();

        /** @var PageKey $key */
        foreach ($keys as $key) {
            if ($key->getAttributeType()->getAttributeTypeHandle() === 'rating') {
                yield $key->getAttributeKeyID() => $key->getAttributeKeyDisplayName();
            }
        }
    }
}
