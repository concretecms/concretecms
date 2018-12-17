<?php

namespace Concrete\Controller\Dialog\Page;

use Concrete\Controller\Backend\UserInterface as UserInterfaceController;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Foundation\Queue\QueueService;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Cloner;
use Concrete\Core\Page\ClonerOptions;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Sitemap\DragRequestData;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\User;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Core\Workflow\Request\MovePageRequest as MovePagePageWorkflowRequest;

class DragRequest extends UserInterfaceController
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/dialogs/page/drag_request';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$controllerActionPath
     */
    protected $controllerActionPath = '/ccm/system/dialogs/page/drag_request';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::$validationToken
     */
    protected $validationToken = '/dialogs/page/drag_request';

    public function view()
    {
        $dragRequestData = $this->app->make(DragRequestData::class);
        $originalPageIDs = [];
        foreach ($dragRequestData->getOriginalPages() as $originalPage) {
            $originalPageIDs[] = $originalPage->isAliasPage() ? $originalPage->getCollectionPointerOriginalID() : $originalPage->getCollectionID();
        }
        $this->set('validationToken', $this->app->make('token')->generate($this->validationToken));
        $this->set('dragRequestData', $dragRequestData);
        $this->set('originalPageIDs', implode(',', $originalPageIDs));
        $this->set('formID', 'ccm-drag-request-form-' . $this->app->make(Identifier::class)->getString(32));
    }

    public function submit()
    {
        if (!$this->validateAction()) {
            throw new UserMessageException($this->error->toText());
        }
        $dragRequestData = $this->app->make(DragRequestData::class);
        switch ($this->request->request->get('ctask', $this->request->query->get('ctask'))) {
            case $dragRequestData::OPERATION_ALIAS:
                return $this->doAlias($dragRequestData);
            case $dragRequestData::OPERATION_COPY:
                return $this->doCopy($dragRequestData);
            case $dragRequestData::OPERATION_MOVE:
                return $this->doMove($dragRequestData);
            case $dragRequestData::OPERATION_COPYVERSION:
                return $this->doCopyVersion($dragRequestData);
            default:
                throw new UserMessageException('Invalid parameter: ctask (unrecognized)');
        }
    }

    public function doCopyAll()
    {
        if ($this->request->request->get('process', $this->request->query->get('process'))) {
            return $this->continueCopyAll();
        }
        if (!$this->validateAction()) {
            throw new UserMessageException($this->error->toText());
        }
        $dragRequestData = $this->app->make(DragRequestData::class);
        $error = $dragRequestData->whyCantDo($dragRequestData::OPERATION_COPYALL);
        if ($error !== '') {
            throw new UserMessageException($error);
        }
        $queue = $this->app->make(QueueService::class)->get('copy_page');
        if ($queue->count() == 0) {
            foreach ($dragRequestData->getOriginalPages() as $originalPage) {
                $originalPage->queueForDuplication($dragRequestData->getDestinationPage(), !$dragRequestData->isCopyChildrenOnly());
            }
        }
        $this->set('showProgressBar', true);
        $this->set('totalItems', $queue->count());
    }

    protected function continueCopyAll()
    {
        $isMultilingual = (bool) $this->request->request->get('multilingual', $this->request->query->get('multilingual', false));
        $db = $this->app->make(Connection::class);
        $config = $this->app->make('config');
        $queue = $this->app->make(QueueService::class)->get('copy_page');
        $messages = $queue->receive($config->get('concrete.limits.copy_pages'));
        foreach ($messages as $message) {
            $messageBody = unserialize($message->body);
            $originalPage = Page::getByID($messageBody['cID']);
            // this is the page we're going to copy.
            // now we check to see if the parent ID of the current record has already been duplicated somewhere.
            $destinationPageID = $db->fetchColumn('select cID from QueuePageDuplicationRelations where originalCID = ? and queue_name = ?', [$messageBody['cParentID'], 'copy_page']);
            if ($destinationPageID) {
                $destinationPage = Page::getByID($destinationPageID);
            } else {
                $destinationPage = Page::getByID($messageBody['destination']);
            }
            if ($isMultilingual) {
                // Find multilingual section of the destination
                if (Section::isMultilingualSection($destinationPage)) {
                    $multilingualSection = Section::getByID($destinationPage->getCollectionID());
                } else {
                    $multilingualSection = Section::getBySectionOfSite($destinationPage);
                }
                // Is page already copied?
                $existingCID = Section::getRelatedCollectionIDForLocale($messageBody['cID'], $multilingualSection->getLocale());
                if ($existingCID) {
                    $newPage = Page::getById($existingCID);
                    if ($destinationPage->getCollectionID() != $newPage->getCollectionParentID()) {
                        $newPage->move($destinationPage);
                    }
                } else {
                    $newPage = $originalPage->duplicate($destinationPage);
                }
            } else {
                $newPage = $originalPage->duplicate($destinationPage);
            }
            $originalPageID = $originalPage->getCollectionPointerOriginalID() ?: $originalPage->getCollectionID();
            $newPageID = $newPage->getCollectionPointerOriginalID() ?: $newPage->getCollectionID();
            $db->executeQuery(
                'insert into QueuePageDuplicationRelations (cID, originalCID, queue_name) values (?, ?, ?)',
                [$newPageID, $originalPageID, 'copy_page']
            );
            $queue->deleteMessage($message);
        }
        $totalItems = $queue->count();
        if ($totalItems == 0) {
            $queue->deleteQueue();
            $db->executeQuery('truncate table QueuePageDuplicationRelations');
        }

        return $this->app->make(ResponseFactoryInterface::class)->json(['totalItems' => $totalItems]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    protected function canAccess()
    {
        $pc = new Checker();

        return $pc->canAccessSitemap();
    }

    protected function doAlias(DragRequestData $dragRequestData)
    {
        $error = $dragRequestData->whyCantDo($dragRequestData::OPERATION_ALIAS);
        if ($error !== '') {
            throw new UserMessageException($error);
        }
        $newCIDs = [];
        $successMessages = [];
        $destipationPage = $dragRequestData->getDestinationPage();
        foreach ($dragRequestData->getOriginalPages() as $originalPage) {
            $newCIDs[] = $originalPage->addCollectionAlias($destipationPage);
            $successMessages[] = t('"%1$s" was successfully aliased beneath "%2$s".', $originalPage->getCollectionName(), $destipationPage->getCollectionName());
        }
        $this->setNewPagesDisplayOrder($dragRequestData, $newCIDs);

        return $this->buildOperationCompletedResponse($newCIDs, $successMessages);
    }

    protected function doCopy(DragRequestData $dragRequestData)
    {
        $error = $dragRequestData->whyCantDo($dragRequestData::OPERATION_COPY);
        if ($error !== '') {
            throw new UserMessageException($error);
        }
        $newCIDs = [];
        $successMessages = [];
        $destipationPage = $dragRequestData->getDestinationPage();
        foreach ($dragRequestData->getOriginalPages() as $originalPage) {
            if ($originalPage->isAlias()) {
                $newPageID = $originalPage->addCollectionAlias($destipationPage);
                $newPage = Page::getByID($newPageID);
            } else {
                $newPage = $originalPage->duplicate($destipationPage);
            }
            if (!$newPage || $newPage->isError()) {
                throw new UserMessageException(t('An error occurred while attempting the copy operation.'));
            }
            $newCIDs[] = $newPage->getCollectionID();
            $successMessages[] = t('"%1$s" was successfully copied beneath "%2$s".', $originalPage->getCollectionName(), $destipationPage->getCollectionName());
        }
        $this->setNewPagesDisplayOrder($dragRequestData, $newCIDs);

        return $this->buildOperationCompletedResponse($newCIDs, $successMessages);
    }

    protected function doMove(DragRequestData $dragRequestData)
    {
        $error = $dragRequestData->whyCantDo($dragRequestData::OPERATION_MOVE);
        if ($error !== '') {
            throw new UserMessageException($error);
        }
        $newCIDs = [];
        $successMessages = [];
        $destipationPage = $dragRequestData->getDestinationPage();
        $u = $this->app->make(User::class);
        foreach ($dragRequestData->getOriginalPages() as $originalPage) {
            $pkr = new MovePagePageWorkflowRequest();
            $pkr->setRequestedPage($originalPage);
            $pkr->setRequestedTargetPage($destipationPage);
            $pkr->setSaveOldPagePath($dragRequestData->isSaveOldPagePath());
            $pkr->setRequesterUserID($u->getUserID());
            $u->unloadCollectionEdit($originalPage);
            $r = $pkr->trigger();
            $newCIDs[] = $originalPage->getCollectionID();
            if ($r instanceof \Concrete\Core\Workflow\Progress\Response) {
                $successMessages[] = t('"%1$s" was moved beneath "%2$s".', $originalPage->getCollectionName(), $destipationPage->getCollectionName());
            } else {
                $successMessages[] = t('Your request to move "%1$s" beneath "%2$s" has been stored. Someone with approval rights will have to activate the change.', $originalPage->getCollectionName(), $destipationPage->getCollectionName());
            }
        }
        $this->setNewPagesDisplayOrder($dragRequestData, $newCIDs);

        return $this->buildOperationCompletedResponse($newCIDs, $successMessages);
    }

    /**
     * @param \Concrete\Core\Page\Sitemap\DragRequestData $dragRequestData
     *
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function doCopyVersion(DragRequestData $dragRequestData)
    {
        $error = $dragRequestData->whyCantDo($dragRequestData::OPERATION_COPYVERSION);
        if ($error !== '') {
            throw new UserMessageException($error);
        }
        $originalPage = $dragRequestData->getSingleOriginalPage();
        $originalVersion = $originalPage->getVersionObject();
        $cloner = $this->app->make(Cloner::class);
        $clonerOptions = $this->app->build(ClonerOptions::class)
            ->setForceUnapproved(true)
            ->setVersionComments(t('Contents copied from %s', $originalPage->getCollectionName()))
        ;
        $newVersion = $cloner->cloneCollectionVersion($originalVersion, $dragRequestData->getDestinationPage(), $clonerOptions);

        return $this->buildOperationCompletedResponse(
            [$newVersion->getCollectionID()],
            [t('The contents of "%1$s" has been copied to "%2$s".', $originalPage->getCollectionName(), $dragRequestData->getDestinationPage()->getCollectionName())]
        );
    }

    /**
     * @param DragRequestData $dragRequestData
     * @param int[] $newCIDs
     */
    protected function setNewPagesDisplayOrder(DragRequestData $dragRequestData, array $newCIDs)
    {
        $dragMode = $dragRequestData->getDragMode();
        $destinationSibling = $dragRequestData->getDestinationSibling();
        if ($destinationSibling !== null && in_array($dragMode, ['before', 'after'], true)) {
            foreach ($newCIDs as $newCID) {
                $newPage = Page::getByID($newCID);
                $newPage->movePageDisplayOrderToSibling($destinationSibling, $dragMode);
            }
        }
    }

    /**
     * @param int[] $newCIDs
     * @param string[] $successMessages
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function buildOperationCompletedResponse(array $newCIDs, array $successMessages)
    {
        $rf = $this->app->make(ResponseFactoryInterface::class);

        return $rf->json([
            'error' => false,
            'message' => implode("\n", $successMessages),
            'cID' => $newCIDs,
        ]);
    }
}
