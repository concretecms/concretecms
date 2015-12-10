<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Controller\Element\Attribute\AddKey;
use Concrete\Controller\Element\Attribute\EditKey;
use Concrete\Controller\Element\Attribute\Form;
use Concrete\Controller\Element\Attribute\Header;
use Concrete\Controller\Element\Attribute\KeyList;
use Concrete\Core\Attribute\EntityInterface;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\AttributeKey\AttributeKey;
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
        $add = new Form($type);
        $add->setBackButtonURL($backURL);
        $add->setDashboardPageParameters($this->getRequestActionParameters());
        $this->set('attributeView', $add);
        $this->set('pageTitle', t('Add Attribute'));
    }

    public function renderEdit($key, $backURL)
    {
        $add = new EditKey($key);
        $add->setBackButtonURL($backURL);
        $add->setDashboardPageParameters($this->getRequestActionParameters());
        $this->set('attributeView', $add);
        $this->set('pageTitle', t('Add Attribute'));

        $header = new Header($key);
        $header->setDashboardPageParameters($this->getRequestActionParameters());
        $this->set('attributeHeader', $header);
    }

    protected function executeAdd(EntityInterface $entity, Type $type, $successURL)
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
            $category->addFromRequest($type, $this->request);
            $this->flash('success', t('Attribute created successfully.'));
            $this->redirect($successURL);
        }
    }

    protected function executeUpdate(EntityInterface $entity, AttributeKey $key, $successURL)
    {
        $controller = $key->getController();
        $e = $controller->validateKey($this->request->request->all());
        if ($e->has()) {
            $this->error = $e;
        } else {
            /**
             * @var $category \Concrete\Core\Attribute\Category\CategoryInterface
             */
            $category = $entity->getAttributeKeyCategory();
            $category->setEntity($entity);
            $category->updateFromRequest($key, $this->request);
            $this->flash('success', t('Attribute updated successfully.'));
            $this->redirect($successURL);
        }
    }

    protected function executeDelete(EntityInterface $entity, AttributeKey $key, $successURL)
    {
        try {

            if (!$this->token->validate('delete_attribute')) {
                throw new \Exception($this->token->getErrorMessage());
            }

            $category = $entity->getAttributeKeyCategory();
            $category->setEntity($entity);
            $category->delete($key);

            $this->flash('success', t('Attribute deleted successfully.'));
            $this->redirect($successURL);

        } catch (Exception $e) {
            $this->error = $e;
        }
    }



}