<?php

namespace Concrete\Core\Form\Service\DestinationPicker;

use ArrayAccess;
use Concrete\Core\Form\Service\Widget\PageSelector;
use Concrete\Core\Page\Page;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Symfony\Component\HttpFoundation\ParameterBag;

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
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::getPostName()
     */
    public function getPostName($key, array $options)
    {
        return empty($options['postName']) ? "{$key}_cid" : $options['postName'];
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
    public function generate($key, array $options, $selectedValue = null)
    {
        $postName = $this->getPostName($key, $options);
        if (is_object($selectedValue)) {
            $pageID = (int) $selectedValue->getCollectionID();
        } elseif (empty($selectedValue)) {
            $pageID = 0;
        } else {
            $pageID = (int) $selectedValue;
        }

        return $this->pageSelector->selectPage($postName, $pageID ?: null);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::decode()
     */
    public function decode(ParameterBag $data, $key, array $options, ArrayAccess $errors = null)
    {
        $result = null;
        $postName = $this->getPostName($key, $options);
        $postValue = $data->get($postName);
        if ($this->numbers->integer($postValue, 1)) {
            $postValue = (int) $postValue;
            $page = Page::getByID($postValue);
            if (!$page || $page->isError()) {
                if ($errors !== null) {
                    $errors[] = t('Unable to find the specified page.');
                }
            } else {
                $result = $postValue;
            }
        }

        return $result;
    }
}
