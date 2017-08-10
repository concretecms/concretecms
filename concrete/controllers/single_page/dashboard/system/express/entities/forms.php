<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Express\Entities;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Page\Controller\DashboardPageController;
use Doctrine\ORM\Id\UuidGenerator;

class Forms extends DashboardPageController
{
    protected $repository;
    protected $formRepository;
    protected $fieldSetRepository;
    protected $controlRepository;

    public function on_start()
    {
        parent::on_start();
        $this->repository = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        $this->formRepository = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Form');
        $this->fieldSetRepository = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\FieldSet');
        $this->controlRepository = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Control\Control');
    }

    public function save()
    {
        $entity = $this->repository->findOneById($this->request->request->get('entity_id'));
        if (is_object($entity)) {
            if (!$this->token->validate()) {
                $this->error->add($this->token->getErrorMessage());
            }

            if (!$this->error->has()) {
                $form = false;
                if ($this->request->request->has('form_id')) {
                    $form = $this->formRepository->findOneById($this->request->request->get('form_id'));
                }
                if (!$form) {
                    $form = new Form();
                }
                $form->setName($this->request->request->get('name'));
                $form->setEntity($entity);
                $this->entityManager->persist($form);
                $this->entityManager->flush($form);

                if ($this->request->request->has('form_id')) {
                    $this->flash('success', t('Form updated successfully.'));
                } else {
                    $this->flash('success', t('Form added successfully.'));
                }
                $this->redirect('/dashboard/system/express/entities/forms', 'view_form_details', $form->getId());
            } else {
                $this->add($entity->getId());
            }
        } else {
            $this->redirect('/dashboard/system/express/entities');
        }
    }
    public function add($id = null)
    {
        $entity = $this->repository->findOneById($id);
        if (is_object($entity)) {
            $this->set('entity', $entity);
            $this->set('pageTitle', t('Add Form'));
            $this->set('name', '');
            $this->render('/dashboard/system/express/entities/forms/details');
        } else {
            $this->redirect('/dashboard/system/express/entities');
        }
    }
    public function view($id = null)
    {
        $entity = $this->repository->findOneById($id);
        if (is_object($entity)) {
            $this->set('entity', $entity);
            $this->set('forms', $entity->getForms());
            $this->set('pageTitle', t('Forms'));
        } else {
            $this->redirect('/dashboard/system/express/entities');
        }
    }

    public function delete_form($id = null)
    {
        $this->view($id);
        $form = $this->formRepository->findOneById($this->request->request->get('form_id'));
        if (!is_object($form)) {
            $this->error->add(t('Invalid form object.'));
        }
        if (!$this->token->validate('delete_form')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->canDeleteForm($form->getEntity(), $form)) {
            $this->error->add(t('This form is used as the default view or edit form for its entity. It may not be deleted until another form is selected.'));
        }
        if (!$this->error->has()) {
            $this->entityManager->remove($form);
            $this->entityManager->flush();
            $this->flash('success', t('Form deleted successfully.'));
            $this->redirect('/dashboard/system/express/entities/forms', $id);
        } else {
            $this->view_form_details($this->request->request->get('form_id'));
        }
    }

    public function delete_set($id = null)
    {
        $set = $this->fieldSetRepository->findOneById($this->request->request->get('field_set_id'));
        if (!is_object($set)) {
            $this->error->add(t('Invalid field set object.'));
        }
        if (!$this->token->validate('delete_set')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $this->entityManager->remove($set);
            $this->entityManager->flush();
            $this->flash('success', t('Field set deleted successfully.'));
            $this->redirect('/dashboard/system/express/entities/forms', 'view_form_details', $id);
        } else {
            $this->view_form_details($id);
        }
    }

    public function delete_set_control($id = null)
    {
        $control = $this->controlRepository->findOneById($this->request->request->get('field_set_control_id'));
        if (!is_object($control)) {
            $this->error->add(t('Invalid field set control object.'));
        }
        if (!$this->token->validate('delete_set_control')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $this->entityManager->remove($control);
            $this->entityManager->flush();
            $this->flash('success', t('Field set control deleted successfully.'));
            $this->redirect('/dashboard/system/express/entities/forms', 'view_form_details', $id);
        } else {
            $this->view_form_details($id);
        }
    }

    public function edit_control($controlID = null)
    {
        $control = $this->controlRepository->findOneById($controlID);
        if ($control) {
            $manager = \Core::make('express/control/type/manager');
            $type = $manager->driver($control->getType());
            $this->set('type', $type);
            $this->set('control', $control);
            $this->setThemeViewTemplate('dialog.php');
            $this->render('/dashboard/system/express/entities/forms/edit_control');
        }
    }

    public function save_control($controlID = null)
    {
        $control = $this->controlRepository->findOneById($controlID);
        if ($control) {
            $saver = $control->getControlSaveHandler();
            $control = $saver->saveFromRequest($control, $this->request);
            $this->entityManager->persist($control);
            $this->entityManager->flush();
            $element = new \Concrete\Controller\Element\Dashboard\Express\Control($control);
            echo $element->render();
            exit;
        }
    }

    public function add_control($id = null)
    {
        $set = $this->fieldSetRepository->findOneById($id);
        $manager = \Core::make('express/control/type/manager');
        if ($this->request->isMethod("POST")) {
            if ($this->token->validate('add_control')) {
                $current = count($set->getControls());
                $position = 0;
                if ($current > 0) {
                    $position = $current;
                }

                $type = $manager->driver($this->request->request->get('type'));
                $control = $type->createControlByIdentifier($this->request->request->get('id'));
                $control->setId((new UuidGenerator())->generate($this->entityManager, $control));
                $control->setFieldSet($set);
                $control->setPosition($position);

                $this->entityManager->persist($control);
                $this->entityManager->flush();

                $element = new \Concrete\Controller\Element\Dashboard\Express\Control($control);
                echo $element->render();
                exit;
            }
        } else {
            $drivers = $manager->getDrivers();
            $tabs = array();
            foreach ($drivers as $type => $driver) {
                $active = false;
                if ($type == 'entity_property') {
                    $active = true;
                }
                $tabs[] = array($type, $driver->getPluralDisplayName(), $active);
            }
            $this->set('drivers', $drivers);
            $this->set('set', $set);
            $this->set('tabs', $tabs);
            $this->set('interface', \Core::make('helper/concrete/ui'));
            $this->setThemeViewTemplate('dialog.php');
            $this->render('/dashboard/system/express/entities/forms/add_control');
        }
    }

    public function update_set_display_order($id = null)
    {
        $form = $this->formRepository->findOneById($id);
        if (is_object($form)) {
            if ($this->token->validate('update_set_display_order', $this->request->request->get('token'))) {
                $position = 0;
                foreach ($this->post('set') as $setID) {
                    $set = $this->fieldSetRepository->findOneById($setID);
                    if (is_object($set)) {
                        $set->setPosition($position);
                    }
                    $this->entityManager->persist($set);
                    ++$position;
                }
                $this->entityManager->flush();
            }
        }
        exit;
    }

    public function update_set_control_display_order($id = null)
    {
        $form = $this->formRepository->findOneById($id);
        if (is_object($form)) {
            $set = $this->fieldSetRepository->findOneById($this->request->request->get('set'));
            if (is_object($set)) {
                if ($this->token->validate('update_set_control_display_order', $this->request->request->get('token'))) {
                    $position = 0;
                    foreach ($this->post('control') as $controlID) {
                        $control = $this->controlRepository->findOneById($controlID);
                        if (is_object($control)) {
                            $control->setPosition($position);
                            $this->entityManager->persist($control);
                        }
                        ++$position;
                    }
                    $this->entityManager->flush();
                }
            }
        }
        exit;
    }

    public function update_set($id = null)
    {
        $set = $this->fieldSetRepository->findOneById($this->request->request->get('field_set_id'));
        if (!is_object($set)) {
            $this->error->add(t('Invalid field set object.'));
        }
        if (!$this->token->validate('update_set')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $set->setTitle($this->request->request->get('name'));
            $this->entityManager->persist($set);
            $this->entityManager->flush();
            $this->flash('success', t('Field set updated successfully.'));
            $this->redirect('/dashboard/system/express/entities/forms', 'view_form_details', $id);
        } else {
            $this->view_form_details($id);
        }
    }

    public function add_set($id = null)
    {
        $form = $this->formRepository->findOneById($id);
        if (!is_object($form)) {
            $this->error->add(t('Invalid form object.'));
        }
        if (!$this->token->validate('add_set')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $current = count($form->getFieldSets());
            $position = 0;
            if ($current > 0) {
                $position = $current;
            }

            $set = new FieldSet();
            $set->setTitle($this->request->request->get('name'));
            $set->setForm($form);
            $set->setPosition($current);
            $this->entityManager->persist($set);
            $this->entityManager->flush();
            $this->flash('success', t('Form field set added successfully.'));
            $this->redirect('/dashboard/system/express/entities/forms', 'view_form_details', $form->getId());
        } else {
            $this->view_form_details($id);
        }
    }

    public function edit($id = null)
    {
        $form = $this->formRepository->findOneById($id);
        if (is_object($form)) {
            $entity = $form->getEntity();
            $this->set('entity', $entity);
            $this->set('expressForm', $form);
            $this->set('pageTitle', t('Edit Details'));
            $this->set('name', $form->getName());
            $this->render('/dashboard/system/express/entities/forms/details');
        } else {
            $this->redirect('/dashboard/system/express/entities');
        }
    }

    protected function canDeleteForm(Entity $entity, Form $form)
    {
        $canDeleteForm = true;
        if (
            ($entity->getDefaultEditForm() && $entity->getDefaultEditForm()->getID() == $form->getID()) ||
            ($entity->getDefaultViewForm() && $entity->getDefaultViewForm()->getID() == $form->getID())
        ) {
            $canDeleteForm = false;
        }
        return $canDeleteForm;
    }

    public function view_form_details($id = null)
    {
        $form = $this->formRepository->findOneById($id);
        if (is_object($form)) {
            /**
             * @var $entity Entity
             */
            $entity = $form->getEntity();
            $this->set('entity', $entity);
            $this->set('fieldSets', $form->getFieldSets());
            $this->set('expressForm', $form);
            $this->set('pageTitle', t('Form Details'));
            $this->set('canDeleteForm', $this->canDeleteForm($entity, $form));
            $this->render('/dashboard/system/express/entities/forms/view_form');
        } else {
            $this->redirect('/dashboard/system/express/entities');
        }
    }
}
