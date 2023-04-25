<?php

namespace Concrete\Core\Form\Type;

use Concrete\Core\Editor\LinkAbstractor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class CkeditorFieldType extends AbstractType implements DataTransformerInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer($this);
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }

    public function transform($value)
    {
        return LinkAbstractor::translateFromEditMode($value);
    }

    public function reverseTransform($value)
    {
        return LinkAbstractor::translateTo($value);
    }

    public function getBlockPrefix(): string
    {
        return 'wysiwyg';
    }
}
