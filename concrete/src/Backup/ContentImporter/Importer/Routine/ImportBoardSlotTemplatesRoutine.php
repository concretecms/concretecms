<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;


use Concrete\Core\Entity\Board\SlotTemplate;

class ImportBoardSlotTemplatesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'board_slot_templates';
    }

    public function import(\SimpleXMLElement $sx)
    {
        $em = \Database::connection()->getEntityManager();
        if (isset($sx->boardslottemplates)) {
            foreach ($sx->boardslottemplates->template as $bt) {
                $pkg = static::getPackageObject($bt['package']);
                $name = (string) $bt['name'];
                $icon = (string) $bt['icon'];
                $formFactor = (string) $bt['form-factor'];
                $handle = (string) $bt['handle'];
                $template = $em->getRepository(SlotTemplate::class)->findOneByHandle($handle);
                if (!$template) {
                    $template = new SlotTemplate();
                    $template->setFormFactor($formFactor);
                    $template->setIcon($icon);
                    $template->setHandle($handle);
                    $template->setName($name);
                    $em->persist($template);
                }
            }
        }
        $em->flush();
    }
}
