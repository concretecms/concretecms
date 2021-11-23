<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Entity\Design\DesignTag;
use Concrete\Core\Entity\Page\Container;
use Concrete\Core\Entity\Summary\Category;
use Concrete\Core\Entity\Summary\Field;
use Concrete\Core\Entity\Summary\Template;
use Concrete\Core\Entity\Summary\TemplateField;

class ImportSummaryTemplatesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'summary_templates';
    }

    public function import(\SimpleXMLElement $sx)
    {
        $em = \Database::connection()->getEntityManager();
        $categoryRepository = $em->getRepository(Category::class);
        $fieldRepository = $em->getRepository(Field::class);
        $tagRepository = $em->getRepository(DesignTag::class);
        if (isset($sx->summarytemplates)) {
            foreach ($sx->summarytemplates->template as $pt) {
                $pkg = static::getPackageObject($pt['package']);
                $name = (string) $pt['name'];
                $icon = (string) $pt['icon'];
                $handle = (string) $pt['handle'];
                $template = $em->getRepository(Template::class)->findOneByHandle($handle);
                if (!$template) {
                    $template = new Template();
                    $template->setIcon($icon);
                    $template->setHandle($handle);
                    $template->setName($name);
                    $template->setPackage($pkg);
                    $em->persist($template);

                    if (isset($pt->categories)) {
                        foreach ($pt->categories->children() as $summaryCategory) {
                            $categoryHandle = (string)$summaryCategory['handle'];
                            if ($categoryHandle !== null) {
                                $category = $categoryRepository->findOneByHandle($categoryHandle);
                                if ($category) {
                                    $template->getCategories()->add($category);
                                }
                            }
                            $em->persist($template);
                        }
                    }

                    if (isset($pt->tags)) {
                        foreach ($pt->tags->children() as $summaryTag) {
                            $summaryTagValue = (string) $summaryTag['value'];
                            if ($summaryTagValue !== null) {
                                $tag = $tagRepository->findOneByValue($summaryTagValue);
                                if ($tag) {
                                    $template->getTags()->add($tag);
                                }
                            }
                            $em->persist($template);
                        }
                    }


                    if (isset($pt->fields)) {
                        foreach ($pt->fields->children() as $summaryTemplateField) {
                            $fieldHandle = (string) $summaryTemplateField;
                            if ($fieldHandle !== null) {
                                $required = false;
                                $requiredNode = (string) $summaryTemplateField['required'];
                                if ($requiredNode === '1') {
                                    $required = true;
                                }
                                $field = $fieldRepository->findOneByHandle($fieldHandle);
                                if ($field) {
                                    $templateField = new TemplateField();
                                    $templateField->setTemplate($template);
                                    $templateField->setField($field);
                                    $templateField->setIsRequired($required);                                    
                                    $em->persist($templateField);
                                }
                            }
                            $em->persist($template);
                        }
                    }

                }
            }
        }
        $em->flush();
    }
}
