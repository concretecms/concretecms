<?php
namespace Concrete\Controller\Dialog\Type;

use Block;
use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Foundation\Queue\QueueService;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\PageList;
use Page;
use PageTemplate;
use PageType;
use Permissions;
use View;

class UpdateFromType extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/type/update_from_type';

    public function on_start()
    {
        parent::on_start();

        $request = $this->request;
        $ptID = $request->get('ptID');

        if (!$ptID) {
            throw new \Exception(t('Invalid request data.'));
        }

        $this->pagetype = PageType::getByID($ptID);

        if (!is_object($this->pagetype)) {
            throw new \Exception('Invalid page type');
        }

        $permissionsClass = $this->app->make(Permissions::class);
        $this->permissions = new $permissionsClass($this->pagetype);
    }

    public function view($ptID, $pTemplateID)
    {
        $this->fetchTypeAndTemplate($ptID, $pTemplateID);

        $pl = new PageList();
        $pl->filterByPageTypeID($this->pagetype->getPageTypeID());
        $pl->filterByPageTemplate($this->template);
        $pl->ignorePermissions();

        $this->set('total', $pl->getTotalResults());
    }

    protected function queueForPageTypeUpdate($pageTypeDefaultPage, $queue)
    {
        $records = [];

        $pageTypeDefaultPageBlocks = [];
        $pageTypeDefaultPageRelBlocks = [];

        foreach ($pageTypeDefaultPage->getBlocks() as $b) {
            $pageTypeDefaultPageBlocks[$b->getBlockID()] = $pageTypeDefaultPageRelBlocks[$b->getBlockRelationID()] = $b;
        }

        $db = $this->app->make(Connection::class);
        $site = $this->app->make('site')->getSite();

        $siteTreeID = $site->getSiteTreeID();

        $ptID = $pageTypeDefaultPage->getPageTypeID();
        $pagesPlusCV = $db->fetchAll('select p.cID, max(cvID) as cvID from Pages p inner join CollectionVersions cv on p.cID = cv.cID where ptID = ? and cIsTemplate = 0 and cIsActive = 1 and siteTreeID = ? group by cID order by cID', [$ptID, $siteTreeID]);

        foreach ($pagesPlusCV as $pagePlusCV) {
            $pageTypeDefaultPageBlocksClone = $pageTypeDefaultPageBlocks;

            $asociatedBlocks = $db->fetchAll(
                'select cbDisplayOrder, arHandle, bID, cbRelationID from CollectionVersionBlocks where cID = ? and cvID = ?',
                [$pagePlusCV['cID'], $pagePlusCV['cvID']]
            );

            $blocksToUpdate = [];
            $blocksToAdd = [];

            foreach ($asociatedBlocks as $asociatedBlock) {
                $bID = $asociatedBlock['bID'];
                $cbRelationID = $asociatedBlock['cbRelationID'];

                $blockToUpdate = [
                    'bID' => $bID,
                    'arHandle' => $asociatedBlock['arHandle'],
                    'actions' => [],
                ];

                $blockPageTypeDefaultPage = null;

                if (array_key_exists($bID, $pageTypeDefaultPageBlocks)) {
                    $blockPageTypeDefaultPage = $pageTypeDefaultPageBlocks[$bID];
                } elseif (array_key_exists($cbRelationID, $pageTypeDefaultPageRelBlocks)) {
                    $blockPageTypeDefaultPage = $pageTypeDefaultPageRelBlocks[$cbRelationID];
                    $blockToUpdate['actions'][] = [
                        'name' => 'update_forked',
                        'pageTypeBlockID' => $blockPageTypeDefaultPage->getBlockID(),
                        'pageTypeArHandle' => $blockPageTypeDefaultPage->getAreaHandle(),
                    ];
                } else {
                    $blockToUpdate['actions'][] = ['name' => 'delete'];
                }

                if ($blockPageTypeDefaultPage) {
                    $forceChangeOrder = false;

                    if ($asociatedBlock['arHandle'] != $blockPageTypeDefaultPage->getAreaHandle()) {
                        $blockToUpdate['actions'][] = [
                            'name' => 'change_arHandle',
                            'actualArHandle' => $blockPageTypeDefaultPage->getAreaHandle(),
                        ];

                        $forceChangeOrder = true;
                    }

                    if ($forceChangeOrder || ($asociatedBlock['cbDisplayOrder'] != $blockPageTypeDefaultPage->cbDisplayOrder)) {
                        $blockToUpdate['actions'][] = [
                            'name' => 'change_display_order',
                            'actualDisplayOrder' => $blockPageTypeDefaultPage->cbDisplayOrder,
                            'actualArHandle' => $blockPageTypeDefaultPage->getAreaHandle(),
                        ];
                    }
                }

                unset($pageTypeDefaultPageBlocksClone[$bID]);

                if (!empty($blockToUpdate['actions'])) {
                    $blocksToUpdate[] = $blockToUpdate;
                }
            }

            if (count($pageTypeDefaultPageBlocksClone) > 0) {
                foreach ($pageTypeDefaultPageBlocksClone as $pageTypeBlock) {
                    $blocksToAdd[] = [
                        'bID' => $pageTypeBlock->getBlockID(),
                        'actualDisplayOrder' => $pageTypeBlock->cbDisplayOrder,
                        'pageTypeArHandle' => $pageTypeBlock->getAreaHandle(),
                    ];
                }
            }

            $records[] = [
                'blocksToAdd' => $blocksToAdd,
                'blocksToUpdate' => $blocksToUpdate,
                'cID' => $pagePlusCV['cID'],
                'cvID' => $pagePlusCV['cvID'],
            ];
        }

        $queueName = $queue->getName();
        $queue->deleteQueue();

        $queueService = $this->app->make(QueueService::class);
        $queue = $queueService->get($queueName);

        foreach ($records as $record) {
            $queue->send(serialize($record));
        }

        return $queue;
    }

    private function processBlockUpdateActions($actions, $pageTypeDefaultPage, $page, $pageBlock)
    {
        $handlesToOrder = [];
        $blockService = $this->app->make(Block::class);
        /* @var Block $blockService */
        foreach ($actions as $action) {
            // Update all forked pages by page type
            if ($action['name'] == 'update_forked') {
                $pageTypeBlock = $blockService->getByID($action['pageTypeBlockID'], $pageTypeDefaultPage, $action['pageTypeArHandle']);
                $bt = $pageTypeBlock->getBlockTypeObject();

                $pageBlock->deleteBlock();

                if ($bt->isCopiedWhenPropagated()) {
                    $pageBlock = $pageTypeBlock->duplicate($page, true);
                } else {
                    $pageTypeBlock->alias($page);
                }
                // Update block area by page type, if changed
            } elseif ($action['name'] == 'change_arHandle') {
                $db = $this->app->make(Connection::class);
                $actualArHandle = $action['actualArHandle'];
                $pageCollectionID = $page->getCollectionID();
                $pageVersionID = $page->getVersionID();

                $db->executeQuery(
                    'UPDATE CollectionVersionBlockStyles SET arHandle = ?  WHERE cID = ? and cvID = ? and bID = ?',
                    [$actualArHandle, $pageCollectionID, $pageVersionID, $pageBlock->getBlockID()]
                );
                $db->executeQuery(
                    'UPDATE CollectionVersionBlocks SET arHandle = ?  WHERE cID = ? and cvID = ? and bID = ?',
                    [$actualArHandle, $pageCollectionID, $pageVersionID, $pageBlock->getBlockID()]
                );
                // Update display order by page type
            } elseif ($action['name'] == 'change_display_order') {
                $pageBlock->setAbsoluteBlockDisplayOrder($action['actualDisplayOrder']);
                array_push($handlesToOrder, $action['actualArHandle']);
                // If block doesn't appear in page type, delete it
            } elseif ($action['name'] == 'delete') {
                $pageBlock->deleteBlock();
            }
        }

        return $handlesToOrder;
    }

    public function submit($ptID, $pTemplateID)
    {
        $responseFactory = $this->app->make(ResponseFactory::class);

        if (!$this->validateAction() || !$this->canAccess()) {
            $this->app->shutdown();

            return;
        }

        $this->fetchTypeAndTemplate($ptID, $pTemplateID);
        $pageTypeDefaultPage = $this->pagetype->getPageTypePageTemplateDefaultPageObject($this->template);

        if (!$pageTypeDefaultPage->isMasterCollection()) {
            $this->app->shutdown();

            return;
        }

        $queueService = $this->app->make(QueueService::class);
        /* @var Queue $queueService */
        $queueName = sprintf('update_pagetype_defaults_%s', $this->pagetype->getPageTypeID());
        $queue = $queueService->get($queueName);

        $pageService = $this->app->make(Page::class);
        /* @var Page $pageService */
        $blockService = $this->app->make(Block::class);
        /* @var Block $blockService */
        if ($_POST['process']) {
            $db = $this->app->make(Connection::class);
            $obj = new \stdClass();
            $messages = $queue->receive(20);

            foreach ($messages as $key => $message) {
                $record = unserialize($message->body);
                $page = $pageService->getByID($record['cID'], $record['cvID']);

                $blocksToUpdate = $record['blocksToUpdate'];
                $blocksToAdd = $record['blocksToAdd'];
                $handlesToOrder = [];

                foreach ($blocksToAdd as $blockToAdd) {
                    $pageTypeBlock = $blockService->getByID($blockToAdd['bID'], $pageTypeDefaultPage, $blockToAdd['pageTypeArHandle']);
                    $pageTypeBlock->alias($page);
                    $addedChildPageblock = $blockService->getByID($pageTypeBlock->getBlockID(), $page, $blockToAdd['pageTypeArHandle']);
                    $addedChildPageblock->setAbsoluteBlockDisplayOrder($blockToAdd['actualDisplayOrder']);
                }

                foreach ($blocksToUpdate as $blockToUpdate) {
                    $pageBlock = $blockService->getByID($blockToUpdate['bID'], $page, $blockToUpdate['arHandle']);
                    $permissionsClass = $this->app->make(Permissions::class);
                    $pageBlockPerms = new $permissionsClass($pageBlock);

                    if (!is_object($pageBlock) || $pageBlock->isError() || !$pageBlockPerms->canAdminBlock()) {
                        continue;
                    }

                    array_merge(
                        $handlesToOrder,
                        $this->processBlockUpdateActions($blockToUpdate['actions'], $pageTypeDefaultPage, $page, $pageBlock)
                    );
                }

                foreach ($handlesToOrder as $handleToOrder) {
                    $page->rescanDisplayOrder($handleToOrder);
                }

                $queue->deleteMessage($message);
            }

            $obj->totalItems = $queue->count();

            if ($queue->count() === 0) {
                $queue->deleteQueue($name);
            }

            $obj->message = t('All child pages updated successfully.');

            return $responseFactory->json($obj);
        } else {
            $queue = $this->queueForPageTypeUpdate($pageTypeDefaultPage, $queue);
        }

        $totalItems = $queue->count();
        View::element('progress_bar', ['totalItems' => $totalItems, 'totalItemsSummary' => t2('%d page', '%d pages', $totalItems)]);
    }

    protected function canAccess()
    {
        return $this->permissions->canEditPageType();
    }

    private function fetchTypeAndTemplate($ptID, $pTemplateID)
    {
        if (!$ptID || !$pTemplateID) {
            throw new \Exception(t('Invalid request data.'));
        }

        $permissionsClass = $this->app->make(Permissions::class);
        $cmp = new $permissionsClass($this->pagetype);

        if (!$cmp->canEditPageType()) {
            throw new \Exception(t('You do not have access to edit this page type.'));
        }

        $this->template = PageTemplate::getByID($pTemplateID);

        if (!$this->template) {
            throw new \Exception(t('Invalid request data.'));
        }
    }
}
