<?php

namespace Concrete\Core\Form\Type;

use Concrete\Core\File\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileSelectorType extends AbstractType implements DataTransformerInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer($this);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'filters' => [],
            'choose_label' => null,
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $filters = json_encode($options['filters'] ?: []);

        $fileId = $form->getData() ? $form->getData()->getFileID() : 0;
        $view->vars['filters'] = $filters;
        $view->vars['file_id'] = $fileId;
        $view->vars['choose_file'] = $options['choose_label'] ? $options['choose_label'] : t('Choose File');
        $view->vars['input_name'] = $view->vars['full_name'];
    }

    public function transform($value)
    {
        return File::getByID($value);
    }

    public function reverseTransform($value)
    {
        return File::getByID($value);
    }

    public function getBlockPrefix(): string
    {
        return 'file_selector';
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}
