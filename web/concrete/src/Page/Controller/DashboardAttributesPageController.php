<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Controller\Element\Attribute\AddKey;
use Concrete\Controller\Element\Attribute\EditKey;
use Concrete\Controller\Element\Attribute\Form;
use Concrete\Controller\Element\Attribute\Header;
use Concrete\Controller\Element\Attribute\KeyHeader;
use Concrete\Controller\Element\Attribute\KeyList;
use Concrete\Controller\Element\Attribute\StandardListHeader;
use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\EntityInterface;
use Concrete\Core\Attribute\Set;
use Concrete\Core\Entity\Attribute\Category;
use Concrete\Core\Entity\Attribute\SetKey;
use Concrete\Core\Entity\Attribute\Type;
use Concrete\Core\Controller\ElementController;
use Concrete\Core\Error\Error;
use Concrete\Core\Validation\CSRF\Token;
use Loader;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class DashboardAttributesPageController extends DashboardPageController
{

    /**
     * @return EntityInterface
     */
    abstract protected function getCategoryEntityObject();

    public function renderList()
    {
        $entity = $this->getCategoryEntityObject();
        $category = $entity->getAttributeKeyCategory();
        $list = new KeyList();
        $list->setAttributeSets($entity->getAttributeSets());
        $list->setUnassignedAttributeKeys($category->getUnassignedAttributeKeys());
        $list->setAttributeTypes($category->getAttributeTypes());
        $list->setDashboardPagePath($this->getPageObject()->getCollectionPath());
        $list->setDashboardPageParameters($this->getRequestActionParameters());
        if (!$entity->allowAttributeSets()) {
            $list->setEnableSorting(false);
        }

        $header = new StandardListHeader($category);
        $this->set('attributeHeader', $header);

        $this->set('attributeView', $list);
        $this->set('pageTitle', t('Attributes'));
    }

    public function renderAdd($type, $backURL)
    {
        $add = new Form($type);
        $add->setBackButtonURL($backURL);
        $add->setCategory($this->getCategoryEntityObject());
        $add->setDashboardPageParameters($this->getRequestActionParameters());
        $this->set('attributeView', $add);
        $this->set('pageTitle', t('Add Attribute'));
    }

    public function renderEdit($key, $backURL)
    {
        $edit = new EditKey($key);
        $edit->setBackButtonURL($backURL);
        $edit->setCategory($this->getCategoryEntityObject());
        $edit->setDashboardPageParameters($this->getRequestActionParameters());
        $this->set('attributeView', $edit);
        $this->set('pageTitle', t('Edit Attribute'));

        $header = new KeyHeader($key);
        $header->setDashboardPageParameters($this->getRequestActionParameters());
        $this->set('attributeHeader', $header);
    }

    protected function executeAdd(Type $type, $successURL, $onComplete = null)
    {
        $controller = $type->getController();
        $e = $controller->validateKey($this->request->request->all());
        if ($e->has()) {
            $this->error = $e;
        } else {
            /**
             * @var $category \Concrete\Core\Attribute\Category\CategoryInterface
             */
            $entity = $this->getCategoryEntityObject();
            $category = $entity->getAttributeKeyCategory();
            $category->setEntity($entity);
            $key = $category->addFromRequest($type, $this->request);
            $this->assignToSetFromRequest($key);

            if ($onComplete instanceof \Closure) {
                $onComplete();
            }
            $this->flash('success', t('Attribute created successfully.'));
            $this->redirect($successURL);
        }
    }

    protected function assignToSetFromRequest(AttributeKeyInterface $key)
    {
        $request = $this->request;
        $category = $this->getCategoryEntityObject();
        if ($category->allowAttributeSets()) {
            $set = Set::getByID($request->request->get('asID'));
            $setKeys = Set::getByAttributeKey($key);
            if (in_array($set, $setKeys)) {
                return;
            }

            if ($category->allowAttributeSets() == EntityInterface::ASET_ALLOW_SINGLE || !is_object($set)) {
                $query = $this->entityManager->createQuery(
                    'delete from \Concrete\Core\Entity\Attribute\SetKey sk where sk.attribute_key = :attribute_key'
                );
                $query->setParameter('attribute_key', $key);
                $query->execute();
            }

            if (is_object($set)) {

                $this->entityManager->refresh($set);

                // Refresh display order just in case.
                $displayOrder = 0;
                foreach($set->getAttributeKeys() as $setKey) {
                    $setKey->setDisplayOrder($displayOrder);
                    $this->entityManager->persist($setKey);
                    $displayOrder++;
                }

                $setKey = new SetKey();
                $setKey->setAttributeKey($key);
                $setKey->setAttributeSet($set);
                $setKey->setDisplayOrder($displayOrder);
                $this->entityManager->persist($setKey);
            }
        }

        $this->entityManager->flush();

    }


    protected function executeUpdate(AttributeKeyInterface $key, $successURL, $onComplete = null)
    {
        $controller = $key->getController();
        $entity = $this->getCategoryEntityObject();
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
            $this->assignToSetFromRequest($key);
            if ($onComplete instanceof \Closure) {
                $onComplete();
            }
            $this->flash('success', t('Attribute updated successfully.'));
            $this->redirect($successURL);
        }
    }

    protected function executeDelete(AttributeKeyInterface $key, $successURL, $onComplete = null)
    {
        $entity = $this->getCategoryEntityObject();
        try {

            if (!$this->token->validate('delete_attribute')) {
                throw new \Exception($this->token->getErrorMessage());
            }

            $category = $entity->getAttributeKeyCategory();
            $category->setEntity($entity);
            $category->deleteKey($key);

            if ($onComplete instanceof \Closure) {
                $onComplete();
            }

            $this->flash('success', t('Attribute deleted successfully.'));
            $this->redirect($successURL);

        } catch (Exception $e) {
            $this->error = $e;
        }
    }

    public function sort_attribute_set()
    {
        $entity = $this->getCategoryEntityObject();
        if ($entity->allowAttributeSets()) {
            /**
             * @var $category CategoryInterface
             */
            $category = $entity->getAttributeKeyCategory();
            $keys = array();
            foreach((array) $this->request->request->get('akID') as $akID) {
                /**
                 * @var $key AttributeInterface
                 */
                $key = $category->getAttributeKeyByID($akID);
                if (is_object($key)) {
                    $keys[] = $key;
                }
            }


            foreach($entity->getAttributeSets() as $set) {
                if ($set->getAttributeSetID() == $this->request->request->get('asID') && count($keys)) {

                    // Clear the keys
                    foreach($set->getAttributeKeys() as $setKey) {
                        $this->entityManager->remove($setKey);
                    }
                    $this->entityManager->flush();

                    $i = 0;
                    foreach($keys as $key) {
                        $setKey = new SetKey();
                        $setKey->setAttributeKey($key);
                        $setKey->setAttributeSet($set);
                        $setKey->setDisplayOrder($i);
                        $set->getAttributeKeys()->add($setKey);
                        $i++;
                    }
                    break;
                }
            }

            $this->entityManager->persist($set);
            $this->entityManager->flush();
            return new JsonResponse($set);
        }

    }



}