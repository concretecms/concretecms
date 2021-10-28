<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;


use Concrete\Core\Entity\Board\SlotTemplate;
use Concrete\Core\Entity\Design\DesignTag;

class ImportBoardSlotTemplatesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'board_slot_templates';
    }

    public function import(\SimpleXMLElement $sx)
    {
        $em = \Database::connection()->getEntityManager();
        $tagRepository = $em->getRepository(DesignTag::class);
        if (isset($sx->boardslottemplates)) {
            foreach ($sx->boardslottemplates->template as $bt) {
                $pkg = static::getPackageObject($bt['package']);
                $name = (string) $bt['name'];
                $icon = (string) $bt['icon'];
                $handle = (string) $bt['handle'];
                $template = $em->getRepository(SlotTemplate::class)->findOneByHandle($handle);
                if (!$template) {
                    $template = new SlotTemplate();
                    $template->setIcon($icon);
                    $template->setHandle($handle);
                    $template->setName($name);
                    $template->setPackage($pkg);

                    if (isset($bt->tags)) {
                        foreach ($bt->tags->children() as $templateTag) {
                            $templateTagValue = (string) $templateTag['value'];
                            if ($templateTagValue !== null) {
                                $tag = $tagRepository->findOneByValue($templateTagValue);
                                if ($tag) {
                                    $template->getTags()->add($tag);
                                }
                            }
                        }
                    }

                    $em->persist($template);
                }
            }
        }
        $em->flush();
    }
}
