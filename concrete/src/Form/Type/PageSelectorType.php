<?php

namespace Concrete\Core\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageSelectorType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choose_label' => null,
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $fileId = $form->getData() ? $form->getData() : 0;
        $view->vars['page_id'] = $fileId;
        $view->vars['choose_file'] = $options['choose_label'] ? $options['choose_label'] : t('Choose Page');
        $view->vars['input_name'] = $view->vars['full_name'];
    }

    public function getBlockPrefix(): string
    {
        return 'page_selector';
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}
