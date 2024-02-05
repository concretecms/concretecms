<?php

namespace Concrete\Tests\Summary\Template;

use Concrete\Core\Entity\Summary\Category;
use Concrete\Core\Entity\Summary\Field;
use Concrete\Core\Entity\Summary\Template;
use Concrete\Core\Entity\Summary\TemplateField;
use Concrete\Core\Entity\Summary\TemplateRepository;
use Concrete\Core\Summary\Data\Collection;
use Concrete\Core\Summary\Template\Filterer;
use Concrete\Tests\TestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;
use PHPUnit\Framework\Attributes\DataProvider;

class FiltererTest extends TestCase
{
    
    public static function templatesProvider()
    {
        return [
            [
                [
                    ['default', 'Default Summary Template', 'blank.png', [
                        'title' => true,
                        'link' => true,
                        'description' => false
                    ]],
                ],
                [
                    'title',
                    'link',
                ],
                [
                    'default',
                ]
            ]
        ];
    }

    #[DataProvider('templatesProvider')]
    public function testGetTemplates($templateData, $collectionFields, $expectedFilteredTemplateHandles)
    {
        $entityManager = M::mock(EntityManager::class);
        $collection = M::mock(Collection::class);
        $category = M::mock(Category::class);
        $categoryRepository = M::mock(EntityRepository::class);
        $templateRepository = M::mock(TemplateRepository::class);
        
        $templates = [];
        foreach($templateData as $templateRecord) {
            $template = M::mock(Template::class);
            $template->shouldReceive('getHandle')->andReturn($templateRecord[0]);
            $template->shouldReceive('getName')->andReturn($templateRecord[1]);
            $template->shouldReceive('getIcon')->andReturn($templateRecord[2]);
            $fieldRecords = $templateRecord[3];
            $fields = [];
            foreach($fieldRecords as $fieldIdentifier => $required) {
                $field = M::mock(Field::class);
                $field->shouldReceive('getFieldIdentifier')->andReturn($fieldIdentifier);
                $templateField = M::mock(TemplateField::class);
                $templateField->shouldReceive('getField')->andReturn($field);
                $templateField->shouldReceive('isRequired')->once()->andReturn($required);
                
                $containsField = in_array($fieldIdentifier, $collectionFields);
                if ($required) {
                    $collection->shouldReceive('containsField')->with($field)->once()->andReturn($containsField);
                }
                $fields[] = $templateField;
            }
            $template->shouldReceive('getFields')->andReturn($fields);
            $templates[] = $template;
        }

        $categoryRepository->shouldReceive('findOneByHandle')->with('page')->once()->andReturn($category);
        $templateRepository->shouldReceive('findByCategory')->with($category)->once()->andReturn($templates);
        
        $entityManager->shouldReceive('getRepository')->with(Category::class)->andReturn($categoryRepository);
        $entityManager->shouldReceive('getRepository')->with(Template::class)->andReturn($templateRepository);
        
        $filterer = new Filterer($entityManager);
        $templates = $filterer->getTemplates('page', $collection);
        $this->assertCount(count($expectedFilteredTemplateHandles), $templates);
        foreach($templates as $template) {
            $this->assertInstanceOf(Template::class, $template);
            $this->assertTrue(in_array($template->getHandle(), $expectedFilteredTemplateHandles));
        }
    }
    
    
    
}
