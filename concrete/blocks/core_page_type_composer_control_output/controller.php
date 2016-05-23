<?php
namespace Concrete\Block\CorePageTypeComposerControlOutput;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Page\Type\Composer\FormLayoutSetControl as PageTypeComposerFormLayoutSetControl;
use Concrete\Core\Page\Type\Composer\OutputControl as PageTypeComposerOutputControl;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Backup\ContentExporter;
use PageTemplate;

class Controller extends BlockController
{
    protected $btCacheBlockRecord = true;
    protected $btTable = 'btCorePageTypeComposerControlOutput';
    protected $btIsInternal = true;

    public function getBlockTypeDescription()
    {
        return t("Proxy block for blocks that need to be output through composer.");
    }

    public function getBlockTypeName()
    {
        return t("Composer Control");
    }

    public function getComposerOutputControlObject()
    {
        $outputControl = PageTypeComposerOutputControl::getByID($this->ptComposerOutputControlID);

        return $outputControl;
    }

    public function export(\SimpleXMLElement $blockNode)
    {
        $outputControl = PageTypeComposerOutputControl::getByID($this->ptComposerOutputControlID);
        if (is_object($outputControl)) {
            $fsc = PageTypeComposerFormLayoutSetControl::getByID($outputControl->getPageTypeComposerFormLayoutSetControlID());
            if (is_object($fsc)) {
                $cnode = $blockNode->addChild('control');
                $cnode->addAttribute('output-control-id',
                    ContentExporter::getPageTypeComposerOutputControlTemporaryID($fsc));
            }
        }
    }

    public function getImportData($blockNode, $page)
    {
        $args = array();
        $formLayoutSetControlID = ContentImporter::getPageTypeComposerFormLayoutSetControlFromTemporaryID((string) $blockNode->control['output-control-id']);
        $formLayoutSetControl = PageTypeComposerFormLayoutSetControl::getByID($formLayoutSetControlID);
        $pt = PageTemplate::getByID($page->getPageTemplateID());
        $outputControl = PageTypeComposerOutputControl::getByPageTypeComposerFormLayoutSetControl($pt,
            $formLayoutSetControl);
        $args['ptComposerOutputControlID'] = $outputControl->getPageTypeComposerOutputControlID();

        return $args;
    }
}
