<?php
namespace Concrete\Core\Page\Type\Saver;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Page\Collection\Version\VersionList;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Composer\Control\Control;
use Concrete\Core\Page\Type\Composer\Control\CorePageProperty\NameCorePageProperty;
use Concrete\Core\Page\Type\Event;
use Concrete\Core\Page\Type\Type;
use Core;

class StandardSaver implements SaverInterface
{

    protected $ptDraftVersionsToSave = 10;

    public function setPageTypeObject(Type $type)
    {
        $this->type = $type;
    }

    public function getPageTypeObject()
    {
        return $this->type;
    }

    public function saveForm(Page $c)
    {
        $controls = Control::getList($this->type);
        $outputControls = array();
        foreach ($controls as $cn) {
            $data = $cn->getRequestValue();
            $cn->publishToPage($c, $data, $controls);
            $outputControls[] = $cn;
        }

        // set page name from controls
        // now we see if there's a page name field in there
        $containsPageNameControl = false;
        foreach ($outputControls as $cn) {
            if ($cn instanceof NameCorePageProperty) {
                $containsPageNameControl = true;
                break;
            }
        }
        if (!$containsPageNameControl) {
            foreach ($outputControls as $cn) {
                if ($cn->canPageTypeComposerControlSetPageName()) {
                    $pageName = $cn->getPageTypeComposerControlPageNameValue($c);
                    $c->updateCollectionName($pageName);
                }
            }
        }

        // remove all but the most recent X drafts.
        if ($c->isPageDraft()) {
            $vl = new VersionList($c);
            $vl->setItemsPerPage(-1);
            // this will ensure that we only ever keep X versions.
            $vArray = $vl->getPage();
            if (count($vArray) > $this->ptDraftVersionsToSave) {
                for ($i = $this->ptDraftVersionsToSave; $i < count($vArray); ++$i) {
                    $v = $vArray[$i];
                    @$v->delete();
                }
            }
        }

        $c = Page::getByID($c->getCollectionID(), 'RECENT');
        $controls = array();
        foreach ($outputControls as $oc) {
            $oc->setPageObject($c);
            $controls[] = $oc;
        }

        $ev = new Event($c);
        $ev->setPageType($this->type);
        $ev->setArgument('controls', $controls);
        \Events::dispatch('on_page_type_save_composer_form', $ev);

        return $controls;
    }
}
