<?php

namespace Concrete\Core\Express\Entry\Formatter;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Formatter\FormatterInterface;

class LabelFormatter implements EntryFormatterInterface
{

    /** @var \Concrete\Core\Express\Formatter\FormatterInterface */
    protected $formatter;

    public function __construct(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * Format a mask given an entry
     * @param string $mask
     * @param \Concrete\Core\Entity\Express\Entry $entry
     * @return string|null
     */
    public function format($mask, Entry $entry)
    {
        return $this->formatter->format($mask, function($key) use ($entry) {
            $attribute = $entry->getAttribute($key);
            if ($attribute) {
                return $attribute;
            }

            $association = $entry->getAssociation($key);
            if (is_object($association)) {
                return $association->getSelectedEntry()->getLabel();
            }
        });
    }

    public function getLabel(Entry $entry)
    {
        foreach($entry->getEntity()->getAttributes() as $ak) {
            if ($ak->getAttributeType()->getAttributeTypeHandle() == 'text') {
                return $entry->getAttribute($ak);
            }
        }

    }

}
