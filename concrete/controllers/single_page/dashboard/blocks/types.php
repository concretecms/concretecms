<?php
namespace Concrete\Controller\SinglePage\Dashboard\Blocks;

use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Entity\Block\BlockType\BlockType as BlockTypeEntity;
use Concrete\Core\Page\Search\Field\Field\ContainsBlockTypeField;
use Concrete\Core\Page\Search\SearchProvider;
use Loader;
use Environment;
use BlockTypeList;
use Block;
use BlockType;
use TaskPermission;
use Exception;

class Types extends DashboardPageController
{
    public function on_start()
    {
        $this->set('ci', Loader::helper('concrete/urls'));
        parent::on_start();
    }

    public function view()
    {
        $env = Environment::get();
        $env->clearOverrideCache();
        $btAvailableArray = BlockTypeList::getAvailableList();
        $btl = new BlockTypeList();
        $btl->includeInternalBlockTypes();
        $btInstalledArray = $btl->get();
        $internalBlockTypes = array();
        $normalBlockTypes = array();
        foreach ($btInstalledArray as $_bt) {
            if ($_bt->isInternalBlockType()) {
                $internalBlockTypes[] = $_bt;
            } else {
                $normalBlockTypes[] = $_bt;
            }
        }
        $this->set('internalBlockTypes', $internalBlockTypes);
        $this->set('normalBlockTypes', $normalBlockTypes);
        $this->set('availableBlockTypes', $btAvailableArray);
    }

    public function reset_display_order()
    {
        if ($this->post()) {
            BlockTypeList::resetBlockTypeDisplayOrder();
            $this->set('message', t('Display Order Reset.'));
        }
        $this->view();
    }

    public function refresh($btID = 0)
    {
        if ($btID > 0) {
            $bt = BlockType::getByID($btID);
        }

        if (isset($bt) && ($bt instanceof BlockTypeEntity)) {
            try {
                $bt->refresh();
                $this->set('message', t('Block Type Refreshed. Any database schema changes have been applied.'));
            } catch (Exception $e) {
                $this->set('error', $e);
            }
            $this->inspect($btID);
        }
    }

    public function install($btHandle = null)
    {
        $tp = new TaskPermission();
        if ($tp->canInstallPackages()) {
            try {
                $resp = BlockType::installBlockType($btHandle);
                $this->redirect('/dashboard/blocks/types', 'installed');
            } catch (\Exception $e) {
                $this->error->add($e);
            }
        } else {
            $this->error->add(t('You do not have permission to install custom block types or add-ons.'));
            $this->set('error', $this->error);
        }
        $this->view();
    }

    public function installed()
    {
        $this->set('success', t('Block type installed successfully.'));
        $this->view();
    }

    public function uninstall($btID = 0, $token = '')
    {
        $valt = Loader::helper('validation/token');

        if ($btID > 0) {
            $bt = BlockType::getByID($btID);
        }

        $u = new \User();
        if (!$u->isSuperUser()) {
            $this->error->add(t('Only the super user may remove block types.'));
        } elseif (isset($bt) && ($bt instanceof BlockTypeEntity)) {
            if (!$valt->validate('uninstall', $token)) {
                $this->error->add($valt->getErrorMessage());
            } elseif ($bt->canUnInstall()) {
                $bt->delete();
                $this->redirect('/dashboard/blocks/types', 'block_type_deleted');
            } else {
                $this->error->add(t('This block type is internal. It cannot be uninstalled.'));
            }
        } else {
            $this->error->add('Invalid block type.');
        }

        if ($this->error->has()) {
            $this->set('error', $this->error);
        }
        $this->inspect($btID);
    }

    public function search($btID = 0)
    {
        $bt = BlockType::getByID($btID);
        if (is_object($bt)) {
            /**
             * @var $provider SearchProvider
             */
            $provider = $this->app->make('Concrete\Core\Page\Search\SearchProvider');
            $query = new Query();
            $field = new ContainsBlockTypeField(['btID' => $btID]);
            $query->setFields([$field]);
            $query->setColumns($provider->getDefaultColumnSet());
            $provider->setSessionCurrentQuery($query);
            $this->redirect('/dashboard/sitemap/search');
            exit;
        }
    }

    public function inspect($btID = 0)
    {
        if ($btID > 0) {
            $bt = BlockType::getByID($btID);
        }

        if (isset($bt) && ($bt instanceof BlockTypeEntity)) {
            $this->set('bt', $bt);
            $this->set('num', $bt->getCount());
            $this->set('numActive', $bt->getCount(true));
        } else {
            $this->redirect('/dashboard/blocks/types');
        }
    }

    public function block_type_deleted()
    {
        $this->set('message', t('The block type has been removed.'));
        $this->view();
    }
}
