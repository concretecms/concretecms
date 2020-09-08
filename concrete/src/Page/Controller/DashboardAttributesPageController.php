<?php

namespace Concrete\Core\Page\Controller;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\CategoryObjectInterface;
use Concrete\Core\Attribute\Set;
use Concrete\Core\Attribute\StandardSetManager;
use Concrete\Core\Entity\Attribute\SetKey;
use Concrete\Core\Entity\Attribute\Type;
use Concrete\Core\Error\UserMessageException;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class DashboardAttributesPageController extends DashboardPageController
{
    /**
     * Configure the data for the view so that it can render the list of the attributes.
     */
    public function renderList()
    {
        $entity = $this->getCategoryObject();
        $category = $entity->getAttributeKeyCategory();
        $list = $this->elementManager->get('attribute/key_list');

        /**
         * @var \Concrete\Controller\Element\Attribute\KeyList $controller
         */
        $controller = $list->getElementController();
        $controller->setAttributeSets($category->getSetManager()->getAttributeSets());
        $controller->setUnassignedAttributeKeys($category->getSetManager()->getUnassignedAttributeKeys());
        $controller->setAttributeTypes($category->getAttributeTypes());
        $controller->setDashboardPagePath($this->getPageObject()->getCollectionPath());
        $controller->setDashboardPageParameters($this->getRequestActionParameters());
        if (!$category->getSetManager()->allowAttributeSets()) {
            $controller->setEnableSorting(false);
        }

        $this->set('headerMenu', $this->getHeaderMenu($entity));
        $this->set('attributeView', $list);
    }

    /**
     * Configure the data for the view so that it can render the "Add Attribute" page.
     *
     * @param \Concrete\Core\Entity\Attribute\Type $type The type of the new attribute
     * @param \League\Url\UrlInterface|string $backURL the URL to be used when users hit the "Cancel Add" button
     */
    public function renderAdd($type, $backURL)
    {
        $add = $this->elementManager->get('attribute/form', ['type' => $type]);

        /**
         * @var \Concrete\Controller\Element\Attribute\Form $controller
         */
        $controller = $add->getElementController();
        $controller->setBackButtonURL($backURL);
        $controller->setCategory($this->getCategoryObject());
        $controller->setDashboardPageParameters($this->getRequestActionParameters());

        $this->set('attributeView', $add);
        $this->set('pageTitle', t('Add Attribute'));
    }

    /**
     * Configure the data for the view so that it can render the "Edit Attribute" page.
     *
     * @param \Concrete\Core\Attribute\AttributeKeyInterface $key the key to be modified
     * @param \League\Url\UrlInterface|string $backURL the URL to be used when users hit the "Cancel Add" button
     */
    public function renderEdit($key, $backURL)
    {
        $edit = $this->elementManager->get('attribute/edit_key', ['key' => $key]);

        /**
         * @var \Concrete\Controller\Element\Attribute\EditKey $controller
         */
        $controller = $edit->getElementController();
        $controller->setBackButtonURL($backURL);
        $controller->setCategory($this->getCategoryObject());
        $controller->setDashboardPageParameters($this->getRequestActionParameters());
        $this->set('attributeView', $edit);
        $this->set('pageTitle', t('Edit Attribute'));

        $header = $this->elementManager->get('attribute/key_header', ['key' => $key]);
        /**
         * @var \Concrete\Controller\Element\Attribute\EditKey $headerController
         */
        $headerController = $header->getElementController();
        $headerController->setDashboardPageParameters($this->getRequestActionParameters());

        $this->set('headerMenu', $headerController);
    }

    /**
     * Sort the attributes belinging to a set, reading the data from the request.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function sort_attribute_set()
    {
        $entity = $this->getCategoryObject();
        $category = $entity->getAttributeKeyCategory();
        if ($category->getSetManager()->allowAttributeSets()) {
            $keys = [];
            foreach ((array) $this->request->request->get('akID') as $akID) {
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

    /**
     * Get the attribute category we are working on.
     *
     * @return \Concrete\Core\Attribute\CategoryObjectInterface
     */
    abstract protected function getCategoryObject();

    /**
     * Get the controller of the element to be placed in the header of the "Attribute List" page.
     *
     * @param \Concrete\Core\Attribute\CategoryObjectInterface $category
     *
     * @return \Concrete\Core\Controller\ElementController|null
     */
    protected function getHeaderMenu(CategoryObjectInterface $category)
    {
        return $this->elementManager->get('attribute/standard_list_header', ['category' => $category])->getElementController();
    }

    /**
     * Create a new attribute key for the specified type, reading the type-specific data from the current request.
     *
     * @param \Concrete\Core\Entity\Attribute\Type $type the type of the attribute to be created
     * @param \League\Url\UrlInterface|string $successURL where to redirect the users when the operation succeedes
     * @param callable|null $onComplete a callback function that's called right after the new attribute key is created
     */
    protected function executeAdd(Type $type, $successURL, $onComplete = null)
    {
        $entity = $this->getCategoryObject();
        $category = $entity->getAttributeKeyCategory();
        $validator = $type->getController()->getValidator();
        $response = $validator->validateAddKeyRequest($category, $type, $this->request);
        if (!$response->isValid()) {
            $this->error = $response->getErrorObject();
        } else {
            $key = $category->addFromRequest($type, $this->request);
            $this->assignToSetFromRequest($key);

            if ($onComplete instanceof \Closure) {
                $onComplete();
            }

            $this->flash('success', t('Attribute created successfully.'));

            $this->buildRedirect($successURL)->send();
            $this->app->shutdown();
        }
    }

    /**
     * Assign an attribute key to the set (which is read from the request).
     *
     * @param \Concrete\Core\Attribute\AttributeKeyInterface $key
     */
    protected function assignToSetFromRequest(AttributeKeyInterface $key)
    {
        $request = $this->request;
        $entity = $this->getCategoryObject();
        $category = $entity->getAttributeKeyCategory();
        if ($category->getSetManager()->allowAttributeSets()) {
            if ($request->request->has('asID')) {
                $set = Set::getByID($request->request->get('asID'));
                $setKeys = Set::getByAttributeKey($key);
                if (in_array($set, $setKeys)) {
                    // The set is already a part of this key, so we return.
                    return;
                }
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

    /**
     * Update an existing attribute key, reading the type-specific data from the current request.
     *
     * @param \Concrete\Core\Attribute\AttributeKeyInterface $key the attribute key to be updated
     * @param \League\Url\UrlInterface|string $successURL where to redirect the users when the operation succeedes
     * @param callable|null $onComplete a callback function that's called right after the attribute key is updated
     */
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

            $this->buildRedirect($successURL)->send();
            $this->app->shutdown();
        }
    }

    /**
     * Delete an existing attribute key.
     *
     * @param \Concrete\Core\Attribute\AttributeKeyInterface $key the attribute key to be deleted
     * @param \League\Url\UrlInterface|string $successURL where to redirect the users when the operation succeedes
     * @param callable|null $onComplete a callback function that's called right after the attribute key is deleted
     */
    protected function executeDelete(AttributeKeyInterface $key, $successURL, $onComplete = null)
    {
        try {
            if (!$this->token->validate('delete_attribute')) {
                throw new UserMessageException($this->token->getErrorMessage());
            }

            $this->entityManager->remove($key);
            $this->entityManager->flush();

            if ($onComplete instanceof \Closure) {
                $onComplete();
            }

            $this->flash('success', t('Attribute deleted successfully.'));

            $this->buildRedirect($successURL)->send();
            $this->app->shutdown();
        } catch (UserMessageException $e) {
            $this->error = $e;
        }
    }
}
