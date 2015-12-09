<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Controller\Element\Attribute\AddKey;
use Concrete\Controller\Element\Attribute\KeyList;
use Concrete\Core\Attribute\EntityInterface;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Controller\ElementController;
use Concrete\Core\Error\Error;
use Concrete\Core\Validation\CSRF\Token;
use Loader;

abstract class DashboardAttributesPageController extends DashboardPageController
{

    public function renderList($keys, $types)
    {
        $list = new KeyList();
        $list->setAttributes($keys);
        $list->setAttributeTypes($types);
        $list->setDashboardPagePath($this->getPageObject()->getcollectionPath());
        $list->setDashboardPageParameters($this->getRequestActionParameters());
        $this->set('attributeView', $list);
        $this->set('pageTitle', t('Attributes'));
    }

    public function renderAdd($type, $backURL)
    {
        $add = new AddKey($type);
        $add->setBackButtonURL($backURL);
        $add->setDashboardPageParameters($this->getRequestActionParameters());
        $this->set('attributeView', $add);
        $this->set('pageTitle', t('Add Attribute'));
    }

    protected function executeAdd(EntityInterface $entity, Type $type)
    {
        $controller = $type->getController();
        $e = $controller->validateKey($this->request->request->all());
        if ($e->has()) {
            $this->error = $e;
        } else {
            /**
             * @var $category \Concrete\Core\Attribute\Category\CategoryInterface
             */
            $category = $entity->getAttributeKeyCategory();
            $category->setEntity($entity);
            $builder = \Core::make('Concrete\Core\Attribute\Key\Builder');
            $builder->addFromRequest($category, $type, $this->request);
            $this->redirect('/dashboard/pages/attributes/', 'attribute_created');
        }
    }


}