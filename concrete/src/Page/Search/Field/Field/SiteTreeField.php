<?php
namespace Concrete\Core\Page\Search\Field\Field;

use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Facade;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Entity\Site\SiteTree;

class SiteTreeField extends AbstractField
{
    /**
     * Initialize the instance.
     *
     * @param string|null $keywords the site tree to be searched
     */
    public function __construct($siteTreeID = null)
    {
        if ($siteTreeID) {
            $this->data['siteTreeID'] = $siteTreeID;
        }
    }

    /**
     * The list of all the request variables.
     *
     * @var array
     */
    protected $requestVariables = [
        'siteTreeID',
    ];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Search\Field\FieldInterface::getKey()
     */
    public function getKey()
    {
        return 'site_tree';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Search\Field\FieldInterface::getDisplayName()
     */
    public function getDisplayName()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Search\Field\FieldInterface::filterList()
     */
    public function filterList(ItemList $list)
    {
        $siteTreeID = $this->data['siteTreeID'];
        $app = Facade::getFacadeApplication();
        $em = $app->make(EntityManager::class);
        $tree = $em->find(SiteTree::class, $siteTreeID);
        if ($tree) {
            $list->setSiteTreeObject($tree);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Search\Field\AbstractField::renderSearchField()
     */
    public function renderSearchField()
    {
        return false;
    }
}
