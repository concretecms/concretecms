<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Entity\Design\DesignTag;

class ImportDesignTagsRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'design_tags';
    }

    public function import(\SimpleXMLElement $sx)
    {
        $em = \Database::connection()->getEntityManager();
        if (isset($sx->designtags)) {
            foreach ($sx->designtags->tag as $tag) {
                $value = (string) $tag['value'];
                $tagEntity = $em->getRepository(DesignTag::class)->findOneByValue($value);
                if (!$tagEntity) {
                    $tagEntity = new DesignTag();
                    $tagEntity->setValue($value);
                    $em->persist($tagEntity);
                }
            }
        }
        $em->flush();
    }
}
