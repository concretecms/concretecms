<?php

declare(strict_types=1);

namespace Concrete\Tests\Page\Type\Composer\Control;

use Concrete\Core\Page\Type\Composer\Control\CollectionAttributeControl;
use PHPUnit\Framework\TestCase;

defined('C5_EXECUTE') or die('Access Denied.');

class CollectionAttributeControlTest extends TestCase
{
    public function testSerializedControlsCanStillResolveTheirIdentifier(): void
    {
        $control = new CollectionAttributeControl();
        $control->setAttributeKeyID(123);

        $reloaded = unserialize(serialize($control));

        $this->assertInstanceOf(CollectionAttributeControl::class, $reloaded);
        $this->assertSame(123, $reloaded->getAttributeKeyID());
        $this->assertSame(123, $reloaded->getPageTypeComposerControlIdentifier());
    }
}
