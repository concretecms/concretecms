<?php
namespace Concrete\Controller\Backend\Express;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Search\Pagination\PaginationFactory;
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
        $vals = $this->app->make('helper/validation/strings');
        $entries = [];
        $entryIDs = [];
        if (isset($_REQUEST['exEntryID'])) {
            if (is_array($_REQUEST['exEntryID'])) {
                $entryIDs = $_REQUEST['exEntryID'];
            } else {
                $entryIDs[] = $_REQUEST['exEntryID'];
            }
        }
        if (count($entryIDs) > 0) {
            $r = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entry');
            foreach ($entryIDs as $entryID) {
                $entry = $r->findOneById($entryID);
                if (is_object($entry)) {
                    $entries[] = $entry;
                }
            }
        } elseif ($_REQUEST['exEntityID'] && $vals->min($_REQUEST['keyword'], 2)) {
            $r = $this->entityManager->getRepository(Entity::class);
            $entity = $r->find($_REQUEST['exEntityID']);
            if (is_object($entity)) {
                $list = new EntryList($entity);
                $list->filterByKeywords($_REQUEST['keyword']);
                $factory = new PaginationFactory($this->getRequest());
                $pagination = $factory->createPaginationObject($list, PaginationFactory::PERMISSIONED_PAGINATION_STYLE_PAGER);
                $entries = $pagination->getCurrentPageResults();
            }
        }

        if (0 == count($entries)) {
            $this->app->make('helper/ajax')->sendError(t('Entries not found.'));
        }

        return $entries;
    }

    public function getJSON()
    {
        $entries = $this->getRequestEntries();
        foreach ($entries as $entry) {
            $ep = new \Permissions($entry->getEntity());
            if (!$ep->canViewExpressEntries()) {
                throw new \Exception(t('Access Denied.'));
            }
        }
        $data = [];
        $data['entries'] = $entries;

        return new JsonResponse($data);
    }
}
