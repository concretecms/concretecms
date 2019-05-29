<?php

namespace Concrete\Controller\SinglePage\Dashboard\Blocks;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\BlockType\BlockTypeList;
use Concrete\Core\Block\BlockType\Set as BlockTypeSet;
use Concrete\Core\Entity\Block\BlockType\BlockType as BlockTypeEntity;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Search\Field\Field\ContainsBlockTypeField;
use Concrete\Core\Page\Search\SearchProvider;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\User\User;
use Concrete\Core\Utility\Service\Validation\Numbers;

class Types extends DashboardPageController
{
    public function on_start()
    {
        $this->set('ci', $this->app->make('helper/concrete/urls'));
        $this->set('urlResolver', $this->app->make(ResolverManagerInterface::class));
        parent::on_start();
    }

    public function view()
    {
        $this->app->make('cache/overrides')->flush();
        $config = $this->app->make('config');
        $availableBlockTypes = BlockTypeList::getAvailableList();
        $blockTypeList = new BlockTypeList();
        $blockTypeList->includeInternalBlockTypes();
        $internalBlockTypes = [];
        $normalBlockTypes = [];
        foreach ($blockTypeList->get() as $bt) {
            if ($bt->isInternalBlockType()) {
                $internalBlockTypes[] = $bt;
            } else {
                $normalBlockTypes[] = $bt;
            }
        }
        $this->set('internalBlockTypes', $internalBlockTypes);
        $this->set('normalBlockTypesAndSets', $this->getSetsData($normalBlockTypes));
        $this->set('availableBlockTypes', $availableBlockTypes);
        $this->set('marketplaceEnabled', (bool) $config->get('concrete.marketplace.enabled'));
        $this->set('enableMoveBlocktypesAcrossSets', (bool) $config->get('concrete.misc.enable_move_blocktypes_across_sets'));
        $this->addHeaderItem(<<<EOT
<style>
    #ccm-btlist-btsets .fa-bars {
        visibility: hidden;
        cursor: ns-resize;
    }
    #ccm-btlist-btsets .ccm-btlist-btset-name:hover .fa-bars {
        visibility: visible;
    }
    #ccm-btlist-btsets .ccm-btlist-bt:hover .fa-bars {
        visibility: visible;
    }
    #ccm-btlist-btsets .ui-sortable-helper .fa-bars {
        visibility: visible;
    }
    #ccm-btlist-btsets .ccm-btlist-bts {
        min-height: 30px;
    }
</style>
EOT
        );
    }

    public function inspect($btID = 0)
    {
        $bt = $btID > 0 ? $this->entityManager->find(BlockTypeEntity::class, $btID) : null;
        if ($bt === null) {
            $this->flash('error', t('Unable to find the block type specified.'));

            return $this->app->make(ResponseFactoryInterface::class)->redirect(
                $this->app->make(ResolverManagerInterface::class)->resolve(['/dashboard/blocks/types']),
                302
            );
        }
        $this->set('pageTitle', t('Details of %s', t($bt->getBlockTypeName())));
        $this->set('bt', $bt);
        $this->set('num', $bt->getCount());
        $this->set('numActive', $bt->getCount(true));
    }

    public function refresh($btID = 0, $token = '')
    {
        $bt = $btID > 0 ? $this->entityManager->find(BlockTypeEntity::class, $btID) : null;
        if ($bt === null) {
            $this->flash('error', t('Unable to find the block type specified.'));

            return $this->app->make(ResponseFactoryInterface::class)->redirect(
                $this->app->make(ResolverManagerInterface::class)->resolve(['/dashboard/blocks/types']),
                302
            );
        }
        if ($this->token->validate('ccm-refresh-blocktype', $token)) {
            try {
                $bt->refresh();
                $this->flash('success', t('Block Type Refreshed. Any database schema changes have been applied.'));
            } catch (UserMessageException $e) {
                $this->flash('error', $e->getMessage());
            }
        } else {
            $this->flash('error', $this->token->getErrorMessage());
        }

        return $this->app->make(ResponseFactoryInterface::class)->redirect(
            $this->app->make(ResolverManagerInterface::class)->resolve(['/dashboard/blocks/types', 'inspect', $bt->getBlockTypeID()]),
            302
        );
    }

    public function uninstall($btID = 0, $token = '')
    {
        $redirectTo = ['/dashboard/blocks/types'];
        $bt = $btID > 0 ? $this->entityManager->find(BlockTypeEntity::class, $btID) : null;
        if ($bt === null) {
            $this->flash('error', t('Unable to find the block type specified.'));
        } elseif ($this->token->validate('ccm-uninstall-blocktype', $token)) {
            $u = new User();
            if (!$u->isSuperUser()) {
                $this->flash('error', t('Only the super user may remove block types.'));
                $redirectTo[] = 'inspect';
                $redirectTo[] = $bt->getBlockTypeID();
            } elseif (!$bt->canUnInstall()) {
                $this->flash('error', t('This block type is internal. It cannot be uninstalled.'));
                $redirectTo[] = 'inspect';
                $redirectTo[] = $bt->getBlockTypeID();
            } else {
                $bt->delete();
                $this->set('success', t('The block type has been removed.'));
            }
        } else {
            $this->flash('error', $this->token->getErrorMessage());
        }

        return $this->app->make(ResponseFactoryInterface::class)->redirect($this->app->make(ResolverManagerInterface::class)->resolve($redirectTo), 302);
    }

    public function install($btHandle = null)
    {
        $tp = new Checker();
        if ($tp->canInstallPackages()) {
            try {
                BlockType::installBlockType($btHandle);
                $this->flash('success', t('Block type installed successfully.'));
            } catch (UserMessageException $e) {
                $this->flash('error', $e->getMessage());
            }
        } else {
            $this->flash('error', t('You do not have permission to install custom block types or add-ons.'));
        }

        return $this->app->make(ResponseFactoryInterface::class)->redirect($this->app->make(ResolverManagerInterface::class)->resolve(['/dashboard/blocks/types']), 302);
    }

    public function search($btID = 0)
    {
        $bt = $btID > 0 ? $this->entityManager->find(BlockTypeEntity::class, $btID) : null;
        if ($bt === null) {
            $this->flash('error', t('Unable to find the block type specified.'));

            return $this->app->make(ResponseFactoryInterface::class)->redirect(
                $this->app->make(ResolverManagerInterface::class)->resolve(['/dashboard/blocks/types']),
                302
            );
        }
        $provider = $this->app->make(SearchProvider::class);
        $query = new Query();
        $field = new ContainsBlockTypeField(['btID' => $btID]);
        $query->setFields([$field]);
        $query->setColumns($provider->getDefaultColumnSet());
        $provider->setSessionCurrentQuery($query);

        return $this->app->make(ResponseFactoryInterface::class)->redirect(
            $this->app->make(ResolverManagerInterface::class)->resolve(['/dashboard/sitemap/search']),
            302
        );
    }

    public function sort_blocktypesets()
    {
        if (!$this->token->validate('ccm-sort_blocktypesets')) {
            throw new UserMessageException($this->token->getErrorMessage());
        }
        $valn = $this->app->make(Numbers::class);
        $rawBtSetIDs = $this->request->request->get('btSetIDs');
        if (!is_array($rawBtSetIDs)) {
            throw new UserMessageException(sprintf('Invalid parameters: %s', 'btSetIDs'));
        }
        $btSetIDs = [];
        foreach ($rawBtSetIDs as $rawBtSetID) {
            if (!$valn->integer($rawBtSetID, 0)) {
                throw new UserMessageException(sprintf('Invalid parameters: %s', 'btSetIDs'));
            }
            $btSetID = (int) $rawBtSetID;
            if (in_array($btSetID, $btSetIDs, true)) {
                throw new UserMessageException(sprintf('Invalid parameters: %s', 'btSetIDs'));
            }
            $btSetIDs[] = $btSetID;
        }
        $p = array_search(0, $btSetIDs, true);
        if ($p !== false) {
            if ($p !== count($btSetIDs) - 1) {
                throw new UserMessageException(t('The "%s" block type set must be the last one.', t('Other')));
            }
            array_pop($btSetIDs);
        }
        $btSets = BlockTypeSet::getList([]);
        if (count($btSets) !== count($btSetIDs)) {
            throw new UserMessageException(sprintf('Invalid parameters: %s', 'btSetIDs'));
        }
        $sortedSets = [];
        foreach ($btSets as $btSet) {
            $displayOrder = array_search((int) $btSet->getBlockTypeSetID(), $btSetIDs, true);
            if ($displayOrder === false) {
                throw new UserMessageException(sprintf('Invalid parameters: %s', 'btSetIDs'));
            }
            $sortedSets[$displayOrder] = $btSet;
        }
        foreach ($sortedSets as $displayOrder => $btSet) {
            $btSet->updateBlockTypeSetDisplayOrder($displayOrder);
        }

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    public function sort_blocktypes()
    {
        if (!$this->token->validate('ccm-sort_blocktypes')) {
            throw new UserMessageException($this->token->getErrorMessage());
        }
        $config = $this->app->make('config');
        $post = $this->request->request;
        $valn = $this->app->make(Numbers::class);
        $movingID = $post->get('movingID');
        if (!$valn->integer($movingID, 1)) {
            throw new UserMessageException(sprintf('Invalid parameters: %s', 'movingID'));
        }
        $movingBlockType = BlockType::getByID($movingID);
        if ($movingBlockType === null) {
            throw new UserMessageException(sprintf('Invalid parameters: %s', 'movingID'));
        }
        $oldBtSetID = $post->get('oldBtSetID');
        if (!$valn->integer($oldBtSetID, 0)) {
            throw new UserMessageException(sprintf('Invalid parameters: %s', 'oldBtSetID'));
        }
        $oldBtSetID = (int) $oldBtSetID;
        if ($oldBtSetID === 0) {
            $oldBtSet = null;
        } else {
            $oldBtSet = BlockTypeSet::getByID($oldBtSetID);
            if ($oldBtSet === null) {
                throw new UserMessageException(sprintf('Invalid parameters: %s', 'oldBtSetID'));
            }
        }
        $newBtSetID = $post->get('newBtSetID');
        if (!$valn->integer($newBtSetID, 0)) {
            throw new UserMessageException(sprintf('Invalid parameters: %s', 'newBtSetID'));
        }
        $newBtSetID = (int) $newBtSetID;
        if ($newBtSetID === 0) {
            $newBtSet = null;
        } elseif ($oldBtSetID === $newBtSetID) {
            $newBtSet = $oldBtSet;
        } else {
            $newBtSet = BlockTypeSet::getByID($newBtSetID);
            if ($newBtSet === null) {
                throw new UserMessageException(sprintf('Invalid parameters: %s', 'newBtSetID'));
            }
        }
        if (!$config->get('concrete.misc.enable_move_blocktypes_across_sets') && $newBtSetID !== $oldBtSetID) {
            throw new UserMessageException(sprintf('Invalid parameters: %s', 'oldBtSetID, newBtSetID'));
        }
        $rawBtIDs = $this->request->request->get('btIDs');
        if (!is_array($rawBtIDs)) {
            throw new UserMessageException(sprintf('Invalid parameters: %s', 'btIDs'));
        }
        $btIDs = [];
        foreach ($rawBtIDs as $rawBtID) {
            if (!$valn->integer($rawBtID, 0)) {
                throw new UserMessageException(sprintf('Invalid parameters: %s', 'btIDs'));
            }
            $btID = (int) $rawBtID;
            if (in_array($btID, $btIDs, true)) {
                $bt = $this->entityManager->find(BlockTypeEntity::class, $btID);
                if ($bt === null) {
                    throw new UserMessageException(sprintf('Invalid parameters: %s', 'btIDs'));
                }
                throw new UserMessageException(t('The block type set "%1$s" already contains the block type "%2$s".', $newBtSet === null ? t('Other') : $newBtSet->getBlockTypeSetDisplayName('text')));
            }
            $btIDs[] = $btID;
        }
        if ($newBtSet === null) {
            $blockTypes = BlockTypeSet::getUnassignedBlockTypes();
        } else {
            $blockTypes = $newBtSet->getBlockTypes();
        }
        if ($newBtSet === $oldBtSet) {
            if (count($btIDs) !== count($blockTypes)) {
                throw new UserMessageException(sprintf('Invalid parameters: %s', 'btIDs'));
            }
        } else {
            if (count($btIDs) !== count($blockTypes) + 1) {
                throw new UserMessageException(sprintf('Invalid parameters: %s', 'btIDs'));
            }
        }
        $sortedBlockTypes = [];
        foreach ($blockTypes as $blockType) {
            $displayOrder = array_search((int) $blockType->getBlockTypeID(), $btIDs, true);
            if ($displayOrder === false) {
                throw new UserMessageException(sprintf('Invalid parameters: %s', 'btIDs'));
            }
            $sortedBlockTypes[$displayOrder] = $blockType;
        }
        if ($oldBtSet !== $newBtSet) {
            $displayOrder = array_search((int) $movingBlockType->getBlockTypeID(), $btIDs, true);
            if ($displayOrder === false) {
                throw new UserMessageException(sprintf('Invalid parameters: %s', 'btIDs'));
            }
            $sortedBlockTypes[$displayOrder] = $movingBlockType;
        }
        if ($oldBtSet !== $newBtSet) {
            if ($oldBtSet !== null) {
                $oldBtSet->deleteKey($movingID);
            }
            if ($newBtSet !== null) {
                $newBtSet->addBlockType($movingBlockType);
            }
        }
        foreach ($sortedBlockTypes as $displayOrder => $blockType) {
            if ($newBtSet === null) {
                $blockType->setBlockTypeDisplayOrder($displayOrder);
            } else {
                $newBtSet->setBlockTypeDisplayOrder($blockType, $displayOrder);
            }
        }
        $this->entityManager->flush();

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    /**
     * @param \Concrete\Core\Entity\Block\BlockType\BlockType[] $normalBlockTypes
     */
    private function getSetsData(array $normalBlockTypes)
    {
        $result = [];
        $allAssignedBlockTypes = [];
        foreach (BlockTypeSet::getList([]) as $blockTypeSet) {
            $containedBlockTypes = $blockTypeSet->getBlockTypes();
            $result[] = [
                'blockTypeSet' => $blockTypeSet,
                'blockTypes' => $containedBlockTypes,
            ];
            $allAssignedBlockTypes = array_merge($allAssignedBlockTypes, $containedBlockTypes);
        }
        $unassignedBlockTypes = [];
        foreach ($normalBlockTypes as $normalBlockType) {
            if (!in_array($normalBlockType, $allAssignedBlockTypes, true)) {
                $unassignedBlockTypes[] = $normalBlockType;
            }
        }
        usort($unassignedBlockTypes, function (BlockTypeEntity $a, BlockTypeEntity $b) {
            return $a->getBlockTypeDisplayOrder() - $b->getBlockTypeDisplayOrder();
        });
        $result[] = [
            'blockTypeSet' => null,
            'blockTypes' => $unassignedBlockTypes,
        ];

        return $result;
    }
}
