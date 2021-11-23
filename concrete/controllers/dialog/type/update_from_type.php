<?php
namespace Concrete\Controller\Dialog\Type;

use Block;
use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Command\Batch\Batch;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Type\Command\UpdatePageTypeDefaultsCommand;
use Page;
use PageTemplate;
use PageType;
use Permissions;
use View;

/**
 * Note – this came from a half-completed pull request, and no routes actually reference this controller anymore.
 * I'm keeping it around in case we decide to finish the functionality at some point.
 */
class UpdateFromType extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/type/update_from_type';

    public function on_start()
    {

        throw new UserMessageException(t('This feature is not implemented yet.'));

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

    protected function queueForPageTypeUpdate($pageTypeDefaultPage)
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
        $pagesPlusCV = $db->fetchAll('select p.cID, max(cvID) as cvID from Pages p inner join CollectionVersions cv on p.cID = cv.cID where ptID = ? and cIsTemplate = 0 and cIsActive = 1 and siteTreeID = ? group by cID order by cID',
            [$ptID, $siteTreeID]);

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
                'blocksToAdd' => json_encode($blocksToAdd),
                'blocksToUpdate' => json_encode($blocksToUpdate),
                'cID' => $pagePlusCV['cID'],
                'cvID' => $pagePlusCV['cvID'],
            ];
        }

        $batch = Batch::create(t('Update Page Type Defaults'), function() use ($records, $pageTypeDefaultPage) {
            foreach ($records as $record) {
                yield new UpdatePageTypeDefaultsCommand(
                    $pageTypeDefaultPage->getCollectionID(),
                    $record['cID'],
                    $record['cvID'],
                    $record['blocksToUpdate'],
                    $record['blocksToAdd']
                );
            }
        });
        return $this->dispatchBatch($batch);
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

        return $this->queueForPageTypeUpdate($pageTypeDefaultPage);
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
