<?php

namespace Concrete\Core\Board\DataSource\Populator;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\Configuration\Configuration;
use Concrete\Core\Summary\Template\RendererFilterer;

defined('C5_EXECUTE') or die("Access Denied.");

abstract class AbstractPopulator implements PopulatorInterface
{

    protected $rendererFilter;

    public function __construct(RendererFilterer $rendererFilterer)
    {
        $this->rendererFilter = $rendererFilterer;
    }

    public function createBoardItemBlock($mixed): Block
    {
        // Retrieve a summary object from the content object.

        $type = BlockType::getByHandle(BLOCK_HANDLE_SUMMARY_PROXY);
        $template = $this->rendererFilter->getRandomTemplate($mixed);
        if ($template) {
            $data = $template->getData();
            return $type->add([
                'data' => json_encode($data),
                'templateID' => $template->getTemplate()->getId()
            ]);
        }
    }




}
