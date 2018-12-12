<?php

namespace Concrete\Tests\Page\Collection\Version;

use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Application;
use Concrete\TestHelpers\Page\PageTestCase;

class ApproveTest extends PageTestCase
{
    /**
     * @var \Concrete\Core\Localization\Service\Date
     */
    protected $dateHelper;

    /**
     * @var int
     */
    protected $numCollectionVersions = 3;

    /**
     * @var \Concrete\Core\Page\Page
     */
    private $page;

    /**
     * @var int[]
     */
    private $cvIDs;

    public function setUp()
    {
        parent::setUp();
        $this->dateHelper = Application::getFacadeApplication()->make('date');
        $this->page = Page::getByID(Page::getHomePageID());
        $this->cvIDs = [];
        for ($i = 0; $i < $this->numCollectionVersions; ++$i) {
            $this->page->loadVersionObject($i + 1);
            if ($this->page->getVersionID() === null) {
                $this->page->loadVersionObject('RECENT');
                $cv = $this->page->cloneVersion('')->getVersionObject();
            } else {
                $cv = $this->page->getVersionObject();
            }
            $this->cvIDs[] = $cv->getVersionID();
        }
        $this->resetVersions();
    }

    public function testWithoutStartEndDate()
    {
        $this->getVersion(0)->approve(false);
        $this->assertTrue((bool) $this->getVersion(0)->isApproved());
        $this->assertFalse((bool) $this->getVersion(1)->isApproved());
        $this->assertFalse((bool) $this->getVersion(2)->isApproved());
        $this->getVersion(1)->approve(false);
        $this->assertFalse((bool) $this->getVersion(0)->isApproved());
        $this->assertTrue((bool) $this->getVersion(1)->isApproved());
        $this->assertFalse((bool) $this->getVersion(2)->isApproved());
    }

    public function testStartDate()
    {
        $now = strtotime('2018-10-23 12:00:00');
        $startDate = $this->dateHelper->toDB($now);
        $startDatePlus1 = $this->dateHelper->toDB($now + 1);
        $startDatePlus2 = $this->dateHelper->toDB($now + 2);

        $this->getVersion(0)->approve(false, $startDate);
        $this->getVersion(1)->setPublishInterval($startDate, null);

        $this->assertTrue((bool) $this->getVersion(0)->isApproved());
        $this->assertFalse((bool) $this->getVersion(1)->isApproved());
        $this->assertSame($startDate, $this->getVersion(0)->getPublishDate());
        $this->assertSame($startDate, $this->getVersion(1)->getPublishDate());

        $this->getVersion(1)->approve(false, $startDate, null);

        $this->assertFalse((bool) $this->getVersion(0)->isApproved());
        $this->assertTrue((bool) $this->getVersion(1)->isApproved());
        $this->assertSame($startDate, $this->getVersion(0)->getPublishDate());
        $this->assertSame($startDate, $this->getVersion(1)->getPublishDate());

        $this->getVersion(2)->approve(false, $startDatePlus2, null);
        $this->assertTrue((bool) $this->getVersion(1)->isApproved());
        $this->assertTrue((bool) $this->getVersion(2)->isApproved());
        $this->assertSame($startDate, $this->getVersion(1)->getPublishDate());
        $this->assertSame($startDatePlus1, $this->getVersion(1)->getPublishEndDate());
        $this->assertSame($startDatePlus2, $this->getVersion(2)->getPublishDate());
    }

    public function testEndDate()
    {
        $now = strtotime('2018-10-23 12:00:00');
        $endDate = $this->dateHelper->toDB($now);
        $endDateMinus1 = $this->dateHelper->toDB($now - 1);
        $endDateMinus2 = $this->dateHelper->toDB($now - 2);

        $this->getVersion(0)->approve(false, null, $endDate);
        $this->getVersion(1)->setPublishInterval(null, $endDate);

        $this->assertTrue((bool) $this->getVersion(0)->isApproved());
        $this->assertFalse((bool) $this->getVersion(1)->isApproved());
        $this->assertSame($endDate, $this->getVersion(0)->getPublishEndDate());
        $this->assertSame($endDate, $this->getVersion(1)->getPublishEndDate());

        $this->getVersion(1)->approve(false, null, $endDate);

        $this->assertFalse((bool) $this->getVersion(0)->isApproved());
        $this->assertTrue((bool) $this->getVersion(1)->isApproved());
        $this->assertSame($endDate, $this->getVersion(0)->getPublishEndDate());
        $this->assertSame($endDate, $this->getVersion(1)->getPublishEndDate());

        $this->getVersion(2)->approve(false, null, $endDateMinus2);
        $this->assertTrue((bool) $this->getVersion(1)->isApproved());
        $this->assertTrue((bool) $this->getVersion(2)->isApproved());
        $this->assertSame($endDateMinus1, $this->getVersion(1)->getPublishDate());
        $this->assertSame($endDate, $this->getVersion(1)->getPublishEndDate());
        $this->assertSame($endDateMinus2, $this->getVersion(2)->getPublishEndDate());
    }

    public function testStartEndDate()
    {
        $now = strtotime('2018-10-23 12:00:00');
        $startDate = $this->dateHelper->toDB($now - 10);
        $endDate = $this->dateHelper->toDB($now + 10);
        $endDatePlus1 = $this->dateHelper->toDB($now + 10 + 1);

        $this->getVersion(0)->approve(false);
        $this->getVersion(1)->approve(false, $startDate, $endDate);

        $this->assertTrue((bool) $this->getVersion(0)->isApproved());
        $this->assertSame($endDatePlus1, $this->getVersion(0)->getPublishDate());
        $this->assertNull($this->getVersion(0)->getPublishEndDate());

        $this->resetVersions();
        $this->getVersion(0)->approve(false, $startDate, $endDate);
        $this->getVersion(1)->approve(false, $startDate, $endDate);
        $this->assertTrue((bool) $this->getVersion(1)->isApproved());
        $this->assertFalse((bool) $this->getVersion(0)->isApproved());
    }

    /**
     * @param int $index
     *
     * @return \Concrete\Core\Page\Collection\Version\Version
     */
    protected function getVersion($index)
    {
        $this->page->loadVersionObject($this->cvIDs[$index]);

        return $this->page->getVersionObject();
    }

    protected function resetVersions()
    {
        foreach (array_keys($this->cvIDs) as $index) {
            $cv = $this->getVersion($index);
            $cv->deny();
            $cv->setPublishInterval(null, null);
        }
    }
}
