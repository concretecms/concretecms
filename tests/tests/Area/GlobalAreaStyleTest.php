<?php

declare(strict_types=1);

namespace Concrete\Tests\Area;

use Concrete\Core\Area\CustomStyleRepository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Utility\Service\Text;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * The style of a global area is stored in its stack, but the pages render it in the area named after that stack.
 *
 * @see \Concrete\Core\Area\CustomStyleRepository::getStackAreaStyles()
 */
class GlobalAreaStyleTest extends ConcreteDatabaseTestCase
{
    protected $tables = [
        'CollectionVersionAreaStyles',
    ];

    protected $entityClassNames = [
        'Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet',
    ];

    public function testTheStyleOfAGlobalAreaTargetsTheNameOfItsStack(): void
    {
        $styleSet = new StyleSet();
        $styleSet->setBackgroundColor('#aaaaaa');
        $styleSet->save();
        $this->giveTheStackAreaAStyle(101, 5, (int) $styleSet->getID());
        $repository = new CustomStyleRepository(app(Connection::class), app(Text::class));

        $styles = $repository->getStackAreaStyles($this->createStack(101, 5, 'Footer Col 1'), $this->createMock(Theme::class));

        static::assertCount(1, $styles);
        static::assertSame('ccm-custom-style-footercol1', $styles[0]->getCustomStyleClass());
        static::assertSame('.ccm-custom-style-footercol1{background-color:#aaaaaa}', $styles[0]->getCSS());
    }

    private function giveTheStackAreaAStyle(int $collectionID, int $versionID, int $styleSetID): void
    {
        app(Connection::class)->insert('CollectionVersionAreaStyles', [
            'cID' => $collectionID,
            'cvID' => $versionID,
            'arHandle' => STACKS_AREA_NAME,
            'issID' => $styleSetID,
        ]);
    }

    private function createStack(int $collectionID, int $versionID, string $stackName): Stack
    {
        $stack = $this->createMock(Stack::class);
        $stack->method('getCollectionID')->willReturn($collectionID);
        $stack->method('getVersionID')->willReturn($versionID);
        $stack->method('getStackName')->willReturn($stackName);

        return $stack;
    }
}
