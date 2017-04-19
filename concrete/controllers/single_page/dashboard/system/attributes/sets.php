<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Attributes;

use Concrete\Core\Attribute\SetManagerInterface;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Page\Controller\DashboardPageController;
use Loader;
use Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use AttributeSet;

class Sets extends DashboardPageController
{
    public $category;

    public function view()
    {
        $this->set('categories', AttributeKeyCategory::getList());
    }

    public function category($categoryID = false, $mode = false)
    {
        //$this->addHeaderItem('<style type="text/css"> .ccm-attribute-sortable-set-list li a:hover { cursor:move;}</style>');
        $this->addFooterItem('<script type="text/javascript">
		$(function() {
            $("ul.ccm-attribute-sortable-set-list").sortable({
                opacity: 0.5,
                stop: function() {
                    var ualist = $(this).sortable(\'serialize\');
                    ualist += \'&categoryID=' . $categoryID . '\';
                    $.post(\'' . REL_DIR_FILES_TOOLS_REQUIRED . '/dashboard/attribute_set_order_update\', ualist, function(r) {

                    });
                }
            });
		});
	</script>');
        if (!intval($categoryID)) {
            $this->redirect('/dashboard/system/attributes/sets');
        }
        $this->category = AttributeKeyCategory::getByID($categoryID);
        if (is_object($this->category)) {
            $sets = $this->category->getController()->getSetManager()->getAttributeSets();
            $this->set('sets', $sets);
        } else {
            $this->redirect('/dashboard/system/attributes/sets');
        }
        $this->set('categoryID', $categoryID);
        switch ($mode) {
            case 'set_added':
                $this->set('message', t('Attribute set added.'));
                break;
            case 'set_deleted':
                $this->set('message', t('Attribute set deleted.'));
                break;
            case 'set_updated':
                $this->set('message', t('Attribute set updated.'));
                break;
        }
    }

    public function add_set()
    {
        $this->category($this->post('categoryID'));
        if ($this->token->validate('add_set')) {
            if (!trim($this->post('asHandle'))) {
                $this->error->add(t("Specify a handle for your attribute set."));
            } else {
                $as = AttributeSet::getByHandle($this->post('asHandle'));
                if (is_object($as)) {
                    $this->error->add(t('That handle is in use.'));
                }
            }
            if (!trim($this->post('asName'))) {
                $this->error->add(t("Specify a name for your attribute set."));
            } else {
                if (preg_match('/[<>;{}?"`]/', trim($this->post('asName')))) {
                    $this->error->add(t('Name cannot contain the characters: %s',
                        Loader::helper('text')->entities('<>;{}?`')));
                }
            }
            if (!$this->error->has()) {
                /**
                 * @var $manager SetManagerInterface
                 */
                $manager = $this->category->getController()->getSetManager();
                if ($manager->allowAttributeSets()) {
                    $manager->addSet($this->post('asHandle'), $this->post('asName'), false, 0);
                    $this->redirect('dashboard/system/attributes/sets', 'category',
                        $this->category->getAttributeKeyCategoryID(), 'set_added');
                }
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
    }

    public function update_set()
    {
        $this->edit($this->post('asID'));
        if ($this->token->validate('update_set')) {
            $as = AttributeSet::getByID($this->post('asID'));
            if (!is_object($as)) {
                $this->error->add(t('Invalid attribute set.'));
            } else {
                if (!trim($this->post('asHandle')) && (!$as->isAttributeSetLocked())) {
                    $this->error->add(t("Specify a handle for your attribute set."));
                } else {
                    $asx = AttributeSet::getByHandle($this->post('asHandle'));
                    if (is_object($asx) && $asx->getAttributeSetID() != $as->getAttributeSetID()) {
                        $this->error->add(t('That handle is in use.'));
                    }
                }
                if (!trim($this->post('asName'))) {
                    $this->error->add(t("Specify a name for your attribute set."));
                }
            }

            if (!$this->error->has()) {
                if (!$as->isAttributeSetLocked()) {
                    $as->setAttributeSetHandle($this->post('asHandle'));
                }
                $as->setAttributeSetName($this->post('asName'));
                $this->entityManager->persist($as);
                $this->entityManager->flush();
                $this->redirect('dashboard/system/attributes/sets', 'category', $as->getAttributeSetKeyCategoryID(),
                    'set_updated');
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
    }

    public function update_set_attributes()
    {
        if ($this->token->validate('update_set_attributes')) {
            $as = AttributeSet::getByID($this->post('asID'));
            if (!is_object($as)) {
                $this->error->add(t('Invalid attribute set.'));
            }

            if (!$this->error->has()) {
                // go through and add all the attributes that aren't in another set
                foreach ($as->getAttributeKeyCollection() as $setKey) {
                    $this->entityManager->remove($setKey);
                }

                $this->entityManager->flush();
                $cat = AttributeKeyCategory::getByID($as->getAttributeSetKeyCategoryID());
                $category = $cat->getAttributeKeyCategory();
                $unassigned = $category->getSetManager()->getUnassignedAttributeKeys();

                if (is_array($this->post('akID'))) {
                    foreach ($unassigned as $ak) {
                        if (in_array($ak->getAttributeKeyID(), $this->post('akID'))) {
                            // This is a lame hack but our legacy support is biting us here:
                            if (!($ak instanceof Key)) {
                                $ak = \Concrete\Core\Attribute\Key\Key::getByID($ak->getAttributeKeyID());
                            }
                            $as->addKey($ak);
                        }
                    }
                    $this->entityManager->persist($as);
                    $this->entityManager->flush();
                }
                $this->redirect('dashboard/system/attributes/sets', 'category', $cat->getAttributeKeyCategoryID(),
                    'set_updated');
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
        $this->edit($this->post('asID'));
    }

    public function delete_set()
    {
        if ($this->token->validate('delete_set')) {
            $as = AttributeSet::getByID($this->post('asID'));
            if (!is_object($as)) {
                $this->error->add(t('Invalid attribute set.'));
            } else {
                if ($as->isAttributeSetLocked()) {
                    $this->error->add(t('This attribute set is locked. That means it was added by a package and cannot be manually removed.'));
                    $this->edit($as->getAttributeSetID());
                }
            }
            if (!$this->error->has()) {
                $categoryID = $as->getAttributeSetKeyCategoryID();
                $this->entityManager->remove($as);
                $this->entityManager->flush();
                $this->redirect('dashboard/system/attributes/sets', 'category', $categoryID, 'set_deleted');
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
        $this->view();
    }

    public function edit($asID = false)
    {
        $as = AttributeSet::getByID($asID);
        if (is_object($as)) {
            $this->set('set', $as);
        } else {
            $this->redirect('/dashboard/system/attributes/sets');
        }
    }
}
