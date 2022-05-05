<?php

namespace Concrete\Controller\Dialog\Page;

use Concrete\Controller\Backend\UserInterface as UserInterfaceController;
use Concrete\Core\Command\Batch\Batch;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Cloner;
use Concrete\Core\Page\ClonerOptions;
use Concrete\Core\Page\Command\ClearPageCopyCommandBatch;
use Concrete\Core\Page\Command\CopyPageCommand;
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
        $isMultilingual = (bool) $this->request->request->get('multilingual', $this->request->query->get('multilingual', false));
        if (!$this->validateAction()) {
            throw new UserMessageException($this->error->toText());
        }
        $dragRequestData = $this->app->make(DragRequestData::class);
        $error = $dragRequestData->whyCantDo($dragRequestData::OPERATION_COPYALL);
        if ($error !== '') {
            throw new UserMessageException($error);
        }
        foreach ($dragRequestData->getOriginalPages() as $oc) {
            $pages = [];
            $pages = $oc->populateRecursivePages($pages, ['cID' => $oc->getCollectionID()], $oc->getCollectionParentID(), 0, !$dragRequestData->isCopyChildrenOnly());
            usort($pages, ['\Concrete\Core\Page\Page', 'queueForDuplicationSort']);
            $copyBatchID = uuid_create();
            $batch = Batch::create(t('Copy Pages'));
            foreach ($pages as $page) {
                $batch->add(new CopyPageCommand($page['cID'], $copyBatchID, $dragRequestData->getDestinationPage()->getCollectionID(), $isMultilingual));
            }
            $batch->add(new ClearPageCopyCommandBatch($copyBatchID));
            return $this->dispatchBatch($batch);
        }
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
