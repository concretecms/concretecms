<?php
namespace Concrete\Controller\Element\Dashboard\Express\Entries;

use Concrete\Core\Controller\ElementController;

/**
 * @since 8.0.0
 */
class Header extends ElementController
{
    protected $entity;
    protected $page;
    protected $createURL;
    /**
     * @since 8.2.0
     */
    protected $exportURL;

    /**
     * @return mixed
     */
    public function getCreateURL()
    {
        return $this->createURL;
    }

    /**
     * @return string
     * @since 8.2.0
     */
    public function getExportURL()
    {
        return $this->exportURL;
    }

    /**
     * @param mixed $createURL
     */
    public function setCreateURL($createURL)
    {
        $this->createURL = $createURL;
    }

    /**
     * @param string $exportURL
     * @since 8.2.0
     */
    public function setExportURL($exportURL)
    {
        $this->exportURL = $exportURL;
    }

    public function __construct($entity, $page = null)
    {
        parent::__construct();
        $this->entity = $entity;
        
        if ($page != null) {
            $this->page = $page;
            $this->setCreateURL(\URL::to($page->getCollectionPath(), 'create_entry', $entity->getID()));
            $this->setExportURL(\URL::to($page->getCollectionPath(), 'csv_export', $entity->getEntityResultsNodeId()));
        }
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    public function getElement()
    {
        return 'dashboard/express/entries/header';
    }

    public function view()
    {
        $this->set('entity', $this->getEntity());
        $this->set('createURL', $this->getCreateURL());
        $this->set('exportURL', $this->getExportURL());
    }
}
