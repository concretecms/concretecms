<?php
namespace Concrete\Core\Application\Service;

use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Page\Type\Composer\Control\Control as PageTypeComposerControl;
use View;

class Composer
{
    /**
     * @param Type $pagetype
     * @param bool|\Page $page
     */
    public function display(Type $pagetype, $page = false)
    {
        $pagetype->renderComposerOutputForm($page);
    }

    /**
     * @param Type $pagetype
     * @param bool|\Page $page
     */
    public function displayButtons(Type $pagetype, $page = false)
    {
        View::element('page_types/composer/form/output/buttons', array(
            'pagetype' => $pagetype,
            'page' => $page,
        ));
    }

    /**
     * @param Type $pt
     * @param \Controller $cnt
     */
    public function addAssetsToRequest(Type $pt, \Controller $cnt)
    {
        $list = PageTypeComposerControl::getList($pt);
        foreach ($list as $l) {
            $l->addAssetsToRequest($cnt);
        }
    }

    public function getPublishButtonTitle(Page $c)
    {
        if ($c->isPageDraft()) {
            $publishTitle = t('Publish Page');
        } else {
            $publishTitle = t('Publish Changes');
        }

        $pk = Key::getByHandle('approve_page_versions');
        $pk->setPermissionObject($c);
        $pa = $pk->getPermissionAccessObject();
        $workflows = array();
        $canApproveWorkflow = true;
        if (is_object($pa)) {
            $workflows = $pa->getWorkflows();
        }
        foreach ($workflows as $wf) {
            if (!$wf->canApproveWorkflow()) {
                $canApproveWorkflow = false;
            }
        }

        if (count($workflows) > 0 && !$canApproveWorkflow) {
            $publishTitle = t('Submit to Workflow');
        }
        return $publishTitle;
    }

    public function displayPublishScheduleSettings(Page $c = null)
    {
        View::element('pages/schedule', array(
            'page' => $c,
        ));
    }
}
