<?php
namespace Concrete\Core\Express\ObjectBuilder;

use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Express\ObjectBuilder;

class FormBuilder
{

    protected $formName;
    protected $fieldsets = [];
    protected $objectBuilder;

    public function __construct(ObjectBuilder $objectBuilder, $formName)
    {
        $this->formName = $formName;
        $this->objectBuilder = $objectBuilder;
    }


    public function addFieldSet($fieldsetName)
    {
        $fieldset = new FieldsetBuilder($fieldsetName);
        $this->fieldsets[] = $fieldset;
        return $fieldset;
    }

    public function save()
    {
        $entity = $this->objectBuilder->getEntity();
        $form = new Form();
        $form->setName($this->formName);
        $form->setEntity($entity);

        /**
         * @var $fieldset FieldsetBuilder
         */
        $position = 0;
        foreach($this->fieldsets as $fieldset) {
            $builtSet = $fieldset->build($this->objectBuilder);
            $builtSet->setPosition($position);
            $builtSet->setForm($form);
            $form->getFieldSets()->add($builtSet);
            $position++;
        }

        $em = $this->objectBuilder->getEntityManager();
        $em->persist($form);

        // Set this as the default view and edit form for the entity if one isn't already set
        $defaultViewForm = $entity->getDefaultViewForm();
        if (!$defaultViewForm) {
            $entity->setDefaultViewForm($form);
        }
        $defaultEditForm = $entity->getDefaultEditForm();
        if (!$defaultEditForm) {
            $entity->setDefaultEditForm($form);
        }
        $em->persist($entity);
        $em->flush();


        return $form;
    }

}
