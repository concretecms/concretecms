<?php

declare(strict_types=1);

namespace Concrete\Tests\Page\Search\Field\Field;

use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Search\Field\Field\VersionAuthorField;
use Concrete\Tests\TestCase;
use Mockery as M;

defined('C5_EXECUTE') or die('Access Denied.');

class VersionAuthorFieldTest extends TestCase
{
    /**
     * @var VersionAuthorField
     */
    private $field;

    /**
     * @before
     */
    public function prepare(): void
    {
        $this->field = new VersionAuthorField();
    }

    /**
     * @after
     */
    public function destroy(): void
    {
        $this->field = null;
    }

    public function testGetKey(): void
    {
        static::assertSame('version_author', $this->field->getKey());
    }

    public function testGetDisplayName(): void
    {
        static::assertNotEmpty($this->field->getDisplayName());
    }

    public function testFilterListDoesNothingWhenNoData(): void
    {
        $list = M::mock(PageList::class);
        $list->shouldNotReceive('filterByVersionAuthorUserID');

        $this->field->filterList($list);
    }

    public function testFilterListDoesNothingWhenZeroUID(): void
    {
        $this->field->loadDataFromRequest(['version_author' => '0']);

        $list = M::mock(PageList::class);
        $list->shouldNotReceive('filterByVersionAuthorUserID');

        $this->field->filterList($list);
    }

    public function testFilterListWithValidUID(): void
    {
        $this->field->loadDataFromRequest(['version_author' => '42']);

        $list = M::mock(PageList::class);
        $list->shouldReceive('filterByVersionAuthorUserID')->with(42)->once();

        $this->field->filterList($list);
    }
}
