<?php

namespace Concrete\Core\Page;

use Concrete\Core\User\User;

/**
 * A class representing the options for the page/collection/version Cloner.
 *
 * @since concrete5 8.5.0a2
 */
class ClonerOptions
{
    /**
     * The currently logged in user.
     *
     * @var \Concrete\Core\User\User
     */
    protected $currentUser;

    /**
     * Should the original author be kept?
     *
     * @var bool
     */
    private $keepOriginalAuthor = false;

    /**
     * Mark the copied data as not approved?
     *
     * @var bool
     */
    private $forceUnapproved = false;

    /**
     * Should the collection version attributes be copied?
     *
     * @var bool
     */
    private $copyAttributes = true;

    /**
     * Should the page type composer output block records be copied?
     *
     * @var bool
     */
    private $copyPageTypeComposerOutputBlocks = true;

    /**
     * Should the custom theme styles be copied?
     *
     * @var bool
     */
    private $copyCustomStyles = true;

    /**
     * Should the collection version contents (blocks/areas/...) be copied?
     *
     * @var bool
     */
    private $copyContents = true;

    /**
     * The version comments (if empty string, automatically build them).
     *
     * @var string
     */
    private $versionComments = '';

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\User\User $currentUser the currently logged in user
     */
    public function __construct(User $currentUser)
    {
        $this->currentUser = $currentUser;
    }

    /**
     * Get the currently logged in user.
     *
     * @return \Concrete\Core\User\User
     */
    public function getCurrentUser()
    {
        return $this->currentUser;
    }

    /**
     * Should the original author be kept?
     *
     * @return bool
     */
    public function keepOriginalAuthor()
    {
        return $this->keepOriginalAuthor;
    }

    /**
     * Should the original author be kept?
     *
     * @param bool $value
     *
     * @return $this
     */
    public function setKeepOriginalAuthor($value)
    {
        $this->keepOriginalAuthor = (bool) $value;

        return $this;
    }

    /**
     * Mark the copied data as not approved?
     *
     * @return bool
     */
    public function forceUnapproved()
    {
        return $this->forceUnapproved;
    }

    /**
     * Mark the copied data as not approved?
     *
     * @param bool $value
     *
     * @return $this
     */
    public function setForceUnapproved($value)
    {
        $this->forceUnapproved = (bool) $value;

        return $this;
    }

    /**
     * Should the collection version attributes be copied?
     *
     * @return bool
     */
    public function copyAttributes()
    {
        return $this->copyAttributes;
    }

    /**
     * Should the collection version attributes be copied?
     *
     * @param bool $value
     *
     * @return $this
     */
    public function setCopyAttributes($value)
    {
        $this->copyAttributes = (bool) $value;

        return $this;
    }

    /**
     * Should the page type composer output block records be copied?
     *
     * @return bool
     */
    public function copyPageTypeComposerOutputBlocks()
    {
        return $this->copyPageTypeComposerOutputBlocks;
    }

    /**
     * Should the page type composer output block records be copied?
     *
     * @param bool $copyPageTypeComposerOutputBlocks
     */
    public function setCopyPageTypeComposerOutputBlocks($copyPageTypeComposerOutputBlocks)
    {
        $this->copyPageTypeComposerOutputBlocks = (bool) $copyPageTypeComposerOutputBlocks;
    }

    /**
     * Should the custom theme styles be copied?
     *
     * @return bool
     */
    public function copyCustomStyles()
    {
        return $this->copyCustomStyles;
    }

    /**
     * Should the custom theme styles be copied?
     *
     * @param bool $value
     *
     * @return $this
     */
    public function setCopyCustomStyles($value)
    {
        $this->copyCustomStyles = (bool) $value;

        return $this;
    }

    /**
     * Should the collection version contents (blocks/areas/...) be copied?
     *
     * @return bool
     */
    public function copyContents()
    {
        return $this->copyContents;
    }

    /**
     * Should the collection version contents (blocks/areas/...) be copied?
     *
     * @param bool $value
     *
     * @return $this
     */
    public function setCopyContents($value)
    {
        $this->copyContents = (bool) $value;

        return $this;
    }

    /**
     * Get the version comments (if empty string, automatically build it).
     *
     * @var string
     */
    public function getVersionComments()
    {
        return $this->versionComments;
    }

    /**
     * Set the version comments (if empty string, automatically build it).
     *
     * @param string|object $value a string value, or an object implementing the __toString() method
     *
     * @return $this
     */
    public function setVersionComments($value)
    {
        $this->versionComments = (string) $value;

        return $this;
    }
}
