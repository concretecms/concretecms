<?php

namespace Concrete\Controller\Dialog\Type;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Page\PageList;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Foundation\Queue\Queue;
use PageTemplate;
use PageType;
use Permissions;

class UpdateFromType extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/type/update_from_type';


    private function fetchTypeAndTemplate($ptID, $pTemplateID){
      $cmp = new \Permissions($this->pagetype);

      if (!$cmp->canEditPageType()) {
        throw new \Exception(t('You do not have access to edit this page type.'));
      }

      $this->template = PageTemplate::getByID($pTemplateID);

      if (!$this->template) {
        throw new \Exception(t('Invalid request data.'));
      }
    }

    public function on_start()
    {
        parent::on_start();

        $request = $this->request;
        $ptID = $request->get('ptID');

        if (!$ptID || !$request->get('ptID')) {
          throw new \Exception(t('Invalid request data.'));
        }

        $this->pagetype = PageType::getByID($ptID);

        if (!is_object($this->pagetype)) {
            throw new \Exception('Invalid page type');
        }

        $this->permissions = new \Permissions($this->pagetype);
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

    public function queueForPageTypeUpdate($pageTypeDefaultPage, $queue)
    {
      $records = array();

      $pageTypeDefaultPageBlocks = [];
      $pageTypeDefaultPageRelBlocks = [];

      foreach ($pageTypeDefaultPage->getBlocks() as $b) {
        $pageTypeDefaultPageBlocks[$b->getBlockID()] = $pageTypeDefaultPageRelBlocks[$b->getBlockRelationID()] = $b;
      }

      $db = \Database::connection();
      $site = \Core::make('site')->getSite();
      $siteTreeID = $site->getSiteTreeID();

      $ptID = $pageTypeDefaultPage->getPageTypeID();
      $pagesPlusCV = $db->GetAll('select p.cID, max(cvID) as cvID from Pages p inner join CollectionVersions cv on p.cID = cv.cID where ptID = ? and cIsTemplate = 0 and cIsActive = 1 and siteTreeID = ? group by cID order by cID', [$ptID, $siteTreeID]);

      foreach ($pagesPlusCV as $pagePlusCV) {

        $pageTypeDefaultPageBlocksClone = $pageTypeDefaultPageBlocks;

        $asociatedBlocks = $db->GetAll('select cbDisplayOrder, arHandle, bID, cbRelationID from CollectionVersionBlocks where cID = ? and cvID = ?', array(
            $pagePlusCV['cID'], $pagePlusCV['cvID']
        ));

        $blocksToUpdate = array();
        $blocksToAdd = array();

        foreach ($asociatedBlocks as $asociatedBlock) {

          $bID = $asociatedBlock['bID'];
          $cbRelationID = $asociatedBlock['cbRelationID'];

          $blockToUpdate = array(
            'bID' => $bID,
            'arHandle' => $asociatedBlock['arHandle'],
            'actions' => array()
          );

          $blockPageTypeDefaultPage = NULL;

          if(array_key_exists($bID, $pageTypeDefaultPageBlocks)){
            $blockPageTypeDefaultPage = $pageTypeDefaultPageBlocks[$bID];

          }else if(array_key_exists($cbRelationID, $pageTypeDefaultPageRelBlocks)){
            $blockPageTypeDefaultPage = $pageTypeDefaultPageRelBlocks[$cbRelationID];
            $blockToUpdate['actions'][] = array('name' => 'update_forked', 'pageTypeBlockID' => $blockPageTypeDefaultPage->getBlockID(), 'pageTypeArHandle' => $blockPageTypeDefaultPage->getAreaHandle());
          }else{
            $blockToUpdate['actions'][] = array('name' => 'delete');
          }

          if($blockPageTypeDefaultPage){
            $forceChangeOrder = false;

            if($asociatedBlock['arHandle'] != $blockPageTypeDefaultPage->getAreaHandle()){
              $blockToUpdate['actions'][] = array(
                'name' => 'change_arHandle',
                'actualArHandle' => $blockPageTypeDefaultPage->getAreaHandle()
              );
              
              $forceChangeOrder = true;
            }

            if($forceChangeOrder || ($asociatedBlock['cbDisplayOrder'] != $blockPageTypeDefaultPage->cbDisplayOrder)){
              $blockToUpdate['actions'][] = array(
                'name' => 'change_display_order',
                'actualDisplayOrder' => $blockPageTypeDefaultPage->cbDisplayOrder,
                'actualArHandle' => $blockPageTypeDefaultPage->getAreaHandle()
              );
            }
          }

          unset($pageTypeDefaultPageBlocksClone[$bID]);

          if(!empty($blockToUpdate['actions'])){
            $blocksToUpdate[] = $blockToUpdate;
          }
        }

        if(count($pageTypeDefaultPageBlocksClone) > 0){
          foreach ($pageTypeDefaultPageBlocksClone as $pageTypeBlock) {
            $blocksToAdd[] = array('bID' => $pageTypeBlock->getBlockID(), 'actualDisplayOrder' => $pageTypeBlock->cbDisplayOrder, 'pageTypeArHandle' => $pageTypeBlock->getAreaHandle());
          }
        }

        $records[] = array(
          'blocksToAdd' => $blocksToAdd,
          'blocksToUpdate' => $blocksToUpdate,
          'cID' => $pagePlusCV['cID'],
          'cvID' => $pagePlusCV['cvID']
        );

      }

      $queueName = $queue->getName();
      $queue->deleteQueue();
      $queue = Queue::get($queueName);

      foreach ($records as $record) {
          $queue->send(serialize($record));
      }

      return $queue;
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

        $queueName = sprintf('update_pagetype_defaults_%s', $this->pagetype->getPageTypeID());
        $queue = \Queue::get($queueName);

        if ($_POST['process']) {
          $db = \Database::connection();
          $obj = new \stdClass();
          $messages = $queue->receive(20);

          foreach ($messages as $key => $message) {
            $record = unserialize($message->body);
            $childPage = \Page::getByID($record['cID'], $record['cvID']);

            $blocksToUpdate = $record['blocksToUpdate'];
            $blocksToAdd = $record['blocksToAdd'];
            $handlesToOrder = array();

            foreach ($blocksToAdd as $blockToAdd) {
              $pageTypeBlock = \Block::getByID($blockToAdd['bID'], $pageTypeDefaultPage, $blockToAdd['pageTypeArHandle']);
              $pageTypeBlock->alias($childPage);
              $addedChildPageblock = \Block::getByID($pageTypeBlock->getBlockID(), $childPage, $blockToAdd['pageTypeArHandle']);
              $addedChildPageblock->setAbsoluteBlockDisplayOrder($blockToAdd['actualDisplayOrder']);
            }

            foreach ($blocksToUpdate as $blockToUpdate) {
              $childPageBlock = \Block::getByID($blockToUpdate['bID'], $childPage, $blockToUpdate['arHandle']);
              $childPageBlockPerms = new \Permissions($childPageBlock);

              if (!is_object($childPageBlock) || $childPageBlock->isError() || !$childPageBlockPerms->canAdminBlock()) {
                continue;
              }

              $actions = $blockToUpdate['actions'];

              foreach ($actions as $action) {
                if($action['name'] == 'update_forked'){
                  $pageTypeBlock = \Block::getByID($action['pageTypeBlockID'], $pageTypeDefaultPage, $action['pageTypeArHandle']);
                  $bt = $pageTypeBlock->getBlockTypeObject();

                  $childPageBlock->deleteBlock();

                  if ($bt->isCopiedWhenPropagated()) {
                    $childPageBlock = $pageTypeBlock->duplicate($childPage, true);
                  }else{
                    $pageTypeBlock->alias($childPage);
                  }
                }
                else if ($action['name'] == 'change_arHandle') {

                  $nh = $action['actualArHandle'];
                  $pageCollectionID = $childPage->getCollectionID();
                  $pageVersionID = $childPage->getVersionID();

                  $db->executeQuery('UPDATE CollectionVersionBlockStyles SET arHandle = ?  WHERE cID = ? and cvID = ? and bID = ?',
                               [$nh, $pageCollectionID, $pageVersionID, $childPageBlock->getBlockID()]);
                  $db->executeQuery('UPDATE CollectionVersionBlocks SET arHandle = ?  WHERE cID = ? and cvID = ? and bID = ?',
                               [$nh, $pageCollectionID, $pageVersionID, $childPageBlock->getBlockID()]);
                }
                else if ($action['name'] == 'change_display_order') {
                  $childPageBlock->setAbsoluteBlockDisplayOrder($action['actualDisplayOrder']);
                  array_push($handlesToOrder, $action['actualArHandle']);
                }
                else if ($action['name'] == 'delete') {
                  $childPageBlock->deleteBlock();
                }
              }

            }

            foreach ($handlesToOrder as $handleToOrder) {
              $childPage->rescanDisplayOrder($handleToOrder);
            }

            $queue->deleteMessage($message);
          }

          $obj->totalItems = $queue->count();

          if ($queue->count() == 0) {
              $queue->deleteQueue($name);
          }

          $obj->message = t('All child child pages updated successfully.');
          return $responseFactory->json($obj);

        } else {
          $queue = $this->queueForPageTypeUpdate($pageTypeDefaultPage, $queue);
        }

        $totalItems = $queue->count();
        \View::element('progress_bar', array('totalItems' => $totalItems, 'totalItemsSummary' => t2("%d pages", "%d pages", $totalItems)));
    }

    protected function canAccess()
    {
        return $this->permissions->canEditPageType();
    }
}
