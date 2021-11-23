<?php
namespace Concrete\Core\Export\Item;

use Concrete\Core\Entity\Attribute\Set;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Export\Item\ItemInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class Board implements ItemInterface
{

    /**
     * @param $set \Concrete\Core\Entity\Board\Board
     * @param \SimpleXMLElement $xml
     * @return \SimpleXMLElement
     */
    public function export($board, \SimpleXMLElement $xml)
    {
        $node = $xml->addChild('board');
        $node->addAttribute('name', $board->getBoardName());
        $node->addAttribute('template', $board->getTemplate()->getHandle());
        $node->addAttribute('package', $board->getPackageHandle());
        $node->addAttribute('uses-custom-template-subset', $board->hasCustomSlotTemplates() ? 1 : '');
        $node->addAttribute('sort', $board->getSortBy());

        $dataSources = $board->getDataSources();
        $dataSourcesNode = $node->addChild('datasources');
        foreach ($dataSources as $dataSource) {
            /**
             * @var $dataSource ConfiguredDataSource
             */
            $dataSourceNode = $dataSourcesNode->addChild('datasource');
            $dataSourceNode->addAttribute('source', $dataSource->getDataSource()->getHandle());
            $dataSourceNode->addAttribute('weight', $dataSource->getCustomWeight());
            $dataSourceNode->addAttribute('name', $dataSource->getName());
            $dataSourceNode->addAttribute('day-interval-future', $dataSource->getPopulationDayIntervalFuture());
            $dataSourceNode->addAttribute('day-interval-past', $dataSource->getPopulationDayIntervalPast());

            $configuration = $dataSource->getConfiguration();
            $configurationNode = $dataSourceNode->addChild('configuration');
            $configuration->export($configurationNode);
        }

        if ($board->hasCustomSlotTemplates()) {
            $templates = $board->getCustomSlotTemplates();
            $templatesNode = $node->addChild('templates');
            foreach ($templates as $template) {
                $templateNode = $templatesNode->addChild('template');
                $templateNode->addAttribute('handle', $template->getHandle());
            }
        }
        
        



        return $node;
    }

}