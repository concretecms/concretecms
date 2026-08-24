<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\TestHelpers\Block\BlockApiValueTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the feature block.
 *
 * The value of the block is the record of its table, which is what the API exposes anyway: only its schema
 * is written by the controller.
 *
 * @see \Concrete\Block\Feature\Controller::getApiValueSchema()
 */
class FeatureApiValueTest extends BlockApiValueTestCase
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'feature';
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
            'icon' => 'fas fa-address-book',
            'fID' => $this->getFile()->getFileID(),
            'title' => 'The feature',
            'titleFormat' => 'h3',
            'paragraph' => '<p>What it does</p>',
            'link__which' => 'external_url',
            'link_external_url' => 'https://www.example.com',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getExpectedApiValue()
     */
    protected function getExpectedApiValue(): array
    {
        // the keys are in the order of the columns of the btFeature table
        return [
            'icon' => 'fas fa-address-book',
            'title' => 'The feature',
            'paragraph' => '<p>What it does</p>',
            'externalLink' => 'https://www.example.com',
            'internalLinkCID' => '0',
            'titleFormat' => 'h3',
            'fID' => '{ccm:export:file::id=' . $this->getFile()->getFileUUID() . '}',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getPartialApiValue()
     */
    protected function getPartialApiValue(): array
    {
        return ['title' => 'Another feature'];
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
}
