<?php
namespace Concrete\Core\Form\Context\Registry;

use Concrete\Core\Form\Context\ContextInterface;

/**
 * A simple class for registering form context to actual implemented classes.
 */
class ContextRegistry
{

    /**
     * @var ContextEntry[]
     */
    protected $entries = [];

    public function getContext(ContextInterface $context)
    {
        $class = get_class($context);
        foreach($this->entries as $entry) {
            if ($class == $entry->getContext()) {
                return call_user_func(
                    [new \ReflectionClass($entry->getContextToReturn()), 'newInstance']
                );
            }
        }
        return $context;
    }

    public function register($context, $return)
    {
        $entry = new ContextEntry($context, $return);
        $this->addOrReplaceEntry($entry);
    }

    protected function addOrReplaceEntry(ContextEntry $replace)
    {
        $index = null;
        foreach($this->entries as $key => $entry) {
            if ($entry->getContext() == $replace->getContext()) {
                $index = $key;
            }
        }

        if ($index) {
            $this->entries[$index] = $replace;
        } else {
            $this->entries[] = $replace;
        }
    }

}
