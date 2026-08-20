<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\Core\Block\Block;
use Concrete\Core\Database\Connection\Connection;
use Concrete\TestHelpers\Block\BlockApiValueTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the survey block.
 *
 * @see \Concrete\Block\Survey\Controller::getApiValueSchema()
 * @see \Concrete\Block\Survey\Controller::serializeValueForApi()
 * @see \Concrete\Block\Survey\Controller::getImportDataFromApiValue()
 */
class SurveyApiValueTest extends BlockApiValueTestCase
{
    public function testTheAnswersAreAddedAndRemoved(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, ['options' => ['Yes', 'Maybe', 'Brand new']]);

        // the answers that are still there keep their position: the new ones are appended
        static::assertSame(
            ['Yes', 'Maybe', 'Brand new'],
            $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['options']
        );
    }

    public function testTheVotesSurviveWhenTheirAnswerIsKept(): void
    {
        $block = $this->addBlock();
        $optionID = $this->getOptionID($block, 'Yes');
        $this->addVote($block, $optionID);

        $this->updateBlock($block, ['question' => 'What do you think now?']);

        static::assertSame(1, $this->countVotes($block));
        // the answer hasn't been recreated, so the votes still refer to it
        static::assertSame($optionID, $this->getOptionID($block, 'Yes'));
    }

    public function testTheVotesAreDeletedWhenTheirAnswerIsRemoved(): void
    {
        $block = $this->addBlock();
        $this->addVote($block, $this->getOptionID($block, 'Yes'));

        $this->updateBlock($block, ['options' => ['No', 'Maybe']]);

        static::assertSame(0, $this->countVotes($block));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'survey';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getSaveData()
     */
    protected function getSaveData(): array
    {
        // the save() method adds the answers whose name is in the pollOption argument
        return [
            'question' => 'What do you think about that?',
            'requiresRegistration' => 1,
            'showResults' => 1,
            'customMessage' => 'Thanks for your vote!',
            'pollOption' => ['Yes', 'No', 'Maybe'],
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
            'question' => 'What do you think about that?',
            'requiresRegistration' => '1',
            'showResults' => '1',
            'customMessage' => 'Thanks for your vote!',
            'options' => ['Yes', 'No', 'Maybe'],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getPartialApiValue()
     */
    protected function getPartialApiValue(): array
    {
        return ['question' => 'What do you think now?'];
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
     * Get the ID of one of the answers of a block.
     */
    private function getOptionID(Block $block, string $optionName): int
    {
        $db = $this->app->make(Connection::class);

        return (int) $db->fetchOne(
            'select optionID from btSurveyOptions where bID = ? and optionName = ?',
            [$block->getBlockID(), $optionName]
        );
    }

    /**
     * Cast a vote for one of the answers of a block.
     */
    private function addVote(Block $block, int $optionID): void
    {
        $this->app->make(Connection::class)->insert('btSurveyResults', [
            'bID' => $block->getBlockID(),
            'cID' => $block->getBlockCollectionObject()->getCollectionID(),
            'optionID' => $optionID,
            'uID' => 0,
            'ipAddress' => '127.0.0.1',
        ]);
    }

    /**
     * Get the number of votes cast for the answers of a block.
     */
    private function countVotes(Block $block): int
    {
        $db = $this->app->make(Connection::class);

        return (int) $db->fetchOne('select count(*) from btSurveyResults where bID = ?', [$block->getBlockID()]);
    }
}
