<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Entity\Summary\Category;

class ImportSummaryCategoriesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'summary_categories';
    }

    public function import(\SimpleXMLElement $sx)
    {
        $em = \Database::connection()->getEntityManager();
        if (isset($sx->summarycategories)) {
            foreach ($sx->summarycategories->category as $cat) {
                $pkg = static::getPackageObject($cat['package']);
                $name = (string) $cat['name'];
                $handle = (string) $cat['handle'];
                $category = $em->getRepository(Category::class)->findOneByHandle($handle);
                if (!$category) {
                    $category = new Category();
                    $category->setHandle($handle);
                    $category->setName($name);
                    $category->setPackage($pkg);
                    $em->persist($category);
                }
            }
        }
        $em->flush();
    }
}
