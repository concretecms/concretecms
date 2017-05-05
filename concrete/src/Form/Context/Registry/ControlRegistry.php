<?php
namespace Concrete\Core\Form\Context\Registry;

use Concrete\Core\Application\Application;
use Concrete\Core\Express\Form\Context\FormContext;
use Concrete\Core\Express\Form\Context\ViewContext;
use Concrete\Core\Express\Form\Control\View\AssociationFormView;
use Concrete\Core\Express\Form\Control\View\AssociationView;
use Concrete\Core\Form\Context\ContextInterface;
use Concrete\Core\Express\Form\Control\View\AttributeKeyFormView;
use Concrete\Core\Express\Form\Control\View\AttributeKeyView;

/**
 * A simple class for registering context to view bindings, in the event that certain contexts ought to
 * deliver different views. (Used by Express Attribute Key View vs Form)
 */
class ControlRegistry
{

    public function __construct()
    {
        $this->registerControl('express_control_attribute_key', [
            [new FormContext(), AttributeKeyFormView::class],
            [new ViewContext(), AttributeKeyView::class],
        ]);
        $this->registerControl('express_control_association', [
            [new FormContext(), AssociationFormView::class],
            [new ViewContext(), AssociationView::class],
        ]);

    }

    /**
     * @var ControlEntry[]
     */
    protected $entries = [];

    public function registerControl($handle, $entries)
    {
        foreach($entries as $row) {
            $this->register($row[0], $handle, $row[1]);
        }
    }

    public function register(ContextInterface $context, $handle, $viewClass)
    {
        $entry = new ControlEntry($context, $handle, $viewClass);
        $this->addOrReplaceEntry($entry);
    }

    protected function addOrReplaceEntry(ControlEntry $entry)
    {
        $index = null;
        foreach($this->entries as $key => $existingEntry) {
            if ($entry->getHandle() == $existingEntry->getHandle()) {
                if (get_class($existingEntry->getContext()) == get_class($entry->getContext())) {
                    $index = $key;
                }
            }
        }
        if ($index) {
            $this->entries[$index] = $entry;
        } else {
            $this->entries[] = $entry;
        }
    }

    protected function getEntryFromContext(ContextInterface $context, $handle)
    {
        foreach($this->entries as $key => $existingEntry) {
            if ($handle == $existingEntry->getHandle()) {
                $entryContext = $existingEntry->getContext();
                if ($context instanceof $entryContext) {
                    return $existingEntry;
                }
            }
        }
    }

    public function getControlView(ContextInterface $context, $handle, $arguments = array())
    {
        $entry = $this->getEntryFromContext($context, $handle);
        array_unshift($arguments, $context);

        if (is_object($entry)) {
            return call_user_func_array(
                [new \ReflectionClass($entry->getViewClass()), 'newInstance'],
                $arguments
            );
        }
    }

}
