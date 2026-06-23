<?php

namespace Concrete\Controller\Backend\Express\Entry;

use Concrete\Controller\Backend\Page;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Express\Component\ExpressEntrySelectInstance;
use Concrete\Core\Express\Component\ExpressEntrySelectInstanceFactory;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Component\PageSelectInstance;
use Concrete\Core\Page\Component\PageSelectInstanceFactory;
use Concrete\Core\Page\PageList;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Search\Pagination\PaginationFactory;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Concrete\Core\Page\Page as CorePage;

defined('C5_EXECUTE') or die('Access Denied.');

class Autocomplete extends AbstractController
{
    public function checkAccess(): ExpressEntrySelectInstance
    {
        $instanceFactory = $this->app->make(ExpressEntrySelectInstanceFactory::class);
        $requestInstance = $instanceFactory->createInstanceFromRequest($this->request);

        if (!$instanceFactory->instanceMatchesAccessToken($requestInstance, $this->request->request->get('accessToken') ?? '')) {
            throw new UserMessageException($this->app->make('token')->getErrorMessage());
        }

        return $requestInstance;
    }

    public function view(): Response
    {
        $requestInstance = $this->checkAccess();
        $entity = $this->getAccessibleEntity($requestInstance);
        /**
         * @var $objectManager ObjectManager
         */
        $objectManager = $this->app->make(ObjectManager::class);
        $query = $this->request->request->get('query', $this->request->query->get('query'));
        $entryList = $objectManager->getList($entity->getHandle(), true);
        $entryList->filterByKeywords($query);
        $factory = new PaginationFactory($this->request);
        $pagination = $factory->createPaginationObject($entryList);
        $results = [];
        foreach ($pagination->getCurrentPageResults() as $entry) {
            $results[] = $requestInstance->createResultFromEntry($entry);
        }

        return new JsonResponse($results);
    }

    public function getSelectedEntries(): JsonResponse
    {
        $requestInstance = $this->checkAccess();
        $entity = $this->getAccessibleEntity($requestInstance);
        $results = [];
        /**
         * @var $objectManager ObjectManager
         */
        $objectManager = $this->app->make(ObjectManager::class);
        foreach ((array) $this->request->request->get('entryId') as $id) {
            $entry = $objectManager->getEntry($id);
            if ($entry) {
                $entryEntity = $entry->getEntity();
                if (!$entryEntity instanceof Entity) {
                    throw new \Exception(t('Unable to retrieve entity from Express entry: %s', $entry->getID()));
                }
                if ((int) $entryEntity->getID() !== (int) $entity->getID()) {
                    throw new \Exception(t('Access Denied.'));
                }
                $results[] = $requestInstance->createResultFromEntry($entry);
            }
        }
        return new JsonResponse($results);
    }

    protected function getAccessibleEntity(ExpressEntrySelectInstance $requestInstance): Entity
    {
        $objectManager = $this->app->make(ObjectManager::class);
        $entity = $objectManager->getObjectByHandle($requestInstance->getEntityHandle());
        if (!$entity instanceof Entity) {
            throw new \Exception(t('Access Denied.'));
        }

        $permissions = new Checker($entity);
        if (!$permissions->canViewExpressEntries()) {
            throw new \Exception(t('Access Denied.'));
        }

        return $entity;
    }

}
