<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\TestHelpers\Block\BlockApiValueTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the hero image block.
 *
 * The value of the block is the record of its table, which is what the API exposes anyway: only its schema
 * is written by the controller.
 *
 * @see \Concrete\Block\HeroImage\Controller::getApiValueSchema()
 * @see \Concrete\Block\HeroImage\Controller::getImportDataFromApiValue()
 */
class HeroImageApiValueTest extends BlockApiValueTestCase
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'hero_image';
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
            'image' => $this->getFile()->getFileID(),
            'height' => 60,
            'icon' => 'fas fa-air-freshener',
            'title' => 'This is the title',
            'titleFormat' => 'h2',
            'body' => '<p>What you are looking at</p>',
            'buttonText' => 'This is the Large Button Text',
            'imageLink__which' => 'external_url',
            'imageLink_external_url' => 'https://www.example.com',
            'buttonSize' => 'lg',
            'buttonStyle' => 'outline',
            'buttonColor' => 'primary',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getExpectedApiValue()
     */
    protected function getExpectedApiValue(): array
    {
        // the keys are in the order of the columns of the btHeroImage table
        return [
            'image' => '{ccm:export:file::id=' . $this->getFile()->getFileUUID() . '}',
            'title' => 'This is the title',
            'body' => '<p>What you are looking at</p>',
            'buttonText' => 'This is the Large Button Text',
            'buttonExternalLink' => 'https://www.example.com',
            'buttonInternalLinkCID' => '0',
            'buttonFileLinkID' => '0',
            'height' => '60',
            'buttonColor' => 'primary',
            'buttonStyle' => 'outline',
            'buttonSize' => 'lg',
            'titleFormat' => 'h2',
            'icon' => 'fas fa-air-freshener',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getPartialApiValue()
     */
    protected function getPartialApiValue(): array
    {
        return ['title' => 'Another title'];
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
