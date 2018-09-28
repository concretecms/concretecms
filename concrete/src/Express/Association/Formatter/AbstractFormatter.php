<?php
namespace Concrete\Core\Express\Association\Formatter;

use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Entry\Formatter\EntryFormatterInterface;
use Concrete\Core\Support\Facade\Application;

abstract class AbstractFormatter implements FormatterInterface
{
    protected $association;
    protected $entryFormatter;

    public function __construct(Association $association)
    {
        $this->association = $association;
    }

    /**
     * Get the display label for an entry
     * @param \Concrete\Core\Entity\Express\Control\AssociationControl $control
     * @param \Concrete\Core\Entity\Express\Entry $entry
     * @return null|string
     */
    public function getEntryDisplayName(AssociationControl $control, Entry $entry)
    {
        $formatter = $this->getEntryFormatter();
        $name = null;

        // Do we have a custom display mask? If so, we try to use that
        if ($mask = $control->getAssociationEntityLabelMask()) {
            $name = $formatter->format($mask, $entry);
        }

        $name = $entry->getLabel();

        if ($name = trim($name)) {
            return $name;
        }

        return $entry->getID();
    }

    /**
     * Supploy the entry formatter we should use
     * @param \Concrete\Core\Express\Entry\Formatter\EntryFormatterInterface $formatter
     */
    public function setEntryFormatter(EntryFormatterInterface $formatter)
    {
        $this->entryFormatter = $formatter;
    }

    /**
     * Get the entry formatter to use
     * @return \Concrete\Core\Express\Entry\Formatter\EntryFormatterInterface
     */
    protected function getEntryFormatter()
    {
        if (!$this->entryFormatter) {
            $this->entryFormatter = Application::getFacadeApplication()->make(EntryFormatterInterface::class);
        }

        return $this->entryFormatter;
    }

    public function getDisplayName()
    {
        return sprintf(
            '%s > %s', $this->association->getSourceEntity()->getEntityDisplayName(),
            $this->association->getTargetEntity()->getEntityDisplayName()
        );
    }
}
