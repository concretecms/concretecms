<?php
namespace Concrete\Core\Service\Configuration\HTTP;

use Concrete\Core\Service\Configuration\GeneratorInterface;
use Concrete\Core\Service\Rule\RuleInterface;

abstract class Generator implements GeneratorInterface
{
    /**
     * @var RuleInterface[]
     */
    protected $rules;

    /**
     * Initializes the instance.
     */
    public function __construct()
    {
        $this->rules = array();
    }

    /**
     * {@inheritdoc}
     *
     * @see GeneratorInterface::addRule()
     */
    public function addRule($handle, RuleInterface $rule)
    {
        $this->rules[$handle] = $rule;
    }

    /**
     * {@inheritdoc}
     *
     * @see GeneratorInterface::getRules()
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * {@inheritdoc}
     *
     * @see GeneratorInterface::getRule()
     */
    public function getRule($handle)
    {
        $rules = $this->getRules();

        return isset($rules[$handle]) ? $rules[$handle] : null;
    }
}
