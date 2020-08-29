<?php

namespace Concrete\Controller\Element\Attribute;

use Concrete\Core\Attribute\CategoryObjectInterface;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Controller\ElementController;
use League\Url\UrlInterface;

class EditableSetList extends ElementController
{
    /**
     * @var CategoryObjectInterface
     */
    protected $categoryEntity;

    /**
     * @var ObjectInterface
     */
    protected $attributedObject;

    /**
     * @var UrlInterface
     */
    protected $editDialogURL;

    /**
     * @var string
     */
    protected $sectionTitle;

    public function __construct(CategoryObjectInterface $categoryEntity, ObjectInterface $attributedObject)
    {
        parent::__construct();

        $this->categoryEntity = $categoryEntity;
        $this->attributedObject = $attributedObject;
    }

    public function getElement()
    {
        return 'attribute/editable_set_list';
    }

    /**
     * @return UrlInterface
     */
    public function getEditDialogURL(): ?UrlInterface
    {
        return $this->editDialogURL;
    }

    /**
     * @param UrlInterface $editDialogURL
     */
    public function setEditDialogURL(UrlInterface $editDialogURL): void
    {
        $this->editDialogURL = $editDialogURL;
    }

    /**
     * @return string
     */
    public function getSectionTitle(): ?string
    {
        return $this->sectionTitle;
    }

    /**
     * @param string $sectionTitle
     */
    public function setSectionTitle(string $sectionTitle): void
    {
        $this->sectionTitle = $sectionTitle;
    }

    public function view()
    {
        $category = $this->categoryEntity->getAttributeKeyCategory();
        $setManager = $category->getSetManager();
        $sets = $setManager->getAttributeSets();
        $unassigned = $setManager->getUnassignedAttributeKeys();

        $this->set('attributeSets', $sets);
        $this->set('unassigned', $unassigned);

        $this->set('attributedObject', $this->attributedObject);
        $this->set('sectionTitle', $this->getSectionTitle());
        $this->set('editDialogURL', $this->getEditDialogURL());
    }
}
