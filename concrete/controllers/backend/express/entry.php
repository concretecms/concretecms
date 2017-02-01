<?php
namespace Concrete\Controller\Backend\Express;

use Concrete\Core\Controller\AbstractController;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;

class Entry extends AbstractController
{

    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function getRequestEntries()
    {
        $entries = array();
        if (is_array($_REQUEST['exEntryID'])) {
            $entryIDs = $_REQUEST['exEntryID'];
        } else {
            $entryIDs[] = $_REQUEST['exEntryID'];
        }
        $r = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entry');
        foreach ($entryIDs as $entryID) {
            $entry = $r->findOneById($entryID);
            if (is_object($entry)) {
                $entries[] = $entry;
            }
        }

        if (count($entries) == 0) {
            $this->app->make('helper/ajax')->sendError(t('Entries not found.'));
        }

        return $entries;
    }


    public function getJSON()
    {
        $entries = $this->getRequestEntries();
        foreach($entries as $entry) {
            $ep = new \Permissions($entry->getEntity());
            if (!$ep->canViewExpressEntries()) {
                throw new \Exception(t('Access Denied.'));
            }
        }
        $data = array();
        $data['entries'] = $entries;
        return new JsonResponse($data);
    }
}
