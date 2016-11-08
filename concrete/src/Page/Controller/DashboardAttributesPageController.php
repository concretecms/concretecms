<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Controller\Element\Attribute\EditKey;
use Concrete\Controller\Element\Attribute\Form;
use Concrete\Controller\Element\Attribute\KeyHeader;
use Concrete\Controller\Element\Attribute\KeyList;
use Concrete\Controller\Element\Attribute\StandardListHeader;
use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\CategoryObjectInterface;
use Concrete\Core\Attribute\Set;
use Concrete\Core\Attribute\StandardSetManager;
use Concrete\Core\Entity\Attribute\Category;
use Concrete\Core\Entity\Attribute\SetKey;
use Concrete\Core\Entity\Attribute\Type;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class DashboardAttributesPageController extends DashboardPageController
{
    /**
     * @return CategoryObjectInterface
     */
    abstract protected function getCategoryObject();

    public function renderList()
    {
        $entity = $this->getCategoryObject();
        $category = $entity->getAttributeKeyCategory();
        $list = new KeyList();
        $list->setAttributeSets($category->getSetManager()->getAttributeSets());
        $list->setUnassignedAttributeKeys($category->getSetManager()->getUnassignedAttributeKeys());
        $list->setAttributeTypes($category->getAttributeTypes());
        $list->setDashboardPagePath($this->getPageObject()->getCollectionPath());
        $list->setDashboardPageParameters($this->getRequestActionParameters());
        if (!$category->getSetManager()->allowAttributeSets()) {
            $list->setEnableSorting(false);
        }

        $this->set('headerMenu', $this->getHeaderMenu($entity));

        $this->set('attributeView', $list);
    }

    protected function getHeaderMenu(CategoryObjectInterface $category)
    {
        return new StandardListHeader($category);
    }

    public function renderAdd($type, $backURL)
    {
        $add = new Form($type);
        $add->setBackButtonURL($backURL);
        $add->setCategory($this->getCategoryObject());
        $add->setDashboardPageParameters($this->getRequestActionParameters());
        $this->set('attributeView', $add);
        $this->set('pageTitle', t('Add Attribute'));
    }

    public function renderEdit($key, $backURL)
    {
        $edit = new EditKey($key);
        $edit->setBackButtonURL($backURL);
        $edit->setCategory($this->getCategoryObject());
        $edit->setDashboardPageParameters($this->getRequestActionParameters());
        $this->set('attributeView', $edit);
        $this->set('pageTitle', t('Edit Attribute'));

        $header = new KeyHeader($key);
        $header->setDashboardPageParameters($this->getRequestActionParameters());
        $this->set('headerMenu', $header);
    }

    protected function executeAdd(Type $type, $successURL, $onComplete = null)
    {
        $entity = $this->getCategoryObject();
        $category = $entity->getAttributeKeyCategory();
        $validator = $type->getController()->getValidator();
        $response = $validator->validateAddKeyRequest($category, $type, $this->request);
        if (!$response->isValid()) {
            $this->error = $response->getErrorObject();
        } else {
            /*
             * @var $category \Concrete\Core\Attribute\Category\CategoryInterface
             */
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
        $entity = $this->getCategoryObject();
        $category = $entity->getAttributeKeyCategory();
        if ($category->getSetManager()->allowAttributeSets()) {
            $set = Set::getByID($request->request->get('asID'));
            $setKeys = Set::getByAttributeKey($key);
            if (in_array($set, $setKeys)) {
                return;
            }

            if ($category->getSetManager()->allowAttributeSets() == StandardSetManager::ASET_ALLOW_SINGLE || !is_object($set)) {
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
                foreach ($set->getAttributeKeyCollection() as $setKey) {
                    $setKey->setDisplayOrder($displayOrder);
                    $this->entityManager->persist($setKey);
                    ++$displayOrder;
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
        $entity = $this->getCategoryObject();
        $category = $entity->getAttributeKeyCategory();
        $validator = $key->getController()->getValidator();
        $response = $validator->validateUpdateKeyRequest($category, $key, $this->request);
        if (!$response->isValid()) {
            $this->error = $response->getErrorObject();
        } else {
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
        $entity = $this->getCategoryObject();
        try {
            if (!$this->token->validate('delete_attribute')) {
                throw new \Exception($this->token->getErrorMessage());
            }

            $this->entityManager->remove($key);
            $this->entityManager->flush();

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
        $entity = $this->getCategoryObject();
        $category = $entity->getAttributeKeyCategory();
        if ($category->getSetManager()->allowAttributeSets()) {
            /*
             * @var CategoryInterface
             */
            $keys = array();
            foreach ((array) $this->request->request->get('akID') as $akID) {
                /*
                 * @var AttributeInterface
                 */
                $key = $category->getAttributeKeyByID($akID);
                if (is_object($key)) {
                    $keys[] = $key;
                }
            }

            foreach ($category->getSetManager()->getAttributeSets() as $set) {
                if ($set->getAttributeSetID() == $this->request->request->get('asID') && count($keys)) {

                    // Clear the keys
                    foreach ($set->getAttributeKeyCollection() as $setKey) {
                        $this->entityManager->remove($setKey);
                    }
                    $this->entityManager->flush();

                    $i = 0;
                    foreach ($keys as $key) {
                        $setKey = new SetKey();
                        $setKey->setAttributeKey($key);
                        $setKey->setAttributeSet($set);
                        $setKey->setDisplayOrder($i);
                        $set->getAttributeKeyCollection()->add($setKey);
                        ++$i;
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
