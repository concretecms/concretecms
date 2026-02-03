<?php

declare(strict_types=1);

namespace Concrete\Core\Support\Symbol\CheckerGenerator;

class Method
{
    /**
     * @var $string
     */
    private $name;

    /**
     * @var $string
     */
    private $arguments;

    /**
     * @var bool
     */
    private $deprecated = false;

    /**
     * @var $string[]
     */
    private $descriptions = [];

    /**
     * @var $string[]
     */
    private $forObjectOfClasses = [];

    /**
     * @var $string[]
     */
    private $categoryKeyHandles = [];

    /**
     * @var $string[]
     */
    private $sees = [];

    public function __construct(string $name, string $arguments = '')
    {
        $this->name = $name;
        $this->arguments = $arguments;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getArguments(): string
    {
        return $this->arguments;
    }

    /**
     * @return $this
     */
    public function setDeprecated(bool $value): self
    {
        $this->deprecated = $value;

        return $this;
    }

    public function isDeprecated(): bool
    {
        return $this->deprecated;
    }

    /**
     * @return $this
     */
    public function addDescription(string $value): self
    {
        $value = trim($value);
        if ($value !== '') {
            $value = str_replace("\r", "\n", str_replace("\r\n", "\n", $value));
            if (!in_array($value, $this->descriptions, true)) {
                $this->descriptions[] = $value;
            }
        }

        return $this;
    }

    /**
     * @return string[]
     */
    public function getDescriptions(): array
    {
        return $this->descriptions;
    }

    /**
     * @return $this
     */
    public function addForObjectOfClass(string $value): self
    {
        if ($value !== '' && !in_array($value, $this->forObjectOfClasses, true)) {
            $this->forObjectOfClasses[] = $value;
            sort($this->forObjectOfClasses, SORT_STRING);
        }

        return $this;
    }

    /**
     * @return string[]
     */
    public function getForObjectOfClasses(): array
    {
        return $this->forObjectOfClasses;
    }

    /**
     * @return $this
     */
    public function addCategoryKeyHandle(string $value): self
    {
        if ($value !== '' && !in_array($value, $this->categoryKeyHandles, true)) {
            $this->categoryKeyHandles[] = $value;
            sort($this->categoryKeyHandles, SORT_STRING);
        }

        return $this;
    }

    /**
     * @return string[]
     */
    public function getCategoryKeyHandles(): array
    {
        return $this->categoryKeyHandles;
    }

    /**
     * @return $this
     */
    public function addSee(string $value): self
    {
        if ($value !== '' && !in_array($value, $this->sees, true)) {
            $this->sees[] = $value;
            sort($this->sees, SORT_STRING);
        }

        return $this;
    }

    /**
     * @return string[]
     */
    public function getSees(): array
    {
        return $this->sees;
    }

    public function isCompatibleWith(self $other): bool
    {
        if (strcasecmp($this->getName(), $other->getName()) !== 0) {
            return false;
        }
        if ($this->getArguments() !== $other->getArguments()) {
            return false;
        }

        return true;
    }

    /**
     * @return $this
     */
    public function merge(self $other): self
    {
        if (!$other->isDeprecated()) {
            $this->setDeprecated(false);
        }
        foreach ($other->getDescriptions() as $value) {
            $this->addDescription($value);
        }
        foreach ($other->getForObjectOfClasses() as $value) {
            $this->addForObjectOfClass($value);
        }
        foreach ($other->getCategoryKeyHandles() as $value) {
            $this->addCategoryKeyHandle($value);
        }
        foreach ($other->getSees() as $value) {
            $this->addSee($value);
        }

        return $this;
    }
}
