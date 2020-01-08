<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Entity\Board\Template;

class ImportBoardTemplatesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'board_templates';
    }

    public function import(\SimpleXMLElement $sx)
    {
        $em = \Database::connection()->getEntityManager();
        if (isset($sx->boardtemplates)) {
            foreach ($sx->boardtemplates->template as $bt) {
                $pkg = static::getPackageObject($bt['package']);
                $name = (string) $bt['name'];
                $icon = (string) $bt['icon'];
                $handle = (string) $bt['handle'];
                $template = $em->getRepository(Template::class)->findOneByHandle($handle);
                if (!$template) {
                    $template = new Template();
                    $template->setIcon($icon);
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
