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
use Concrete\Core\User\UserInfoRepository;
use Concrete\Core\Utility\Service\Xml;
use SimpleXMLElement;

/**
 * The controller for the conversation block. This block is used to display conversations in a page.
 */
class Controller extends BlockController implements UsesFeatureInterface
{
    /**
     * @var int|string|null
     */
    public $cnvID;

    /**
     * @var int|string|null
     */
    public $enablePosting;

    /**
     * @var bool|int|string|null
     */
    public $paginate;

    /**
     * @var int|string|null
     */
    public $itemsPerPage;

    /**
     * @var string|null
     */
    public $displayMode;

    /**
     * @var string|null
     */
    public $orderBy;

    /**
     * @var bool|int|string|null
     */
    public $enableOrdering;

    /**
     * @var bool|int|string|null
     */
    public $enableCommentRating;

    /**
     * @var bool|int|string|null
     */
    public $enableTopCommentReviews;

    /**
     * @var bool|int|string|null
     */
    public $displaySocialLinks;

    /**
     * @var int|string|null
     */
    public $reviewAggregateAttributeKey;

    /**
     * @var string|null
     */
    public $displayPostingForm;

    /**
     * @var string|null
     */
    public $addMessageLabel;

    /**
     * @var string|null
     */
    public $dateFormat;

    /**
     * @var string|null
     */
    public $customDateFormat;

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
        $conversation = $cnvID ? Conversation::getByID($cnvID) : null;
        if (!$conversation) {
            $conversation = Conversation::add();
            $b = $this->getBlockObject();
            $xc = $b->getBlockCollectionObject();
            $conversation->setConversationPageObject($xc);
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
            if (is_array($post['notificationUsers'] ?? null)) {
                $userInfoRepository = $this->app->make(UserInfoRepository::class);
                foreach ($post['notificationUsers'] as $uID) {
                    $ui = $userInfoRepository->getByID($uID);
                    if ($ui && !isset($users[$ui->getUserID()])) {
                        $users[$ui->getUserID()] = $ui;
                    }
                }
            }
            $users = array_values($users);
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
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::export()
     */
    public function export(SimpleXMLElement $blockNode)
    {
        $xml = $this->app->make(Xml::class);
        parent::export($blockNode);
        $recordNode = $blockNode->data[0]->record[0];
        $conversation = $this->getConversationObject();
        if ($conversation) {
            $recordNode->addChild('attachmentOverridesEnabled', $conversation->getConversationAttachmentOverridesEnabled() ? '1' : '0');
            $recordNode->addChild('attachmentsEnabled', $conversation->getConversationAttachmentsEnabled() ? '1' : '0');
            $recordNode->addChild('maxFilesGuest', (string) $conversation->getConversationMaxFilesGuest());
            $recordNode->addChild('maxFilesRegistered', (string) $conversation->getConversationMaxFilesRegistered());
            $recordNode->addChild('maxFileSizeGuest', (string) $conversation->getConversationMaxFileSizeGuest());
            $recordNode->addChild('maxFileSizeRegistered', (string) $conversation->getConversationMaxFilesRegistered());
            $recordNode->addChild('notificationOverridesEnabled', $conversation->getConversationNotificationOverridesEnabled() ? '1' : '0');
            if ($conversation->getConversationNotificationOverridesEnabled()) {
                foreach ($conversation->getConversationSubscribedUsers() as $ui) {
                    $xml->createChildElement($recordNode, 'notificationUser', $ui->getUserName());
                }
            }
            $recordNode->addChild('subscriptionEnabled', $conversation->getConversationSubscriptionEnabled() ? '1' : '0');
            if ($conversation->getConversationAttachmentOverridesEnabled()) {
                $xml->createChildElement(
                    $recordNode,
                    'fileExtensions',
                    implode(
                        ',',
                        $this->app->make('helper/concrete/file')->unSerializeUploadFileExtensions(
                            $conversation->getConversationFileExtensions()
                        )
                    )
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getImportData()
     */
    protected function getImportData($blockNode, $page)
    {
        $args = parent::getImportData($blockNode, $page);
        unset($args['notificationUser']);
        $notificationUsers = [];
        if (isset($blockNode->data[0]->record[0]->notificationUser)) {
            $userInfoRepository = $this->app->make(UserInfoRepository::class);
            foreach ($blockNode->data[0]->record[0]->notificationUser as $notificationUserNode) {
                if (($uName = trim((string) $notificationUserNode)) !== '') {
                    $ui = $userInfoRepository->getByName($uName);
                    if ($ui) {
                        $notificationUsers[] = $ui->getUserID();
                    }
                }
            }
        }
        $args['notificationUsers'] = $notificationUsers;

        return $args;
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
