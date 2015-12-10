<?
namespace Concrete\Controller\SinglePage\Dashboard\Express\Entities;

use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Entity\Express\Form;
use \Concrete\Core\Page\Controller\DashboardPageController;

class Forms extends DashboardPageController
{

    protected $repository;
    protected $formRepository;

    public function on_start()
    {
        parent::on_start();
        $this->repository = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        $this->formRepository = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Form');
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
                $this->redirect('/dashboard/express/entities/forms', 'view_form_details', $form->getId());

            } else {
                $this->add($entity->getId());
            }

        } else {
            $this->redirect('/dashboard/express/entities');
        }
    }
    public function add($id = null)
    {
        $entity = $this->repository->findOneById($id);
        if (is_object($entity)) {
            $this->set('entity', $entity);
            $this->set('pageTitle', t('Add Form'));
            $this->set('name', '');
            $this->render('/dashboard/express/entities/forms/details');
        } else {
            $this->redirect('/dashboard/express/entities');
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
            $this->redirect('/dashboard/express/entities');
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
        if (!$this->error->has()) {
            $this->entityManager->remove($form);
            $this->entityManager->flush();
            $this->flash('success', t('Form deleted successfully.'));
            $this->redirect('/dashboard/express/entities/forms', $id);
        } else {
            $this->view_form_details($this->request->request->get('form_id'));
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
            $set = new FieldSet();
            $set->setTitle($this->request->request->get('name'));
            $set->setForm($form);
            $this->entityManager->persist($set);
            $this->entityManager->flush();
            $this->flash('success', t('Form field set added successfully.'));
            $this->redirect('/dashboard/express/entities/forms', 'view_form_details', $form->getId());
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
            $this->render('/dashboard/express/entities/forms/details');
        } else {
            $this->redirect('/dashboard/express/entities');
        }
    }


    public function view_form_details($id = null)
    {
        $form = $this->formRepository->findOneById($id);
        if (is_object($form)) {
            $entity = $form->getEntity();
            $this->set('entity', $entity);
            $this->set('fieldSets', $form->getFieldSets());
            $this->set('expressForm', $form);
            $this->set('pageTitle', t('Form Details'));
            $this->render('/dashboard/express/entities/forms/view');
        } else {
            $this->redirect('/dashboard/express/entities');
        }
    }




}
