<?php
namespace Concrete\Controller\Element\Express\Search;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Express\Search\SearchProvider;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\Utility\Service\Url;
use Concrete\Core\Validation\CSRF\Token;

class Menu extends ElementController
{

    /**
     * @var Query
     */
    protected $query;

    /**
     * @var SearchProvider
     */
    protected $searchProvider;

    /**
     * @var string
     */
    protected $createURL;

    /**
     * @var string
     */
    protected $exportURL;


    /**
     * @var Entity
     */
    protected $entity;

    public function __construct(SearchProvider $searchProvider)
    {
        $this->searchProvider = $searchProvider;
    }

    public function getElement()
    {
        return 'express/search/menu';
    }

    /**
     * @param Query $query
     */
    public function setQuery(Query $query): void
    {
        $this->query = $query;
    }

    /**
     * @param Entity $entity
     */
    public function setEntity(Entity $entity): void
    {
        $this->entity = $entity;
    }

    /**
     * @return string
     */
    public function getCreateURL(): string
    {
        return $this->createURL;
    }

    /**
     * @param string $createURL
     */
    public function setCreateURL(string $createURL): void
    {
        $this->createURL = $createURL;
    }

    /**
     * @return string
     */
    public function getExportURL(): string
    {
        return $this->exportURL;
    }

    /**
     * @param string $exportURL
     */
    public function setExportURL(string $exportURL): void
    {
        $this->exportURL = $exportURL;
    }

    public function view()
    {
        $itemsPerPage = (isset($this->query)) ? $this->query->getItemsPerPage() :
            $this->searchProvider->getItemsPerPage();
        $this->set('entity', $this->entity);
        $this->set('itemsPerPage', $itemsPerPage);
        $this->set('createURL', $this->createURL);
        $this->set('exportURL', $this->exportURL);
        $this->set('itemsPerPageOptions', $this->searchProvider->getItemsPerPageOptions());
        $this->set('form', $this->app->make(Form::class));
        $this->set('token', $this->app->make(Token::class));
        $this->set('urlHelper', $this->app->make(Url::class));
        $managePage = \Page::getByPath('/dashboard/system/express/entities');
        $permissions = new \Permissions($managePage);
        if ($permissions->canViewPage()) {
            $this->set('manageURL', \URL::to('/dashboard/system/express/entities', 'view_entity', $this->entity->getID()));
        }
    }

}
