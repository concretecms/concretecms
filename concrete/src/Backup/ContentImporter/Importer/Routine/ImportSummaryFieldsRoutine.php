<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Entity\Summary\Field;

class ImportSummaryFieldsRoutine extends AbstractRoutine

{
    public function getHandle()
    {
        return 'summary_fields';
    }

    public function import(\SimpleXMLElement $sx)
    {
        $em = \Database::connection()->getEntityManager();
        if (isset($sx->summaryfields)) {
            foreach ($sx->summaryfields->field as $pt) {
                $pkg = static::getPackageObject($pt['package']);
                $name = (string) $pt['name'];
                $handle = (string) $pt['handle'];
                $field = $em->getRepository(Field::class)->findOneByHandle($handle);
                if (!$field) {
                    $template = new Field();
                    $template->setHandle($handle);
                    $template->setName($name);
                    $template->setPackage($pkg);
                    $em->persist($template);
                }
            }
        }
        $em->flush();
    }
}
