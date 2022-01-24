<?php
namespace Concrete\Core\Page\Search\Field\Field;

use Concrete\Core\Entity\Page\Container;
use Concrete\Core\Page\PageList;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Doctrine\ORM\EntityManager;

class ContainsContainerField extends AbstractField
{

    protected $requestVariables = [
        'containerID'
    ];

    public function getKey()
    {
        return 'contains_container';
    }

    public function getDisplayName()
    {
        return t('Contains Container');
    }

    /**
     * @param PageList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $em = app(EntityManager::class);
        if ($this->getData('containerID')) {
            $container = $em->find(Container::class, $this->getData('containerID'));
            if ($container) {
                $list->filterByContainer($container);
            }
        }
    }

    public function renderSearchField()
    {
        $form = \Core::make('helper/form');
        $em = app(EntityManager::class);
        $containerSelect = [];
        foreach ($em->getRepository(Container::class)->findBy([], ['containerName' => 'asc']) as $container) {
            $containerSelect[$container->getContainerID()] = $container->getContainerName();
        }
        $html = $form->select('containerID', $containerSelect, $this->getData('containerID'));
        return $html;
    }


}
