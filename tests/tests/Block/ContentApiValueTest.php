<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\TestHelpers\Block\BlockApiValueTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of a block type whose value is derived by the API itself.
 */
class ContentApiValueTest extends BlockApiValueTestCase
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'content';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getSaveData()
     */
    protected function getSaveData(): array
    {
        // that's how the content block stores a reference to a file
        return ['content' => '<p>Some content</p><concrete-picture fID="' . $this->getFile()->getFileID() . '" alt="A sample" />'];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getExpectedApiValue()
     */
    protected function getExpectedApiValue(): array
    {
        // the reference to the file is exchanged as a placeholder
        return ['content' => '<p>Some content</p><concrete-picture alt="A sample" file-id="' . $this->getFile()->getFileUUID() . '" />'];
    }
}
