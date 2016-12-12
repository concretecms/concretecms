<?php

namespace Concrete\Controller\Dialog\File;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Entity\Statistics\UsageTracker\FileUsageRecord;
use Doctrine\ORM\EntityManagerInterface;

class Usage extends Controller
{

    protected $viewPath = '/dialogs/file/usage';

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $manager;

    /**
     * Usage constructor.
     * @param \Doctrine\ORM\EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        parent::__construct();
    }

    public function view($fID)
    {
        $records = $this->manager->getRepository(FileUsageRecord::class)->findByFile($fID);
        $reduced = [];

        /** @var FileUsageRecord $record */
        foreach ($records as $record) {
            $cID = $record->getCollectionId();
            if (isset($reduced[$cID])) {
                if ($record->getCollectionVersionId() > $reduced[$cID]->getCollectionVersionId()) {
                    $reduced[$cID] = $record;
                }
            } else {
                $reduced[$cID] = $record;
            }
        }

        $this->set('records', $reduced);
    }

}
