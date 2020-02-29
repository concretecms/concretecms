<?php

namespace Concrete\Core\File\Import;

use Concrete\Core\File\Import\Processor\PostProcessorInterface;
use Concrete\Core\File\Import\Processor\PreProcessorInterface;
use Concrete\Core\File\Import\Processor\ProcessorInterface;
use Concrete\Core\File\Import\Processor\ValidatorInterface;

class ProcessorManager
{
    /**
     * The list of registered processors.
     *
     * @var \Concrete\Core\File\Import\Processor\ProcessorInterface[]
     */
    private $registeredProcessors = [];

    /**
     * The list of validators.
     *
     * @var \Concrete\Core\File\Import\Processor\ValidatorInterface[]|null
     */
    private $validators;

    /**
     * The list of pre-processors.
     *
     * @var \Concrete\Core\File\Import\Processor\PreProcessorInterface[]|null
     */
    private $preProcessors;

    /**
     * The list of post-processors.
     *
     * @var \Concrete\Core\File\Import\Processor\PostProcessorInterface[]|null
     */
    private $postProcessors;

    /**
     * Register a processor.
     *
     * @param \Concrete\Core\File\Import\Processor\ProcessorInterface $processor
     *
     * @return $this
     */
    public function registerProcessor(ProcessorInterface $processor)
    {
        $this->registeredProcessors[] = $processor;
        $this->postProcessors = $this->preProcessors = $this->validators = null;

        return $this;
    }

    /**
     * Unregister a registered processor.
     *
     * @param \Concrete\Core\File\Import\Processor\ProcessorInterface $processor
     *
     * @return $this
     */
    public function unregisterProcessor(ProcessorInterface $processor)
    {
        $index = array_search($processor, $this->registeredProcessors, true);
        if ($index !== false) {
            array_splice($this->registeredProcessors, $index, 1);
            $this->postProcessors = $this->preProcessors = $this->validators = null;
        }

        return $this;
    }

    /**
     * Get the list of all registered processors.
     *
     * @return \Concrete\Core\File\Import\Processor\ProcessorInterface[]
     */
    public function getRegisteredProcessors()
    {
        return $this->registeredProcessors;
    }

    /**
     * Get the list of registered validators.
     *
     * @return \Concrete\Core\File\Import\Processor\ValidatorInterface[]
     */
    public function getValidators()
    {
        if ($this->validators === null) {
            $list = array_filter($this->getRegisteredProcessors(), function (ProcessorInterface $processor) {
                return $processor instanceof ValidatorInterface;
            });
            usort($list, function (ValidatorInterface $a, ValidatorInterface $b) {
                return $b->getValidationPriority() - $a->getValidationPriority();
            });
            $this->validators = $list;
        }

        return $this->validators;
    }

    /**
     * Get the list of registered pre-processors.
     *
     * @return \Concrete\Core\File\Import\Processor\PreProcessorInterface[]
     */
    public function getPreProcessors()
    {
        if ($this->preProcessors === null) {
            $list = array_filter($this->getRegisteredProcessors(), function (ProcessorInterface $processor) {
                return $processor instanceof PreProcessorInterface;
            });
            usort($list, function (PreProcessorInterface $a, PreProcessorInterface $b) {
                return $b->getPreProcessPriority() - $a->getPreProcessPriority();
            });
            $this->preProcessors = $list;
        }

        return $this->preProcessors;
    }

    /**
     * Get the list of registered post-processors.
     *
     * @return \Concrete\Core\File\Import\Processor\PostProcessorInterface[]
     */
    public function getPostProcessors()
    {
        if ($this->postProcessors === null) {
            $list = array_filter($this->getRegisteredProcessors(), function (ProcessorInterface $processor) {
                return $processor instanceof PostProcessorInterface;
            });
            usort($list, function (PostProcessorInterface $a, PostProcessorInterface $b) {
                return $b->getPostProcessPriority() - $a->getPostProcessPriority();
            });
            $this->postProcessors = $list;
        }

        return $this->postProcessors;
    }
}
