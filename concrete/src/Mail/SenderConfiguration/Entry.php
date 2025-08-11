<?php

declare(strict_types=1);

namespace Concrete\Core\Mail\SenderConfiguration;

defined('C5_EXECUTE') or die('Access Denied.');

final class Entry
{
    /**
     * Neither the email address nor the sender name are required.
     *
     * @var int
     */
    public const REQUIRED_NO = 0;

    /**
     * The email address is required, the sender name is optional.
     *
     * @var int
     */
    public const REQUIRED_EMAIL = 0b1;

    /**
     * Both the email address and the sender name are required.
     *
     * @var int
     */
    public const REQUIRED_EMAIL_AND_NAME = 0b10 | self::REQUIRED_EMAIL;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $emailKey;

    /**
     * @var string
     */
    private $nameKey = '';

    /**
     * @var string
     */
    private $packageHandle = '';

    /**
     * @var int
     */
    private $priority = 0;

    /**
     * @var int
     */
    private $required = self::REQUIRED_NO;

    /**
     * @var string
     */
    private $notes = '';

    public function __construct(string $name, string $emailKey)
    {
        $this->name = $name;
        $this->emailKey = $emailKey;
    }

    /**
     * @return $this
     */
    public function setName(string $value): self
    {
        $this->name = $value;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setEmailKey(string $value): self
    {
        $this->emailKey = $value;

        return $this;
    }

    public function getEmailKey(): string
    {
        return $this->emailKey;
    }

    /**
     * @return $this
     */
    public function setNameKey(string $value): self
    {
        $this->nameKey = $value;

        return $this;
    }

    public function getNameKey(): string
    {
        return $this->nameKey;
    }

    /**
     * @return $this
     */
    public function setPackageHandle(string $value): self
    {
        $this->packageHandle = $value;

        return $this;
    }

    public function getPackageHandle(): string
    {
        return $this->packageHandle;
    }

    /**
     * @return $this
     */
    public function setPriority(int $value): self
    {
        $this->priority = $value;

        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @return $this
     */
    public function setRequired(int $value): self
    {
        $this->required = $value;

        return $this;
    }

    public function getRequired(): int
    {
        return $this->required;
    }

    /**
     * @return $this
     */
    public function setNotes(string $value): self
    {
        $this->notes = $value;
        
        return $this;
    }
    
    public function getNotes(): string
    {
        return $this->notes;
    }
}
