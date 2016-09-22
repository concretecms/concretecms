<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Feature\Feature;
use Concrete\Core\Gathering\Item\Template\Template;
use Concrete\Core\Gathering\Item\Template\Type;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\BannedWord\BannedWord;

class ImportGatheringItemTemplatesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'gathering_item_templates';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->gatheringitemtemplates)) {
            foreach ($sx->gatheringitemtemplates->gatheringitemtemplate as $at) {
                $pkg = static::getPackageObject($at['package']);
                $type = Type::getByHandle((string) $at['type']);
                $gatHasCustomClass = false;
                $gatForceDefault = false;
                $gatFixedSlotWidth = 0;
                $gatFixedSlotHeight = 0;
                if ($at['has-custom-class']) {
                    $gatHasCustomClass = true;
                }
                if ($at['force-default']) {
                    $gatForceDefault = true;
                }
                if ($at['fixed-slot-width']) {
                    $gatFixedSlotWidth = (string) $at['fixed-slot-width'];
                }
                if ($at['fixed-slot-height']) {
                    $gatFixedSlotHeight = (string) $at['fixed-slot-height'];
                }
                $template = Template::add(
                    $type,
                    (string) $at['handle'],
                    (string) $at['name'],
                    $gatFixedSlotWidth,
                    $gatFixedSlotHeight,
                    $gatHasCustomClass,
                    $gatForceDefault,
                    $pkg
                );
                foreach ($at->children() as $fe) {
                    $feo = Feature::getByHandle((string) $fe['handle']);
                    if (is_object($feo)) {
                        $template->addGatheringItemTemplateFeature($feo);
                    }
                }
            }
        }    }

}
