<?php

declare(strict_types=1);

namespace Concrete\Tests\Block\Api;

use Concrete\TestHelpers\Block\BlockApiValueTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the feature link block.
 *
 * The value of the block is the record of its table, which is what the API exposes anyway: only its schema
 * is written by the controller.
 *
 * @see \Concrete\Block\FeatureLink\Controller::getApiValueSchema()
 * @see \Concrete\Block\FeatureLink\Controller::getImportDataFromApiValue()
 */
class FeatureLinkValueTest extends BlockApiValueTestCase
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'feature_link';
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
            'icon' => 'fas fa-address-card',
            'fID' => $this->getFile()->getFileID(),
            'title' => 'Link Title',
            'titleFormat' => 'h3',
            'body' => '<p>Why you should follow the link</p>',
            'buttonText' => 'Follow me',
            'imageLink__which' => 'file',
            'imageLink_file' => $this->getFile()->getFileID(),
            'buttonSize' => 'lg',
            'buttonStyle' => 'outline',
            'buttonColor' => 'light',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getExpectedApiValue()
     */
    protected function getExpectedApiValue(): array
    {
        $fileReference = '{ccm:export:file::id=' . $this->getFile()->getFileUUID() . '}';

        // the keys are in the order of the columns of the btFeatureLink table
        return [
            'title' => 'Link Title',
            'body' => '<p>Why you should follow the link</p>',
            'buttonText' => 'Follow me',
            'buttonExternalLink' => '',
            'buttonInternalLinkCID' => '0',
            'buttonFileLinkID' => $fileReference,
            'buttonColor' => 'light',
            'buttonStyle' => 'outline',
            'buttonSize' => 'lg',
            'titleFormat' => 'h3',
            'icon' => 'fas fa-address-card',
            'fID' => $fileReference,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getPartialApiValue()
     */
    protected function getPartialApiValue(): array
    {
        return ['buttonText' => 'Click here'];
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
