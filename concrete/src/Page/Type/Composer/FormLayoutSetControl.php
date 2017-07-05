<?php
namespace Concrete\Core\Page\Type\Composer;

use Concrete\Core\Entity\Page\Template;
use Concrete\Core\Backup\ContentExporter;
use Concrete\Core\Foundation\Object;
use Concrete\Core\Page\Type\Composer\OutputControl as PageTypeComposerOutputControl;
use Concrete\Core\Page\Type\Composer\FormLayoutSet as PageTypeComposerFormLayoutSet;
use Concrete\Core\Page\Type\Composer\Control\Type\Type as PageTypeComposerControlType;
use Concrete\Core\Support\Facade\Application;

class FormLayoutSetControl extends Object
{
    protected $ptTargetParentPageID = 0;

    public function getPageTypeComposerFormLayoutSetControlID()
    {
        return $this->ptComposerFormLayoutSetControlID;
    }
    public function getPageTypeComposerFormLayoutSetID()
    {
        return $this->ptComposerFormLayoutSetID;
    }
    public function getPageTypeComposerControlTypeID()
    {
        return $this->ptComposerControlTypeID;
    }
    public function getPageTypeComposerControlObject()
    {
        return $this->ptComposerControlObject;
    }
    public function getPageTypeComposerFormLayoutSetControlDisplayOrder()
    {
        return $this->ptComposerFormLayoutSetControlDisplayOrder;
    }
    public function getPageTypeComposerControlTypeObject()
    {
        return PageTypeComposerControlType::getByID($this->ptComposerControlTypeID);
    }
    public function getPageTypeComposerFormLayoutSetObject()
    {
        return PageTypeComposerFormLayoutSet::getByID($this->ptComposerFormLayoutSetID);
    }
    public function getPageTypeComposerFormLayoutSetControlCustomLabel()
    {
        return $this->ptComposerFormLayoutSetControlCustomLabel;
    }
    public function getPageTypeComposerFormLayoutSetControlCustomTemplate()
    {
        return $this->ptComposerFormLayoutSetControlCustomTemplate;
    }
    public function getPageTypeComposerFormLayoutSetControlDescription()
    {
        return $this->ptComposerFormLayoutSetControlDescription;
    }
    public function isPageTypeComposerFormLayoutSetControlRequired()
    {
        return $this->ptComposerFormLayoutSetControlRequired;
    }

    public function getPageTypeComposerFormLayoutSetControlCustomDisplayLabel($format = 'html')
    {
        $value = tc('PageTypeComposerFormLayoutSetControlCustomLabel', $this->getPageTypeComposerFormLayoutSetControlCustomLabel());
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }
    public function getPageTypeComposerFormLayoutSetControlDisplayDescription($format = 'html')
    {
        $value = tc('PageTypeComposerFormLayoutSetControlDescription', $this->getPageTypeComposerFormLayoutSetControlDescription());
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }
    public function setPageObject($page)
    {
        $this->page = $page;
    }

    public function setTargetParentPageID($ptTargetParentPageID)
    {
        $this->ptTargetParentPageID = $ptTargetParentPageID;
    }

    public function render()
    {
        $control = $this->getPageTypeComposerControlObject();
        $control->setPageTypeComposerFormLayoutSetControlObject($this);
        $control->setPageObject(isset($this->page) ? $this->page : null);
        $control->setTargetParentPageID($this->ptTargetParentPageID);
        $control->setPageTypeComposerFormControlRequired($this->isPageTypeComposerFormLayoutSetControlRequired());
        $control->render($this->getPageTypeComposerControlDisplayLabel(), $this->getPageTypeComposerFormLayoutSetControlCustomTemplate(), $this->getPageTypeComposerFormLayoutSetControlDisplayDescription());
    }

    public function getPageTypeComposerControlLabel()
    {
        if ($this->getPageTypeComposerFormLayoutSetControlCustomLabel()) {
            return $this->getPageTypeComposerFormLayoutSetControlCustomLabel();
        } else {
            $control = $this->getPageTypeComposerControlObject();

            return $control->getPageTypeComposerControlName();
        }
    }
    public function getPageTypeComposerControlDisplayLabel($format = 'html')
    {
        $result = $this->getPageTypeComposerFormLayoutSetControlCustomDisplayLabel($format);
        if (!$result) {
            $control = $this->getPageTypeComposerControlObject();
            $result = $control->getPageTypeComposerControlDisplayName($format);
        }

        return $result;
    }

    public function ensureOutputControlExists()
    {
        $set = $this->getPageTypeComposerFormLayoutSetObject();
        $pt = $set->getPageTypeObject();
        $pagetemplates = $pt->getPageTypePageTemplateObjects();
        foreach ($pagetemplates as $pt) {
            $outputcontrol = PageTypeComposerOutputControl::getByPageTypeComposerFormLayoutSetControl($pt, $this);
            if (!is_object($outputcontrol)) {
                PageTypeComposerOutputControl::add($this, $pt);
            }
        }
    }

    public function export($fxml)
    {
        $node = $fxml->addChild('control');
        $node->addAttribute('custom-template', $this->getPageTypeComposerFormLayoutSetControlCustomTemplate());
        if ($this->isPageTypeComposerFormLayoutSetControlRequired()) {
            $node->addAttribute('required', true);
        }
        $node->addAttribute('custom-label', $this->getPageTypeComposerFormLayoutSetControlCustomLabel());
        $node->addAttribute('description', $this->getPageTypeComposerFormLayoutSetControlDescription());
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        $cnt = $db->fetchColumn('select count(*) from PageTypeComposerOutputControls where ptComposerFormLayoutSetControlID = ?', [$this->ptComposerFormLayoutSetControlID]);
        if ($cnt > 0) {
            $ptComposerControlTemporaryID = $app->make('helper/validation/identifier')->getString(8);
            ContentExporter::addPageTypeComposerOutputControlID($this, $ptComposerControlTemporaryID);
            $node->addAttribute('output-control-id', $ptComposerControlTemporaryID);
        }
        $typeo = $this->getPageTypeComposerControlTypeObject();
        $node->addAttribute('type', $typeo->getPageTypeComposerControlTypeHandle());
        $to = $this->getPageTypeComposerControlObject();
        $to->export($node);
    }

    public static function getList(PageTypeComposerFormLayoutSet $set)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        $rows = $db->fetchAll(
            'select ptComposerFormLayoutSetControlID from PageTypeComposerFormLayoutSetControls where ptComposerFormLayoutSetID = ? order by ptComposerFormLayoutSetControlDisplayOrder asc',
            [$set->getPageTypeComposerFormLayoutSetID()]
        );
        $list = [];
        foreach ($rows as $row) {
            $control = static::getByID($row['ptComposerFormLayoutSetControlID']);
            if (is_object($control)) {
                $list[] = $control;
            }
        }

        return $list;
    }

    public static function getByID($ptComposerFormLayoutSetControlID)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        $r = $db->fetchAssoc('select * from PageTypeComposerFormLayoutSetControls where ptComposerFormLayoutSetControlID = ?', [$ptComposerFormLayoutSetControlID]);
        if ($r && $r['ptComposerFormLayoutSetControlID']) {
            $control = new static();
            $control->setPropertiesFromArray($r);
            $control->ptComposerControlObject = @unserialize($r['ptComposerControlObject']);
            if ($control->ptComposerControlObject === false || !$control->ptComposerControlObject->objectExists()) {
                $control->delete();

                return null;
            } else {
                $control->ptComposerControlObject->setPageTypeComposerFormLayoutSetControlObject($control);
            }

            return $control;
        }
    }

    public function updateFormLayoutSetControlDisplayOrder($displayOrder)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        $db->executeQuery(
            'update PageTypeComposerFormLayoutSetControls set ptComposerFormLayoutSetControlDisplayOrder = ? where ptComposerFormLayoutSetControlID = ?',
            [$displayOrder, $this->ptComposerFormLayoutSetControlID]
        );
        $this->ptComposerFormLayoutSetControlDisplayOrder = $displayOrder;
    }

    public function updateFormLayoutSetControlCustomLabel($label)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        $db->executeQuery(
            'update PageTypeComposerFormLayoutSetControls set ptComposerFormLayoutSetControlCustomLabel = ? where ptComposerFormLayoutSetControlID = ?',
            [$label, $this->ptComposerFormLayoutSetControlID]
        );
        $this->ptComposerFormLayoutSetControlCustomLabel = $label;
    }

    public function updateFormLayoutSetControlRequired($required)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        $db->executeQuery(
            'update PageTypeComposerFormLayoutSetControls set ptComposerFormLayoutSetControlRequired = ? where ptComposerFormLayoutSetControlID = ?',
            [intval($required), $this->ptComposerFormLayoutSetControlID]
        );
        $this->ptComposerFormLayoutSetControlRequired = $required;
    }

    public function updateFormLayoutSetControlCustomTemplate($template)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        $db->executeQuery(
            'update PageTypeComposerFormLayoutSetControls set ptComposerFormLayoutSetControlCustomTemplate = ? where ptComposerFormLayoutSetControlID = ?',
            [$template, $this->ptComposerFormLayoutSetControlID]
        );
        $this->ptComposerFormLayoutSetControlCustomTemplate = $template;
    }

    public function updateFormLayoutSetControlDescription($description)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        $db->executeQuery('update PageTypeComposerFormLayoutSetControls set ptComposerFormLayoutSetControlDescription = ? where ptComposerFormLayoutSetControlID = ?',
            [$description, $this->ptComposerFormLayoutSetControlID]
        );
        $this->ptComposerFormLayoutSetControlDescription = $description;
    }

    /*
     * @return \Concrete\Core\Page\Type\Composer\OutputControl
     */
    public function getPageTypeComposerOutputControlObject(Template $pt)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        $ptComposerOutputControlID = $db->fetchColumn(
            'select ptComposerOutputControlID from PageTypeComposerOutputControls where ptComposerFormLayoutSetControlID = ? and pTemplateID = ?',
            [$this->ptComposerFormLayoutSetControlID, $pt->getPageTemplateID()]
        );
        if ($ptComposerOutputControlID) {
            return PageTypeComposerOutputControl::getByID($ptComposerOutputControlID);
        }
    }

    public function delete()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        $db->executeQuery('delete from PageTypeComposerFormLayoutSetControls where ptComposerFormLayoutSetControlID = ?', [$this->ptComposerFormLayoutSetControlID]);
        $db->executeQuery('delete from PageTypeComposerOutputControls where ptComposerFormLayoutSetControlID = ?', [$this->ptComposerFormLayoutSetControlID]);
        $set = $this->getPageTypeComposerFormLayoutSetObject();
        $set->rescanFormLayoutSetControlDisplayOrder();
    }

    public function duplicate(FormLayoutSet $set)
    {
        $control = $this->getPageTypeComposerControlObject();
        $new = $control->addToPageTypeComposerFormLayoutSet($set);
        $new->updateFormLayoutSetControlRequired($this->isPageTypeComposerFormLayoutSetControlRequired());
        $new->updateFormLayoutSetControlDescription($this->getPageTypeComposerFormLayoutSetControlDescription());
        $new->updateFormLayoutSetControlCustomTemplate($this->getPageTypeComposerFormLayoutSetControlCustomTemplate());
        $new->updateFormLayoutSetControlCustomLabel($this->getPageTypeComposerFormLayoutSetControlCustomLabel());

        return $new;
    }
}
