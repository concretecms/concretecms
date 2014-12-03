<?php

namespace Concrete\Controller\SinglePage\Dashboard\Multilingual;

use \Concrete\Core\Page\Controller\DashboardPageController;

defined('C5_EXECUTE') or die("Access Denied.");

class PageReport extends DashboardPageController
{
    public $helpers = array('form');

    public function view()
    {
        Loader::model('section', 'multilingual');
        $list = MultilingualSection::getList();
        $sections = array();
        foreach ($list as $pc) {
            $sections[$pc->getCollectionID()] = $pc->getLanguageText() . " (" . $pc->getLocale() . ")";
        }
        $this->set('sections', $sections);
        $this->set('sectionList', $list);

        if (!isset($_REQUEST['sectionID']) && (count($sections) > 0)) {
            foreach ($sections as $key => $value) {
                $sectionID = $key;
                break;
            }
        } else {
            $sectionID = $_REQUEST['sectionID'];
        }

        if (!isset($_REQUEST['targets']) && (count($sections) > 1)) {
            $i = 0;
            foreach ($sections as $key => $value) {
                if ($key != $sectionID) {
                    $targets[$key] = $key;
                    break;
                }
                $i++;
            }
        } else {
            $targets = $_REQUEST['targets'];
        }
        if (!isset($targets) || (!is_array($targets))) {
            $targets = array();
        }

        $targetList = array();
        foreach ($targets as $key => $value) {
            $targetList[] = MultilingualSection::getByID($key);
        }
        $this->set('targets', $targets);
        $this->set('targetList', $targetList);
        $this->set('sectionID', $sectionID);
        if (isset($sectionID) && $sectionID > 0) {
            Loader::model('multilingual_page_list', 'multilingual');
            $pl = new MultilingualPageList();
            $pl->filterByIsAlias(false);
            $pc = Page::getByID($sectionID);
            $path = $pc->getCollectionPath();
            if (strlen($path) > 1) {
                $pl->filterByPath($path);
            }

            if ($_REQUEST['keywords']) {
                $pl->filterByName($_REQUEST['keywords']);
            }

            $pl->setItemsPerPage(25);
            $pl->ignoreAliases();
            if (!$_REQUEST['showAllPages']) {
                $pl->filterByMissingTargets($targetList);
            }

            $pages = $pl->getPage();
            $this->set('pages', $pages);
            $this->set('section', MultilingualSection::getByID($sectionID));
            $this->set('pl', $pl);
        }
    }

    public function assign_page()
    {
        Loader::model('section', 'multilingual');
        if (Loader::helper('validation/token')->validate('assign_page', $_POST['token'])) {
            if ($_REQUEST['destID'] == $_REQUEST['sourceID']) {
                print '<span class="ccm-error">' . t("You cannot assign this page to itself.") . '</span>';
                exit;
            }
            $destPage = Page::getByID($_POST['destID']);
            if (MultilingualSection::isMultilingualSection($destPage)) {
                $ms = MultilingualSection::getByID($destPage->getCollectionID());
            } else {
                $ms = MultilingualSection::getBySectionOfSite($destPage);
            }
            if (is_object($ms)) {
                $page = Page::getByID($_POST['sourceID']);

                // we need to assign/relate the source ID too, if it doesn't exist
                if (!MultilingualSection::isAssigned($page)) {
                    MultilingualSection::assignAdd($page);
                }

                MultilingualSection::relatePage($page, $destPage, $ms->getLocale());
                print '<a href="' . Loader::helper("navigation")->getLinkToCollection(
                        $destPage
                    ) . '">' . $destPage->getCollectionName() . '</a>';
            } else {
                print '<span class="ccm-error">' . t(
                        "The destination page doesn't appear to be in a valid multilingual section."
                    ) . '</span>';
            }
        }
        exit;
    }

    public function ignore_page()
    {
        Loader::model('section', 'multilingual');
        if (Loader::helper('validation/token')->validate('ignore_page', $_POST['token'])) {
            $page = Page::getByID($_POST['sourceID']);
            MultilingualSection::ignorePageRelation($page, $_POST['locale']);
            print t('Ignored');
        }
        exit;
    }

    public function create_page()
    {
        Loader::model('section', 'multilingual');
        if (Loader::helper('validation/token')->validate('create_page', $_POST['token'])) {
            $ms = MultilingualSection::getByLocale($_POST['locale']);

            $page = Page::getByID($_POST['sourceID']);
            if (is_object($page) && !$page->isError()) {
                // we get the related parent id
                $cParentID = $page->getCollectionParentID();
                $cParent = Page::getByID($cParentID);
                $cParentRelatedID = $ms->getTranslatedPageID($cParent);
                if ($cParentRelatedID > 0) {
                    // we copy the page underneath it and store it
                    $newParent = Page::getByID($cParentRelatedID);
                    $ct = CollectionType::getByID($page->getCollectionTypeID());
                    $cp = new Permissions($newParent);
                    if ($cp->canAddSubCollection($ct) && $page->canMoveCopyTo($newParent)) {
                        $newPage = $page->duplicate($newParent);
                        if (is_object($newPage)) {
                            // grab the approved version and unapprove it
                            $v = CollectionVersion::get($newPage, 'ACTIVE');
                            if (is_object($v)) {
                                $v->deny();
                                $pkr = new ApprovePagePageWorkflowRequest();
                                $pkr->setRequestedPage($newPage);
                                $u = new User();
                                $pkr->setRequestedVersionID($v->getVersionID());
                                $pkr->setRequesterUserID($u->getUserID());
                                $pkr->trigger();
                            }

                            print '<a href="' . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $newPage->getCollectionID(
                                ) . '">' . $newPage->getCollectionName() . '</a>';
                        }
                    } else {
                        print '<span class="ccm-error">' . t("Insufficient permissions.") . '</span>';
                    }
                }
            }
        }
        exit;
    }
}