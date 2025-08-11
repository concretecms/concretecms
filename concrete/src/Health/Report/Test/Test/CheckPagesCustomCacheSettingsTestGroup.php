<?php
namespace Concrete\Core\Health\Report\Test\Test;

use Concrete\Core\Health\Report\Test\TestGroupInterface;
use Doctrine\DBAL\Connection;

class CheckPagesCustomCacheSettingsTestGroup implements TestGroupInterface
{

    /**
     * @var Connection
     */
    protected $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function getTests(): iterable
    {
        $r = $this->db->executeQuery('select cID from Pages where cCacheFullPageContent <> -1 and cIsActive = 1 and cIsDraft = 0 and cIsSystemPage = 0');
        while ($row = $r->fetchAssociative()) {
            $object = new CheckPageCustomCacheSettingsTest();
            $object->setPageId($row['cID']);
            yield $object;
        }
    }

}
