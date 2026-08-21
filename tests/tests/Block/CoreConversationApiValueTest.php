<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\Block\CoreConversation\Controller as CoreConversationController;
use Concrete\Core\Block\Block;
use Concrete\Core\Entity\User\User as UserEntity;
use Concrete\TestHelpers\Block\BlockApiValueTestCase;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the conversation block.
 *
 * @see \Concrete\Block\CoreConversation\Controller::getApiValueSchema()
 * @see \Concrete\Block\CoreConversation\Controller::serializeValueForApi()
 * @see \Concrete\Block\CoreConversation\Controller::getImportDataFromApiValue()
 */
class CoreConversationApiValueTest extends BlockApiValueTestCase
{
    /**
     * The user to be notified when a message is added.
     *
     * @var \Concrete\Core\Entity\User\User|null
     */
    private $user;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getTables()
     */
    protected function getTables()
    {
        return array_merge(parent::getTables(), [
            'Conversations',
            'ConversationSubscriptions',
        ]);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->user = null;
    }

    public function testTheNotifiedUsersAreExchangedByTheirID(): void
    {
        $block = $this->addBlock();

        static::assertSame([$this->getUserID()], $this->getApiValue($block)['notificationUsers']);
    }

    public function testTheNotifiedUsersCanBeCleared(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, ['notificationUsers' => []]);

        static::assertSame(
            [],
            $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['notificationUsers']
        );
    }

    public function testTheAttachmentLimitsAreKeptApart(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, ['maxFilesRegistered' => '4', 'maxFileSizeRegistered' => '40']);

        $conversation = $this->getConversation($this->getBlock($block->getBlockCollectionObject()));
        static::assertSame(4, (int) $conversation->getConversationMaxFilesRegistered());
        static::assertSame(40, (int) $conversation->getConversationMaxFileSizeRegistered());
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'core_conversation';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getSaveData()
     */
    protected function getSaveData(): array
    {
        // that's what the form of the block sends
        return [
            'enablePosting' => 1,
            'displayMode' => 'flat',
            'displayPostingForm' => 'bottom',
            'addMessageLabel' => 'Say something',
            'paginate' => 1,
            'itemsPerPage' => 20,
            'orderBy' => 'rating',
            'enableOrdering' => 1,
            'enableCommentRating' => 1,
            'enableTopCommentReviews' => 0,
            'displaySocialLinks' => 1,
            'dateFormat' => 'custom',
            'customDateFormat' => 'Y',
            'attachmentOverridesEnabled' => 1,
            'attachmentsEnabled' => 1,
            'maxFilesGuest' => 3,
            'maxFilesRegistered' => 10,
            'maxFileSizeGuest' => 1,
            'maxFileSizeRegistered' => 20,
            'fileExtensions' => 'jpg,png',
            'notificationOverridesEnabled' => 1,
            'notificationUsers' => [$this->getUserID()],
            'subscriptionEnabled' => 1,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getExpectedApiValue()
     */
    protected function getExpectedApiValue(): array
    {
        return [
            'enablePosting' => '1',
            'paginate' => '1',
            'itemsPerPage' => '20',
            'displayMode' => 'flat',
            'orderBy' => 'rating',
            'enableOrdering' => '1',
            'enableCommentRating' => '1',
            'enableTopCommentReviews' => '0',
            'displaySocialLinks' => '1',
            'reviewAggregateAttributeKey' => null,
            'displayPostingForm' => 'bottom',
            'addMessageLabel' => 'Say something',
            'dateFormat' => 'custom',
            'customDateFormat' => 'Y',
            'attachmentOverridesEnabled' => '1',
            'attachmentsEnabled' => '1',
            'maxFilesGuest' => '3',
            'maxFilesRegistered' => '10',
            'maxFileSizeGuest' => '1',
            'maxFileSizeRegistered' => '20',
            'notificationOverridesEnabled' => '1',
            'subscriptionEnabled' => '1',
            'fileExtensions' => 'jpg,png',
            'notificationUsers' => [$this->getUserID()],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getPartialApiValue()
     */
    protected function getPartialApiValue(): array
    {
        return ['addMessageLabel' => 'Tell us what you think'];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::hasCustomApiValue()
     */
    protected function hasCustomApiValue(): bool
    {
        return true;
    }

    /**
     * Get the conversation a block owns.
     *
     * @return \Concrete\Core\Conversation\Conversation
     */
    private function getConversation(Block $block)
    {
        $controller = $block->getController();
        static::assertInstanceOf(CoreConversationController::class, $controller);
        $conversation = $controller->getConversationObject();
        static::assertNotNull($conversation);

        return $conversation;
    }

    /**
     * Get the ID of the user to be notified (they are created the first time they are asked for).
     */
    private function getUserID(): int
    {
        if ($this->user === null) {
            $entityManager = $this->app->make(EntityManagerInterface::class);
            // the tests of a class share the tables: the user may have been created by another one
            $user = $entityManager->getRepository(UserEntity::class)->findOneBy(['uName' => 'john_doe']);
            if ($user !== null) {
                $this->user = $user;

                return (int) $user->getUserID();
            }
            $user = new UserEntity();
            $user->setUserName('john_doe');
            $user->setUserEmail('john_doe@example.com');
            $user->setUserPassword('');
            $user->setUserDateAdded(new \DateTime());
            $user->setUserLastPasswordChange(new \DateTime());
            $entityManager->persist($user);
            $entityManager->flush();
            $this->user = $user;
        }

        return (int) $this->user->getUserID();
    }
}
