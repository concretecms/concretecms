<?php
namespace Concrete\Core\Multilingual\Page\Section\Processor;

use Concrete\Core\Foundation\Processor\TargetInterface;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Stack\StackList;
use Serializable;

defined('C5_EXECUTE') or die("Access Denied.");

class MultilingualProcessorTarget implements TargetInterface, Serializable
{
    /**
     * @var \Concrete\Core\Multilingual\Page\Section\Section
     */
    protected $section;

    public function __construct(Section $section)
    {
        $this->section = $section;
    }

    /**
     * @return Section
     */
    public function getSection()
    {
        return $this->section;
    }

    public function getItems()
    {
        $pages = $this->section->populateRecursivePages(array(), array(
            'cID' => $this->section->getCollectionID(), ),
            $this->section->getCollectionParentID(), 0, false
        );

        // Add in all the stack pages found for the current locale.
        $list = new StackList();
        $list->filterByLanguageSection($this->getSection());
        $results = $list->get();
        foreach ($results as $result) {
            $pages[] = array('cID' => $result->getCollectionID());
        }

        return $pages;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        $section = $this->getSection();

        return serialize([(int) $section->getCollectionID(), (int) $section->getVersionID()]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list($sectionID, $versionID) =  unserialize($serialized);

        $this->section = Section::getByID($sectionID, $versionID);
    }
}
