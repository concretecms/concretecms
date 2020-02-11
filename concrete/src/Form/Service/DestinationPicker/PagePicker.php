<?php

namespace Concrete\Core\Form\Service\DestinationPicker;

use ArrayAccess;
use Concrete\Core\Form\Service\Widget\PageSelector;
use Concrete\Core\Page\Page;
use Concrete\Core\Utility\Service\Validation\Numbers;

/**
 * A picker for DestinationPicker that allows users specify a concrete5 page.
 *
 * Supported options for the generate method:
 * - displayName: the display name of this picker (to be used in the SELECT html element).
 *
 * Supported options for the decode method:
 * - none
 */
class PagePicker implements PickerInterface
{
    /**
     * @var \Concrete\Core\Form\Service\Widget\PageSelector
     */
    protected $pageSelector;

    /**
     * @var \Concrete\Core\Utility\Service\Validation\Numbers
     */
    protected $numbers;

    /**
     * @param \Concrete\Core\Form\Service\Widget\PageSelector $pageSelector
     * @param Numbers $numbers
     */
    public function __construct(PageSelector $pageSelector, Numbers $numbers)
    {
        $this->pageSelector = $pageSelector;
        $this->numbers = $numbers;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::getDisplayName()
     */
    public function getDisplayName(array $options)
    {
        return empty($options['displayName']) ? t('Page') : $options['displayName'];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::getHeight()
     */
    public function getHeight()
    {
        return 40;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::generate()
     */
    public function generate($pickerKey, array $options, $selectedValue = null)
    {
        if (is_object($selectedValue)) {
            $pageID = (int) $selectedValue->getCollectionID();
        } elseif (empty($selectedValue)) {
            $pageID = 0;
        } else {
            $pageID = (int) $selectedValue;
        }

        return $this->pageSelector->selectPage($pickerKey, $pageID ?: null);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::decode()
     */
    public function decode(array $data, $pickerKey, array $options, ArrayAccess $errors = null, $fieldDisplayName = null)
    {
        $result = null;
        $postValue = array_get($data, $pickerKey);
        if ($this->numbers->integer($postValue, 1)) {
            $postValue = (int) $postValue;
            $page = Page::getByID($postValue);
            if (!$page || $page->isError()) {
                if ($errors !== null) {
                    if ((string) $fieldDisplayName === '') {
                        $errors[] = t('Unable to find the specified page.');
                    } else {
                        $errors[] = t('Unable to find the page specified for %s.', $fieldDisplayName);
                    }
                }
            } else {
                $result = $postValue;
            }
        }

        return $result;
    }
}
