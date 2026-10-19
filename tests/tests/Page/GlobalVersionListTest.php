<?php

declare(strict_types=1);

namespace Concrete\Tests\Page;

use Concrete\Core\Page\Collection\Version\GlobalVersionList;
use Concrete\TestHelpers\Page\PageTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

class GlobalVersionListTest extends PageTestCase
{
    protected $pageData = [
        ['Version List Page 1', false],
        ['Version List Page 2', false],
        ['Version List Page 3', false],
    ];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $self = new static('');
        foreach ($self->pageData as $data) {
            call_user_func_array([$self, 'createPage'], $data);
        }
    }

    public function testFilterByVersionAuthorUserIDNoMatch()
    {
        $list = new GlobalVersionList();
        $list->filterByVersionAuthorUserID(99999);
        static::assertEquals(0, $list->getTotalResults());
    }

    public function testFilterByVersionAuthorUserIDMatch()
    {
        // Pages are created in tests with no logged-in user, so cvAuthorUID defaults to 0.
        // We created 3 pages above; assert at least those 3 are returned.
        $filtered = new GlobalVersionList();
        $filtered->filterByVersionAuthorUserID(0);
        static::assertGreaterThanOrEqual(3, $filtered->getTotalResults());
    }
}
