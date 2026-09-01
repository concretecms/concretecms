<?php

namespace Concrete\Block\CorePageTypeComposerControlOutput;

use Concrete\Core\Api\ApiResourceValueInterface;
use Concrete\Core\Api\ApiValueSchemaInterface;
use Concrete\Core\Backup\ContentExporter;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\Traits\CustomApiValueTrait;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Type\Composer\FormLayoutSetControl as PageTypeComposerFormLayoutSetControl;
use Concrete\Core\Page\Type\Composer\OutputControl as PageTypeComposerOutputControl;

class Controller extends BlockController implements ApiResourceValueInterface, ApiValueSchemaInterface
{
    use CustomApiValueTrait;

    /**
     * @var string
     */
    protected $btTable = 'btCorePageTypeComposerControlOutput';

    /**
     * @var bool
     */
    protected $btIsInternal = true;

    /**
     * @var int|null
     */
    protected $ptComposerOutputControlID;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Proxy block for blocks that need to be output through composer.');
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Composer Control');
    }

    /**
     * @return PageTypeComposerOutputControl|null
     */
    public function getComposerOutputControlObject()
    {
        return PageTypeComposerOutputControl::getByID($this->ptComposerOutputControlID);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Api\ApiValueSchemaInterface::getApiValueSchema()
     */
    public function getApiValueSchema(): array
    {
        return [
            'type' => 'object',
            'description' => 'The control of the composer of a page type whose contents this block displays (blocks of this kind are created by the page types themselves).',
            'properties' => [
                'ptComposerOutputControlID' => [
                    'type' => ['string', 'integer'],
                    'description' => 'The ID of the output control of the composer of the page type this block belongs to (0 if the block displays nothing).',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getImportDataFromApiValue()
     */
    public function getImportDataFromApiValue($page, array $value): array
    {
        $outputControlID = array_key_exists('ptComposerOutputControlID', $value) ? $value['ptComposerOutputControlID'] : $this->ptComposerOutputControlID;

        return [
            'ptComposerOutputControlID' => is_numeric($outputControlID) ? (int) $outputControlID : 0,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\Traits\CustomApiValueTrait::serializeValueForApi()
     */
    protected function serializeValueForApi(): array
    {
        return [
            'ptComposerOutputControlID' => (int) $this->ptComposerOutputControlID,
        ];
    }

    /**
     * @param \SimpleXMLElement $blockNode
     *
     * @return void
     */
    public function export(\SimpleXMLElement $blockNode)
    {
        $outputControl = PageTypeComposerOutputControl::getByID($this->ptComposerOutputControlID);
        if (is_object($outputControl)) {
            $fsc = PageTypeComposerFormLayoutSetControl::getByID($outputControl->getPageTypeComposerFormLayoutSetControlID());
            if (is_object($fsc)) {
                $cnode = $blockNode->addChild('control');
                $cnode->addAttribute(
                    'output-control-id',
                    ContentExporter::getPageTypeComposerOutputControlTemporaryID($fsc)
                );
            }
        }
    }

    /**
     * @param \SimpleXMLElement $blockNode
     * @param Page $page
     *
     * @return array<string,mixed>
     */
    public function getImportData($blockNode, $page)
    {
        $args = [];
        $formLayoutSetControlID = ContentImporter::getPageTypeComposerFormLayoutSetControlFromTemporaryID((string) $blockNode->control['output-control-id']);
        $formLayoutSetControl = PageTypeComposerFormLayoutSetControl::getByID($formLayoutSetControlID);
        $pt = Template::getByID($page->getPageTemplateID());
        $outputControl = PageTypeComposerOutputControl::getByPageTypeComposerFormLayoutSetControl(
            $pt,
            $formLayoutSetControl
        );
        $args['ptComposerOutputControlID'] = $outputControl->getPageTypeComposerOutputControlID();

        return $args;
    }
}
