<?php

namespace Concrete\Core\Page\Type\Composer\Control;

use Concrete\Core\Page\Type\Type;
use Loader;
use Concrete\Core\Foundation\Object;
use Page;
use Controller;
use Concrete\Core\Page\Type\Composer\FormLayoutSet as PageTypeComposerFormLayoutSet;
use Concrete\Core\Page\Type\Composer\FormLayoutSetControl as PageTypeComposerFormLayoutSetControl;
use Concrete\Core\Page\Type\Composer\Control\Type\Type as PageTypeComposerControlType;

abstract class Control extends Object
{
    protected $ptComposerControlIdentifier;
    protected $ptComposerControlName;
    protected $ptComposerControlIconSRC;
    protected $ptComposerControl;
    protected $ptComposerControlRequiredByDefault = false;
    protected $ptComposerControlRequiredOnThisRequest = false;
    protected $ptComposerControlCustomLabel;
    protected $ptComposerControlDescription;

    abstract public function getPageTypeComposerControlCustomTemplates();
    abstract public function render($label, $customTemplate, $description);
    abstract public function publishToPage(Page $c, $data, $controls);
    abstract public function validate();
    abstract public function getPageTypeComposerControlDraftValue();
    abstract public function addAssetsToRequest(Controller $cnt);
    abstract public function export($node);
    abstract public function shouldPageTypeComposerControlStripEmptyValuesFromPage();
    abstract public function isPageTypeComposerControlValueEmpty();
    abstract public function removePageTypeComposerControlFromPage();

    public function pageTypeComposerFormControlSupportsValidation()
    {
        return false;
    }

    public function setPageTypeComposerControlName($ptComposerControlName)
    {
        $this->ptComposerControlName = $ptComposerControlName;
    }

    public function setPageTypeComposerFormControlRequired($req)
    {
        $this->ptComposerControlRequiredOnThisRequest = $req;
    }

    public function setPageTypeComposerControlCustomLabel($label)
    {
        $this->ptComposerControlCustomLabel = $label;
    }
    
    public function getPageTypeComposerControlCustomLabel()
    {
        return $this->ptComposerControlCustomLabel;
    }
    
    public function setPageTypeComposerControlDescription($description)
    {
        $this->ptComposerControlDescription = $description;
    }
    
    public function getPageTypeComposerControlDescription()
    {
        return $this->ptComposerControlDescription;
    }

    public function setPageObject($page)
    {
        $this->page = $page;
    }
    
    public function getPageObject()
    {
        return $this->page;
    }

    public function setTargetParentPageID($ptTargetParentPageID)
    {
        $this->ptTargetParentPageID = $ptTargetParentPageID;
    }

    public function getTargetParentPageID()
    {
        return $this->ptTargetParentPageID;
    }

    public function isPageTypeComposerFormControlRequiredOnThisRequest()
    {
        return $this->ptComposerControlRequiredOnThisRequest;
    }

    public function getPageTypeComposerControlName()
    {
        return $this->ptComposerControlName;
    }
    
    public function getPageTypeComposerControlDisplayName($format = 'html')
    {
        $value = $this->getPageTypeComposerControlName();
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    public function setPageTypeComposerControlIconSRC($ptComposerControlIconSRC)
    {
        $this->ptComposerControlIconSRC = $ptComposerControlIconSRC;
    }

    public function getPageTypeComposerControlIconSRC()
    {
        return $this->ptComposerControlIconSRC;
    }

    public function setPageTypeComposerControlIdentifier($ptComposerControlIdentifier)
    {
        $this->ptComposerControlIdentifier = $ptComposerControlIdentifier;
    }

    public function getPageTypeComposerControlIdentifier()
    {
        return $this->ptComposerControlIdentifier;
    }

    public function getPageTypeComposerControlTypeObject()
    {
        return PageTypeComposerControlType::getByHandle($this->ptComposerControlTypeHandle);
    }

    public function getPageTypeComposerControlTypeHandle()
    {
        return $this->ptComposerControlTypeHandle;
    }

    public function setPageTypeComposerFormLayoutSetControlObject(PageTypeComposerFormLayoutSetControl $setcontrol)
    {
        $this->ptComposerFormLayoutSetControlObject = $setcontrol;
    }

    public function getPageTypeComposerFormLayoutSetControlObject()
    {
        return $this->ptComposerFormLayoutSetControlObject;
    }

    public function field($key)
    {
        return 'ptComposer[' . $this->ptComposerFormLayoutSetControlObject->getPageTypeComposerFormLayoutSetControlID(). '][' . $key . ']';
    }

    public function getRequestValue($args = false)
    {
        if (!$args) {
            $args = $_POST;
        }

        return $args['ptComposer'][$this->ptComposerFormLayoutSetControlObject->getPageTypeComposerFormLayoutSetControlID()];
    }

    /**
     * @param PageTypeComposerFormLayoutSet $set
     *
     * @return \Concrete\Core\Page\Type\Composer\FormLayoutSetControl
     */
    public function addToPageTypeComposerFormLayoutSet(PageTypeComposerFormLayoutSet $set)
    {
        $db = Loader::db();
        $displayOrder = $db->GetOne('select count(ptComposerFormLayoutSetControlID) from PageTypeComposerFormLayoutSetControls where ptComposerFormLayoutSetID = ?', array($set->getPageTypeComposerFormLayoutSetID()));
        if (!$displayOrder) {
            $displayOrder = 0;
        }
        $ptComposerFormLayoutSetControlRequired = 0;
        if ($this->isPageTypeComposerControlRequiredByDefault()) {
            $ptComposerFormLayoutSetControlRequired = 1;
        }
        $controlType = $this->getPageTypeComposerControlTypeObject();
        $customLabel = $this->getPageTypeComposerControlCustomLabel();
        $description = $this->getPageTypeComposerControlDescription();
        $db->Execute('insert into PageTypeComposerFormLayoutSetControls (ptComposerFormLayoutSetID, ptComposerControlTypeID, ptComposerControlObject, ptComposerFormLayoutSetControlDisplayOrder, ptComposerFormLayoutSetControlCustomLabel, ptComposerFormLayoutSetControlDescription, ptComposerFormLayoutSetControlRequired) values (?, ?, ?, ?, ?, ?, ?)', array(
            $set->getPageTypeComposerFormLayoutSetID(), $controlType->getPageTypeComposerControlTypeID(), serialize($this), $displayOrder, $customLabel, $description, $ptComposerFormLayoutSetControlRequired,
        ));

        return PageTypeComposerFormLayoutSetControl::getByID($db->Insert_ID());
    }

    public function canPageTypeComposerControlSetPageName()
    {
        return false;
    }

    public function getPageTypeComposerControlPageNameValue(Page $c)
    {
        return false;
    }

    public static function getList(Type $pagetype)
    {
        $sets = PageTypeComposerFormLayoutSet::getList($pagetype);
        $controls = array();
        foreach ($sets as $s) {
            $setControls = PageTypeComposerFormLayoutSetControl::getList($s);
            foreach ($setControls as $sc) {
                $cnt = $sc->getPageTypeComposerControlObject();
                $cnt->setPageTypeComposerFormLayoutSetControlObject($sc);
                $cnt->setPageTypeComposerFormControlRequired($sc->isPageTypeComposerFormLayoutSetControlRequired());
                $controls[] = $cnt;
            }
        }

        return $controls;
    }

    public function isPageTypeComposerControlRequiredByDefault()
    {
        return $this->ptComposerControlRequiredByDefault;
    }

    public function objectExists()
    {
        return true;
    }
}
