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
        /**
         * @var $objectManager ObjectManager
         */
        $objectManager = $this->app->make(ObjectManager::class);
        $query = $this->request->request->get('query', $this->request->query->get('query'));
        $entryList = $objectManager->getList($requestInstance->getEntityHandle(), true);
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
        $results = [];
        /**
         * @var $objectManager ObjectManager
         */
        $objectManager = $this->app->make(ObjectManager::class);
        foreach ((array) $this->request->request->get('entryId') as $id) {
            $entry = $objectManager->getEntry($id);
            if ($entry) {
                $entity = $entry->getEntity();
                if ($entity instanceof Entity) {
                    $permissions = new Checker($entity);
                    if (!$permissions->canViewExpressEntries()) {
                        throw new \Exception(t('Access Denied.'));
                    } else {
                        $results[] = $requestInstance->createResultFromEntry($entry);
                    }
                } else {
                    throw new \Exception(t('Unable to retrieve entity from Express entry: %s', $entry->getID()));
                }
            }
        }
        return new JsonResponse($results);
    }

}
